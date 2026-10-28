@component('mail::message')
# Votre demande a bien été reçue

Bonjour {{ $name }},

La Clinique Juridique FSJP-UAM a bien reçu votre pré-saisine. Conservez le
numéro de référence ci-dessous : il identifie votre demande dans nos échanges.

@component('mail::panel')
**{{ $reference }}**
@endcomponent

Votre demande va être examinée. Vous serez informé de sa recevabilité et, le
cas échéant, des suites à donner.

**Si votre situation présente un danger immédiat**, n'attendez pas cette
réponse : appelez-nous pendant nos heures d'ouverture, ou contactez directement
les services compétents.

@component('mail::button', ['url' => route('contact')])
Nous contacter
@endcomponent

Ce message est un accusé de réception automatique ; il n'appelle pas de
réponse.

Cordialement,
{{ config('clinique.name') }}
@endcomponent
