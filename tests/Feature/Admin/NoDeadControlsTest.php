<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * No screen in the administration offers a control that does nothing.
 *
 * This is one defect, found three times over, and it reads the same way
 * every time: the person clicks, the page appears to reload, and nothing
 * happens. An `href="#"` reloads. A `<button type="button">` with nothing
 * bound to it is inert. Both look to the user like the screen refusing to
 * obey rather than like a control that is simply not there.
 *
 * The rule the design system already stated for Dupliquer — *null hides
 * the button entirely rather than showing a control that does nothing* —
 * is asserted here for every screen at once.
 */
class NoDeadControlsTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, array{string}> */
    public static function adminScreens(): array
    {
        $routes = [
            'dashboard' => 'admin.dashboard',
            'actualités' => 'admin.news',
            'web tv' => 'admin.webtv',
            'pages' => 'admin.pages',
            'appels' => 'admin.calls',
            'activités' => 'admin.activities',
            'équipe' => 'admin.team',
            'partenaires' => 'admin.partners',
            'fiches pratiques' => 'admin.guides',
            'rapports annuels' => 'admin.reports',
            'référentiels' => 'admin.references',
            'comptes' => 'admin.users',
            'messages' => 'admin.messages',
            'newsletter' => 'admin.newsletter',
            'paramètres' => 'admin.settings',
            'mon compte' => 'admin.profile',
        ];

        return array_map(fn ($r) => [$r], $routes);
    }

    private function html(string $route): string
    {
        return $this->actingAs(User::factory()->create())
            ->get(route($route))
            ->assertOk()
            ->getContent();
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('adminScreens')]
    public function test_no_link_points_at_nothing(string $route): void
    {
        $html = $this->html($route);

        $this->assertStringNotContainsString('href="#"', $html,
            "{$route} has a link to '#', which only reloads the page.");
        $this->assertStringNotContainsString('href=""', $html,
            "{$route} has a link with an empty destination.");
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('adminScreens')]
    public function test_no_form_posts_to_nothing(string $route): void
    {
        $html = $this->html($route);

        $this->assertStringNotContainsString('action="#"', $html,
            "{$route} has a form posting to '#', which reloads the page and saves nothing.");
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('adminScreens')]
    public function test_every_plain_button_has_something_bound_to_it(string $route): void
    {
        $html = $this->html($route);

        // A type="button" does not submit. Something has to be listening:
        // a dialog to open or close, an id that app.js binds, or a menu it
        // controls. Otherwise it is decoration that looks clickable.
        preg_match_all('~<button\b(?![^>]*\btype="submit")[^>]*>~', $html, $matches);

        $inert = array_values(array_filter($matches[0], function (string $tag) {
            foreach (['data-open-dialog', 'data-dialog-close', 'data-carousel', 'id=', 'aria-haspopup', 'aria-expanded', 'aria-controls'] as $bound) {
                if (str_contains($tag, $bound)) {
                    return false;
                }
            }

            return true;
        }));

        $this->assertSame([], $inert,
            "{$route} renders a button with nothing bound to it: ".implode(' | ', $inert));
    }

    public function test_categories_have_no_management_screen_to_link_to(): void
    {
        // The actualités screen carried an "Ajouter" beside Catégories.
        // Six content types read categories and nothing anywhere creates,
        // renames or deletes one — they are seeded. If a route appears,
        // the button can come back; until then it promises a screen that
        // does not exist.
        foreach (['/admin/categories', '/admin/rubriques'] as $uri) {
            $this->actingAs(User::factory()->create())->get($uri)->assertNotFound();
        }

        $this->assertStringNotContainsString(
            'Ajouter',
            // Only the categories panel, not the whole page: the page's own
            // "Ajouter une actualité" is real and opens a dialog.
            $this->sliceAfter($this->html('admin.news'), 'Catégories', 400),
            'The categories panel offers to add one, and there is no screen that can.'
        );
    }

    private function sliceAfter(string $haystack, string $needle, int $length): string
    {
        $at = strpos($haystack, $needle);

        return $at === false ? '' : substr($haystack, $at, $length);
    }
}
