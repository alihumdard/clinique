<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * The three pages the mockups implied but the page inventory never
 * covered: Cookies, Accessibilité and Crédits.
 *
 * They are seeded as drafts, with text the Clinic can correct rather than
 * write from nothing — and as drafts because each contains statements only
 * the Clinic can confirm. Nothing here is on the site until they publish
 * it from the Pages screen.
 */
class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'cookies',
                'title' => 'Politique de cookies',
                'seo_description' => "Comment la Clinique Juridique FSJP-UAM utilise les cookies sur son site.",
                'body' => <<<'TEXT'
Qu'est-ce qu'un cookie ?

Un cookie est un petit fichier déposé sur votre appareil par le site que vous consultez. Il permet notamment de retenir vos préférences d'une page à l'autre.

Les cookies utilisés par ce site

Ce site n'utilise aucun cookie publicitaire et aucun outil de mesure d'audience tiers. Les seuls cookies déposés sont ceux strictement nécessaires à son fonctionnement :

- un cookie de session, qui permet au site de reconnaître votre navigateur le temps de votre visite ;
- un cookie de sécurité, qui protège les formulaires contre les envois frauduleux.

Ces deux cookies sont indispensables : sans eux, les formulaires de contact et de pré-saisine ne peuvent pas fonctionner. Ils expirent à la fermeture de votre navigateur ou après une période d'inactivité.

Vidéos

Les vidéos de la Web TV ne se chargent qu'au moment où vous cliquez sur « Lire ». Tant que vous ne cliquez pas, aucun contenu n'est demandé à la plateforme d'hébergement et aucun cookie n'est déposé par celle-ci. En cliquant, vous acceptez que la plateforme concernée dépose ses propres cookies, soumis à sa politique.

Gérer les cookies

Vous pouvez supprimer ou bloquer les cookies depuis les réglages de votre navigateur. Le blocage des cookies nécessaires empêchera l'envoi des formulaires.

Pour toute question, vous pouvez nous écrire à l'adresse indiquée sur la page Contact.
TEXT,
            ],
            [
                'slug' => 'accessibilite',
                'title' => "Déclaration d'accessibilité",
                'seo_description' => "L'engagement de la Clinique Juridique FSJP-UAM pour un site accessible à tous.",
                'body' => <<<'TEXT'
Notre engagement

La Clinique Juridique FSJP-UAM souhaite que son site soit consultable par toute personne, quel que soit son matériel, sa connexion ou sa situation de handicap. Cet objectif découle directement de notre mission : l'accès au droit pour tous.

Ce qui a été mis en œuvre

- Une structure de titres ordonnée sur chaque page, pour la navigation au lecteur d'écran.
- Un lien « Aller au contenu principal » en début de page.
- Des libellés associés à chaque champ de formulaire, et des messages d'erreur rattachés au champ concerné.
- Un contraste suffisant entre le texte et son fond.
- Une navigation complète au clavier, avec une indication visible de l'élément actif.
- Des images décoratives marquées comme telles, et une description pour les images porteuses d'information.
- Des pages qui restent utilisables lorsque JavaScript est désactivé.
- Une transcription écrite pour les capsules vidéo qui en disposent.

Limites connues

Certains contenus publiés avant la mise en ligne de ce site peuvent ne pas comporter de description d'image ou de transcription. Ces contenus sont complétés progressivement.

Nous faire un retour

Si vous rencontrez une difficulté d'accès à une information de ce site, écrivez-nous à l'adresse indiquée sur la page Contact en précisant la page concernée. Nous vous répondrons et vous fournirons l'information par un autre moyen si nécessaire.
TEXT,
            ],
            [
                'slug' => 'credits',
                'title' => 'Crédits',
                'seo_description' => "Crédits du site de la Clinique Juridique FSJP-UAM.",
                'body' => <<<'TEXT'
Éditeur du site

Clinique Juridique de la Faculté des Sciences Juridiques et Politiques, Université Abdou Moumouni de Niamey.

Conception et développement

À compléter par la Clinique.

Crédits photographiques

Les photographies présentées sur ce site sont des images de substitution pendant la phase de développement. Elles seront remplacées par les photographies de la Clinique avant la mise en ligne publique.

Typographie

Le site n'utilise aucune police téléchargée : il s'appuie sur les polices déjà présentes sur votre appareil. Aucune requête n'est donc adressée à un service de polices extérieur, et le site reste rapide sur une connexion lente.

Hébergement

À confirmer par la Clinique. Les coordonnées de l'hébergeur figurent sur la page Mentions légales.
TEXT,
            ],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(
                ['slug' => $page['slug']],
                [
                    'title' => $page['title'],
                    'body' => $page['body'],
                    'seo_description' => $page['seo_description'],
                    // Drafts: each contains statements only the Clinic can
                    // confirm, so nothing goes live until they publish it.
                    'status' => Page::STATUS_DRAFT,
                ]
            );
        }
    }
}
