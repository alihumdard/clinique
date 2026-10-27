<?php

/*
|--------------------------------------------------------------------------
| Pré-saisine — filtre d'orientation
|--------------------------------------------------------------------------
|
| PROVISOIRE. The plan asks for "un filtre d'orientation composé de quatre
| questions" and names the outcomes it must produce, but the questions
| themselves, their wording and the exact criteria are listed among the
| "éléments à confirmer par la Clinique". What follows is built from the
| outcomes the plan does state:
|
|   - "si l'utilisateur a déjà un avocat, l'orienter vers l'ANAJJ"
|   - "en cas d'urgence, afficher l'instruction téléphonique approuvée"
|   - "préciser que l'accueil se fait uniquement à Niamey pour les
|      personnes qui ne peuvent pas se déplacer"
|   - "si aucune orientation ne correspond, afficher la référence vers le
|      partenaire approuvé"
|
| Every label below is ours and is shown to the visitor as provisional
| until the Clinic approves it. Changing the wording is an edit to this
| file; changing the logic is not needed.
|
| An answer that does not route to the form is never stored: "ne pas
| conserver les réponses sous forme de dossier juridique si l'utilisateur
| n'est pas dirigé vers le formulaire de dépôt".
|
*/

return [

    // Shown on the orientation page while the wording is not approved.
    'provisional' => true,

    'questions' => [

        [
            'key' => 'urgence',
            'label' => "Votre situation présente-t-elle un danger immédiat pour une personne ?",
            'help' => "Violences en cours, menace pour l'intégrité physique, mise en danger d'un enfant.",
            // The answer that stops the journey, and what is shown then.
            'blocking' => 'oui',
            'outcome' => 'urgence',
        ],

        [
            'key' => 'avocat',
            'label' => "Êtes-vous déjà assisté par un avocat dans cette affaire ?",
            'help' => "Un avocat déjà saisi suit votre dossier ; la Clinique n'intervient pas en parallèle.",
            'blocking' => 'oui',
            'outcome' => 'avocat',
        ],

        [
            'key' => 'domaine',
            'label' => "Votre demande relève-t-elle d'un des domaines d'intervention de la Clinique ?",
            'help' => "Famille, travail, foncier, état civil, succession, pénal, administratif.",
            'blocking' => 'non',
            'outcome' => 'hors_domaine',
        ],

        [
            'key' => 'deplacement',
            'label' => "Pouvez-vous vous déplacer à Niamey si une consultation est nécessaire ?",
            'help' => "L'accueil de la Clinique se tient uniquement à Niamey.",
            'blocking' => 'non',
            'outcome' => 'hors_niamey',
        ],

    ],

    /*
    | What the visitor is shown when a question stops the journey. The
    | telephone instruction, the ANAJJ wording and the partner to refer to
    | are all awaiting the Clinic's approved text; the contact details
    | come from the site settings so they are never written twice.
    */
    'outcomes' => [

        'urgence' => [
            'title' => "Votre situation nécessite une réponse immédiate",
            'body' => "La pré-saisine en ligne n'est pas adaptée à une situation d'urgence : elle est examinée sous plusieurs jours ouvrables. Contactez directement les services compétents, ou appelez la Clinique pendant ses heures d'ouverture.",
            'show_phone' => true,
        ],

        'avocat' => [
            'title' => "Vous êtes déjà assisté par un avocat",
            'body' => "La Clinique n'intervient pas sur une affaire déjà suivie par un avocat. Pour toute question sur l'aide juridictionnelle ou sur la désignation d'un avocat, adressez-vous à l'ANAJJ (Agence Nationale d'Assistance Juridique et Judiciaire).",
            'show_phone' => false,
        ],

        'hors_domaine' => [
            'title' => "Votre demande sort de nos domaines d'intervention",
            'body' => "La Clinique intervient sur un nombre limité de matières. Nous vous invitons à consulter la page de nos services, ou à nous contacter pour être orienté vers une structure partenaire.",
            'show_phone' => false,
        ],

        'hors_niamey' => [
            'title' => "L'accueil de la Clinique se tient à Niamey",
            'body' => "Si vous ne pouvez pas vous déplacer, contactez-nous : nous vous orienterons vers une structure partenaire proche de chez vous, et nos caravanes juridiques se rendent régulièrement en région.",
            'show_phone' => true,
        ],

    ],

    /*
    | Attachments, as the plan sets them: "jusqu'à cinq pièces jointes
    | PDF/JPEG/PNG, de 5 Mo maximum chacune, sous réserve de confirmation
    | finale".
    */
    'attachments' => [
        'max_files' => 5,
        'max_size_kb' => 5120,
        'mimes' => ['pdf', 'jpg', 'jpeg', 'png'],
    ],

];
