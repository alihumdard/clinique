<?php

/*
|--------------------------------------------------------------------------
| Clinic institutional data
|--------------------------------------------------------------------------
|
| Single source of truth for contact details, opening hours and social
| links. Referenced by the header, footer, homepage and contact page so a
| number is never duplicated across Blade files.
|
| Values are env-overridable: Module 2 moves these into CMS-editable site
| settings, at which point the config falls back to these defaults.
|
*/

return [

    'name' => 'Clinique Juridique FSJP-UAM',

    'faculty' => 'Faculté des Sciences Juridiques et Politiques',

    'university' => 'Université Abdou Moumouni de Niamey',

    /*
    | The one-paragraph description of the Clinic, used as the default meta
    | description and in the footer. Editable from Paramètres du site.
    */
    'description' => "La Clinique Juridique de la FSJP-UAM contribue à l'accès à la justice et à la promotion des droits humains à travers la formation, la sensibilisation et l'assistance juridique.",

    /*
    | Defaults used when a page sets no title or description of its own.
    */
    'seo' => [
        'title' => 'Clinique Juridique FSJP-UAM - Accès au droit pour tous',
        'description' => "La Clinique Juridique FSJP-UAM offre une assistance juridique gratuite, des consultations, des caravanes juridiques et des ressources pratiques pour l'accès au droit au Niger.",
    ],

    /*
    | Contact. Phone numbers are stored in two forms: `tel` is the E.164
    | value used in href attributes, `display` is the human-readable
    | spacing shown on screen.
    */
    'contact' => [
        'phone' => [
            'tel' => env('CLINIQUE_PHONE_TEL', '+22720315263'),
            'display' => env('CLINIQUE_PHONE_DISPLAY', '+227 20 31 52 63'),
        ],
        'whatsapp' => [
            // Digits only, no '+' — wa.me rejects the plus sign.
            'number' => env('CLINIQUE_WHATSAPP', '22720315263'),
        ],
        'email' => env('CLINIQUE_EMAIL', 'contact@clinique-fsjp.ne'),
        'address' => [
            'line1' => 'Faculté des Sciences Juridiques et Politiques (FSJP)',
            'line2' => 'Université Abdou Moumouni de Niamey',
            'line3' => 'BP 10 696',
            'city' => 'Niamey',
            'country' => 'Niger',
        ],
    ],

    /*
    | Permanence (walk-in) hours, shown on the homepage and contact page.
    | `days` keeps the French label; `note` carries any qualifier.
    */
    'hours' => [
        // Short form used in the header strip, footer and homepage card.
        'days' => 'Lundi - Vendredi',
        'time' => '08h00 - 16h00',

        // Full schedule shown on the contact page.
        'schedule' => [
            ['days' => 'Lundi - Vendredi', 'time' => '08h00 - 16h00'],
            ['days' => 'Samedi', 'time' => '08h00 - 12h00'],
        ],
        'closed' => 'Fermé le dimanche',

        'note' => 'Accueil uniquement à Niamey',
    ],

    /*
    | Social profiles. A null value hides the icon rather than rendering a
    | dead '#' link.
    */
    'social' => [
        'facebook' => env('CLINIQUE_FACEBOOK'),
        'x' => env('CLINIQUE_X'),
        'youtube' => env('CLINIQUE_YOUTUBE'),
        'instagram' => env('CLINIQUE_INSTAGRAM'),
        'linkedin' => env('CLINIQUE_LINKEDIN'),
    ],

];
