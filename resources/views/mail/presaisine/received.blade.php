@component('mail::message')
# Nouvelle pré-saisine

Une demande a été déposée sur le site public et attend d'être examinée.

@component('mail::table')
| | |
|:--|:--|
| **Référence** | {{ $submission->reference }} |
| **Reçue le** | {{ $submission->created_at->translatedFormat('j F Y à H:i') }} |
| **Domaine** | {{ $submission->legal_domain }} |
| **Pièces jointes** | {{ $submission->attachments()->count() }} |
@endcomponent

{{--
    The request itself is not repeated here. It stays in the registre,
    behind a sign-in: an e-mail travels unencrypted and lands in an inbox
    the Clinic does not control.
--}}
Le détail de la demande est consultable dans le registre numérique.

@component('mail::button', ['url' => $url])
Ouvrir la demande
@endcomponent

@endcomponent
