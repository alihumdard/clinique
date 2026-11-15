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
    'provisional' => false,

    'questions' => [

        [
            'key' => 'urgence',
            'label' => "Votre situation est-elle urgente (garde à vue, audience dans moins de 48h, violence en cours) ?",
            'help' => "Garde à vue, audience imminente, expulsion, violence conjugale ou familiale en cours.",
            'blocking' => 'oui',
            'outcome' => 'urgence',
        ],

        [
            'key' => 'avocat',
            'label' => "Êtes-vous déjà représenté par un avocat dans cette affaire ?",
            'help' => "Si un avocat suit déjà votre dossier, la Clinique n'intervient pas en parallèle.",
            'blocking' => 'oui',
            'outcome' => 'avocat',
        ],

        [
            'key' => 'domaine',
            'label' => "Votre demande concerne-t-elle le droit de la famille, le droit du travail, le foncier, l'état civil, les droits humains ou un domaine juridique similaire ?",
            'help' => "Mariage, divorce, succession, contrat de travail, litige foncier, acte de naissance, nationalité, droits des réfugiés…",
            'blocking' => 'non',
            'outcome' => 'hors_domaine',
        ],

        [
            'key' => 'deplacement',
            'label' => "Pouvez-vous vous déplacer à Niamey pour une consultation si nécessaire ?",
            'help' => "Les permanences de la Clinique se tiennent à la FSJP - UAM, à Niamey, le mardi et le jeudi.",
            'blocking' => 'non',
            'outcome' => 'hors_niamey',
        ],

    ],

    /*
    | Outcomes A–E per client corrections document.
    | A = can proceed (routed to form — handled by controller, not here)
    | B = urgence
    | C = already has lawyer
    | D = out of scope
    | E = cannot travel to Niamey
    */
    'outcomes' => [

        'urgence' => [
            'title' => "Votre situation est urgente — appelez-nous directement",
            'body' => "La pré-saisine en ligne est traitée sous cinq jours ouvrables, ce qui n'est pas adapté à votre situation. Appelez la Clinique pendant les permanences (mardi et jeudi, 8h30–18h00). En dehors des heures d'ouverture, contactez les services de police ou de gendarmerie les plus proches.",
            'show_phone' => true,
        ],

        'avocat' => [
            'title' => "Vous êtes déjà représenté par un avocat",
            'body' => "La Clinique Juridique n'intervient pas sur une affaire déjà prise en charge par un avocat. Si vous souhaitez un deuxième avis sur vos droits en dehors de cette affaire, vous pouvez nous contacter directement pendant les permanences.",
            'show_phone' => false,
        ],

        'hors_domaine' => [
            'title' => "Votre demande ne relève pas de nos domaines d'intervention",
            'body' => "La Clinique intervient principalement en droit de la famille, droit du travail, droit foncier, état civil, successions, droits humains, droits des réfugiés et état civil. Si votre demande concerne un autre domaine, contactez-nous : nous vous orienterons vers un partenaire ou une structure compétente.",
            'show_phone' => false,
        ],

        'hors_niamey' => [
            'title' => "Nos permanences se tiennent à Niamey",
            'body' => "Si vous ne pouvez pas vous déplacer à Niamey, contactez-nous par téléphone ou par WhatsApp pendant les permanences (mardi et jeudi, 8h30–18h00). Nous vous orienterons vers une structure partenaire proche de chez vous. La Clinique organise également des caravanes juridiques dans plusieurs localités du Niger.",
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
