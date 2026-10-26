<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Guide;
use App\Models\Report;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Fiches pratiques and rapports annuels, each with a real (tiny) PDF on the
 * private disk, so the download route can be exercised end to end during
 * review. The Clinic's own documents arrive in phase 4.
 */
class ResourceSeeder extends Seeder
{
    public function run(): void
    {
        $this->guides();
        $this->reports();
    }

    private function guides(): void
    {
        $domains = collect([
            ['name' => 'Droit de la famille', 'tone' => 'blue'],
            ['name' => 'Droit civil', 'tone' => 'green'],
            ['name' => 'Droit pénal', 'tone' => 'pink'],
            ['name' => 'Droits humains', 'tone' => 'purple'],
            ['name' => 'Droit administratif', 'tone' => 'green'],
            ['name' => 'Droit du travail', 'tone' => 'purple'],
            ['name' => 'Droit foncier', 'tone' => 'amber'],
            ['name' => 'Droit des successions', 'tone' => 'red'],
            ['name' => 'Vie pratique', 'tone' => 'blue'],
        ])->map(fn (array $c, int $i) => Category::updateOrCreate(
            ['type' => 'guide', 'slug' => Str::slug($c['name'])],
            ['name' => $c['name'], 'tone' => $c['tone'], 'position' => $i]
        ))->keyBy('name');

        $guides = [
            ['Guide pratique : droits de la famille', 'Droit de la famille', 'placeholders/guide-family', "Un guide pour comprendre les droits et obligations au sein de la famille (mariage, divorce, filiation, etc.).", 2450, '2025-06-02 09:00', 'published'],
            ["La procédure d'état civil au Niger", 'Droit civil', 'placeholders/guide-civil', "Les étapes pour obtenir un acte de naissance, de mariage ou de décès.", 1750, '2025-05-20 10:00', 'published'],
            ['Comprendre la garde à vue', 'Droit pénal', 'placeholders/guide-penal-v2', "Vos droits et les démarches à suivre en cas de garde à vue.", 1980, '2025-05-05 11:00', 'published'],
            ['Accès à la propriété foncière', 'Droit foncier', 'placeholders/guide-succession-event', "Les démarches administratives pour l'obtention d'un titre foncier.", 1280, '2025-04-18 09:30', 'published'],
            ['Le contrat de travail', 'Droit du travail', 'placeholders/guide-labor', "Tout ce qu'il faut savoir sur les types de contrats, les droits et obligations du travailleur.", 1420, '2025-04-02 14:00', 'published'],
            ['Protection des personnes vulnérables', 'Droits humains', 'placeholders/guide-human-rights', "Les droits des femmes, enfants, personnes handicapées et personnes déplacées.", 1120, '2025-03-15 10:30', 'published'],
            ['La succession : démarches et droits des héritiers', 'Droit des successions', 'placeholders/guide-succession', "Un guide pratique sur la transmission du patrimoine.", 870, '2025-02-28 09:00', 'published'],
            ['Recours contre une décision administrative', 'Droit administratif', 'placeholders/guide-penal', "Comment contester une décision de l'administration auprès des autorités compétentes.", 640, '2025-02-10 11:00', 'published'],
            ['Modèles de courriers juridiques', 'Vie pratique', 'placeholders/guide-practical', "Des exemples de courriers pour vos démarches administratives et judiciaires.", 520, '2025-01-22 10:00', 'published'],
            ['Les droits du locataire', 'Droit civil', 'placeholders/guide-civil', "Bail, loyer, préavis : ce que la loi prévoit pour le locataire et le bailleur.", 410, '2024-12-10 09:00', 'published'],
            ['La médiation familiale', 'Droit de la famille', 'placeholders/guide-family', "Résoudre un différend familial sans passer par le tribunal.", 0, null, 'draft'],
        ];

        foreach ($guides as [$title, $domain, $cover, $description, $downloads, $publishedAt, $status]) {
            $guide = Guide::updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'description' => $description,
                    'body' => "Contenu d'exemple de la fiche « {$title} ». Le contenu réel de la Clinique sera intégré ultérieurement.",
                    'cover' => $cover,
                    'category_id' => $domains[$domain]->id,
                    'status' => $status,
                    'published_at' => $publishedAt,
                ] + $this->samplePdf('fiches-pratiques', $title)
            );

            // downloads is a counter, not editor input, so it is not fillable.
            $guide->forceFill(['downloads' => $downloads])->save();
        }
    }

    private function reports(): void
    {
        $reports = [
            [2024, "Rapport annuel d'activité 2024", 'placeholders/report-cover-2026', "Un bilan complet de nos activités, des chiffres clés, des témoignages et des perspectives pour renforcer l'accès au droit.", 1250, '2025-01-20 10:00'],
            [2023, "Rapport annuel d'activité 2023", 'placeholders/report-cover-2023', "Nos réalisations, nos partenariats et l'impact de nos actions sur les communautés locales au Niger.", 980, '2024-01-18 10:00'],
            [2022, "Rapport annuel d'activité 2022", 'placeholders/report-cover-2022', "Une année d'engagement pour l'accès au droit et la protection des personnes vulnérables.", 760, '2023-01-16 10:00'],
            [2021, "Rapport annuel d'activité 2021", 'placeholders/report-cover-2021', "Retour sur nos principales actions, nos formations et nos caravanes juridiques.", 620, '2022-01-14 10:00'],
        ];

        foreach ($reports as [$year, $title, $cover, $description, $downloads, $publishedAt]) {
            $report = Report::updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'year' => $year,
                    'description' => $description,
                    'cover' => $cover,
                    'status' => 'published',
                    'published_at' => $publishedAt,
                ] + $this->samplePdf('rapports-annuels', $title)
            );

            $report->forceFill(['downloads' => $downloads])->save();
        }
    }

    /**
     * A minimal valid PDF written to the private disk, so review can press
     * Télécharger and actually receive a file. Written only once per
     * document: re-seeding must not pile up copies.
     *
     * @return array{file_path: string, file_name: string, file_size: int}
     */
    private function samplePdf(string $folder, string $title): array
    {
        $name = Str::slug($title).'.pdf';
        $path = "documents/{$folder}/{$name}";
        $disk = Storage::disk('local');

        if (! $disk->exists($path)) {
            $disk->put($path, $this->pdfBytes($title));
        }

        return [
            'file_path' => $path,
            'file_name' => $name,
            'file_size' => $disk->size($path),
        ];
    }

    /** A one-page PDF built by hand: no library, and no binary fixture in git. */
    private function pdfBytes(string $title): string
    {
        // Parentheses and backslashes delimit and escape a PDF string.
        $text = str_replace(['(', ')', '\\'], '', $title);

        $page = "BT /F1 15 Tf 60 760 Td ({$text}) Tj ET\n"
            ."BT /F1 11 Tf 60 730 Td (Document d'exemple - Clinique Juridique FSJP-UAM) Tj ET";

        $objects = [
            "1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n",
            "2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n",
            "3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 595 842]/Resources<</Font<</F1 5 0 R>>>>/Contents 4 0 R>>endobj\n",
            '4 0 obj<</Length '.strlen($page).">>stream\n{$page}\nendstream endobj\n",
            "5 0 obj<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>endobj\n",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [];

        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object;
        }

        $startxref = strlen($pdf);
        $size = count($objects) + 1;

        $pdf .= "xref\n0 {$size}\n0000000000 65535 f \n";

        foreach ($offsets as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        return $pdf."trailer<</Size {$size}/Root 1 0 R>>\nstartxref\n{$startxref}\n%%EOF";
    }
}
