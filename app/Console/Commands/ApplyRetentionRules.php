<?php

namespace App\Console\Commands;

use App\Models\Registre\AuditLog;
use App\Models\Registre\PresaisineSubmission;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * The retention rules of the module 4 checklist.
 *
 * Three of them are ours to apply without waiting for anything:
 *
 *   « Données de pré-saisine non acceptées : aucune consultation après
 *     six mois. »
 *   « Journaux d'accès : conserver pendant 12 mois. »
 *   « Pièces jointes en ligne : supprimer du serveur de réception. »
 *
 * The two that touch dossiers — five years for a closed case, one year
 * for a refused one — are not here: both count from a closure the
 * Clinic's thirteen statuses define, and those are still awaited. Doing
 * them from a guess would delete case files on a schedule nobody
 * approved.
 *
 * Nothing is deleted silently: the command reports what it removed, and
 * --dry-run shows what it would.
 */
class ApplyRetentionRules extends Command
{
    protected $signature = 'clinique:conservation
                            {--dry-run : Affiche ce qui serait supprimé, sans rien supprimer}';

    protected $description = "Applique les règles de conservation : pré-saisines non acceptées, journaux, brouillons";

    /** "aucune consultation après six mois" */
    private const SUBMISSION_MONTHS = 6;

    /** "conserver pendant 12 mois" */
    private const LOG_MONTHS = 12;

    /**
     * A draft folder belongs to someone who started the form and never
     * sent it. A week is well past any session.
     */
    private const DRAFT_DAYS = 7;

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $now = CarbonImmutable::now();

        if ($dry) {
            $this->warn('Simulation : rien ne sera supprimé.');
        }

        $this->line('');
        $submissions = $this->purgeSubmissions($now, $dry);
        $logs = $this->purgeLogs($now, $dry);
        $drafts = $this->purgeDrafts($now, $dry);
        $this->line('');

        $this->info(sprintf(
            '%s : %d pré-saisine(s), %d entrée(s) de journal, %d dossier(s) de brouillon.',
            $dry ? 'Seraient supprimés' : 'Supprimés',
            $submissions,
            $logs,
            $drafts
        ));

        return self::SUCCESS;
    }

    /**
     * A request the Clinic never accepted. Its attachments go with it —
     * they were never transferred to a dossier, so nothing else holds
     * them.
     */
    private function purgeSubmissions(CarbonImmutable $now, bool $dry): int
    {
        $cutoff = $now->subMonths(self::SUBMISSION_MONTHS);

        $query = PresaisineSubmission::query()
            ->whereIn('status', [PresaisineSubmission::STATUS_PENDING, PresaisineSubmission::STATUS_REFUSED])
            ->where('created_at', '<', $cutoff);

        $count = (clone $query)->count();
        $this->line(sprintf('Pré-saisines non acceptées avant le %s : %d', $cutoff->format('d/m/Y'), $count));

        if ($dry || $count === 0) {
            return $count;
        }

        $query->with('attachments')->chunkById(100, function ($submissions) {
            foreach ($submissions as $submission) {
                foreach ($submission->attachments as $attachment) {
                    Storage::disk('local')->delete($attachment->path);
                }

                // The attachments cascade with the row.
                $submission->delete();
            }
        });

        return $count;
    }

    private function purgeLogs(CarbonImmutable $now, bool $dry): int
    {
        $cutoff = $now->subMonths(self::LOG_MONTHS);

        $query = AuditLog::where('created_at', '<', $cutoff);
        $count = (clone $query)->count();

        $this->line(sprintf('Entrées de journal avant le %s : %d', $cutoff->format('d/m/Y'), $count));

        if (! $dry && $count > 0) {
            $query->delete();
        }

        return $count;
    }

    /**
     * Files uploaded on the form by someone who never reached the end of
     * it. They belong to no submission and nothing points at them.
     */
    private function purgeDrafts(CarbonImmutable $now, bool $dry): int
    {
        $disk = Storage::disk('local');
        $root = 'presaisine/brouillons';

        if (! $disk->exists($root)) {
            $this->line('Dossiers de brouillon : aucun.');

            return 0;
        }

        $cutoff = $now->subDays(self::DRAFT_DAYS)->getTimestamp();
        $stale = [];

        foreach ($disk->directories($root) as $directory) {
            $files = $disk->files($directory);

            // An empty folder is stale whatever its own timestamp says.
            $newest = 0;
            foreach ($files as $file) {
                $newest = max($newest, $disk->lastModified($file));
            }

            if ($newest < $cutoff) {
                $stale[] = $directory;
            }
        }

        $this->line(sprintf('Dossiers de brouillon abandonnés depuis %d jours : %d', self::DRAFT_DAYS, count($stale)));

        if (! $dry) {
            foreach ($stale as $directory) {
                $disk->deleteDirectory($directory);
            }
        }

        return count($stale);
    }
}
