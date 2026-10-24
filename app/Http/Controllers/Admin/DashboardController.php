<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityPhoto;
use App\Models\Article;
use App\Models\Call;
use App\Models\ContactMessage;
use App\Models\Guide;
use App\Models\NewsletterSubscriber;
use App\Models\Report;
use App\Models\Video;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'kpis' => [
                'articles' => Article::public()->count(),
                'videos' => Video::public()->count(),
                'messages' => ContactMessage::unhandled()->count(),
                'activities' => Activity::public()->count(),
            ],
            'months' => $this->publicationsByMonth(),
            'breakdown' => $this->contentBreakdown(),
            'latest' => Article::query()
                ->with('category')
                ->latest('created_at')
                ->take(5)
                ->get(),
            /*
             * The design's second panel was "Dernières candidatures".
             * Nothing records an application: a call tells the applicant
             * how to apply, and they do it by e-mail or in person — there
             * is no applications table and inventing one is Module 3's
             * business, not this screen's. The contact inbox is the real
             * stream of incoming requests, so it takes the slot.
             */
            'recentMessages' => ContactMessage::query()
                ->latest('created_at')
                ->take(5)
                ->get(),
            'subscribers' => NewsletterSubscriber::active()->count(),
        ]);
    }

    /**
     * Publications per month over the last six months, per content type.
     *
     * One grouped query per type rather than one per month: six months of
     * three types would otherwise be eighteen round trips for one panel.
     *
     * @return array{labels: array<int, string>, series: array<int, array<string, mixed>>}
     */
    private function publicationsByMonth(): array
    {
        $start = now()->startOfMonth()->subMonths(5);

        $months = collect(range(0, 5))->map(fn (int $i) => $start->copy()->addMonths($i));

        $types = [
            ['label' => 'Actualités', 'color' => '#2563eb', 'model' => Article::class, 'column' => 'published_at'],
            ['label' => 'Web TV', 'color' => '#059669', 'model' => Video::class, 'column' => 'published_at'],
            ['label' => 'Activités', 'color' => '#f59e0b', 'model' => Activity::class, 'column' => 'held_on'],
        ];

        $series = [];

        foreach ($types as $type) {
            $counts = $this->countByMonth($type['model'], $type['column'], $start);

            $series[] = [
                'label' => $type['label'],
                'color' => $type['color'],
                'points' => $months
                    ->map(fn (Carbon $month) => (int) ($counts[$month->format('Y-m')] ?? 0))
                    ->all(),
            ];
        }

        return [
            'labels' => $months->map(fn (Carbon $month) => $month->translatedFormat('M Y'))->all(),
            'series' => $series,
        ];
    }

    /**
     * Rows per month, keyed "YYYY-MM".
     *
     * The month expression is driver-dependent: strftime is SQLite's,
     * DATE_FORMAT is MySQL's. Picking it from the connection keeps this
     * working on the server and in the tests alike.
     *
     * @param  class-string<Model>  $model
     * @return Collection<string, int>
     */
    private function countByMonth(string $model, string $column, Carbon $start): Collection
    {
        $connection = $model::query()->getConnection();

        $month = match ($connection->getDriverName()) {
            'sqlite' => "strftime('%Y-%m', {$column})",
            'pgsql' => "to_char({$column}, 'YYYY-MM')",
            default => "DATE_FORMAT({$column}, '%Y-%m')",
        };

        return $model::query()
            ->whereNotNull($column)
            ->where($column, '>=', $start)
            ->groupBy('month')
            ->selectRaw("{$month} as month, COUNT(*) as total")
            ->pluck('total', 'month');
    }

    /**
     * What the site actually holds, for the donut.
     *
     * @return array<int, array{label: string, value: int, color: string}>
     */
    private function contentBreakdown(): array
    {
        $items = [
            ['label' => 'Actualités', 'value' => Article::public()->count(), 'color' => '#2563eb'],
            ['label' => 'Web TV', 'value' => Video::public()->count(), 'color' => '#059669'],
            ['label' => 'Fiches pratiques', 'value' => Guide::public()->count(), 'color' => '#f59e0b'],
            ['label' => 'Activités', 'value' => Activity::public()->count(), 'color' => '#fb923c'],
            ['label' => 'Appels à candidatures', 'value' => Call::open()->count(), 'color' => '#ec4899'],
            ['label' => 'Galerie photos', 'value' => ActivityPhoto::count(), 'color' => '#60a5fa'],
            ['label' => 'Rapports annuels', 'value' => Report::public()->count(), 'color' => '#94a3b8'],
        ];

        // A zero segment draws nothing and reads as a stray legend entry.
        return array_values(array_filter($items, fn (array $item) => $item['value'] > 0));
    }
}
