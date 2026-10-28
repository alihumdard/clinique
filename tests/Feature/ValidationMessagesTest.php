<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;

/**
 * Validation messages are sentences, not keys.
 *
 * The recette found "validation.in" printed on the équipe screen. The cause
 * was not one missing key: there was no lang/fr/validation.php at all, so
 * every key in the file resolved to itself. The forms looked French only
 * because nearly every FormRequest writes its own messages by hand — and
 * any rule those files had not anticipated leaked its key to the Clinic.
 */
class ValidationMessagesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Every rule used anywhere in the application, so a new one cannot be
     * added without a French sentence behind it.
     *
     * @return array<string, array{string}>
     */
    public static function rules(): array
    {
        $keys = [
            'accepted', 'after', 'before', 'boolean', 'confirmed', 'date', 'date_format',
            'different', 'digits', 'email', 'exists', 'file', 'filled', 'image', 'in',
            'integer', 'max.array', 'max.file', 'max.numeric', 'max.string', 'mimes',
            'mimetypes', 'min.array', 'min.file', 'min.numeric', 'min.string', 'not_in',
            'numeric', 'present', 'prohibited', 'regex', 'required', 'required_if',
            'required_with', 'same', 'size.file', 'size.string', 'string', 'unique',
            'uploaded', 'url',
        ];

        return array_combine($keys, array_map(fn ($k) => [$k], $keys));
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('rules')]
    public function test_a_rule_resolves_to_a_french_sentence(string $key): void
    {
        $message = Lang::get("validation.{$key}");

        $this->assertNotSame("validation.{$key}", $message,
            "validation.{$key} has no translation, so a form refused by that rule prints the key.");
        $this->assertStringEndsWith('.', $message, "validation.{$key} is not a sentence.");
    }

    public function test_the_rule_that_was_reported_now_names_the_field(): void
    {
        // /admin/equipe, department outside direction|enseignants|etudiants|
        // administratif. The capitalised value is enough to fail it.
        $response = $this->actingAs(User::factory()->create())
            ->from(route('admin.team'))
            ->post(route('admin.team.store'), [
                'name' => 'QA Test',
                'role_title' => 'Testeur',
                'department' => 'Direction',
                'status' => 'published',
            ]);

        $response->assertSessionHasErrors('department');

        // TeamMemberRequest calls the field "groupe" in its attributes(),
        // and the generic message now picks that name up instead of
        // printing the rule's key.
        $error = session('errors')->first('department');
        $this->assertStringNotContainsString('validation.', $error);
        $this->assertStringContainsString('groupe', $error);
    }

    public function test_a_rule_without_a_hand_written_message_no_longer_leaks(): void
    {
        // CallRequest declares no messages() of its own, so before the
        // translation file existed every one of its failures printed a key.
        $response = $this->actingAs(User::factory()->create())
            ->from(route('admin.calls'))
            ->post(route('admin.calls.store'), []);

        $response->assertSessionHasErrors();

        foreach (session('errors')->all() as $error) {
            $this->assertStringNotContainsString('validation.', $error,
                "A refused appel à candidatures printed a raw key: {$error}");
        }
    }
}
