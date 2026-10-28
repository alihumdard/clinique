<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * What a listing page tells a search engine about itself.
 *
 * A canonical URL built from the path alone collapses every page of a
 * listing and every rubrique filter onto page one, which tells the engine
 * to drop the rest. Both of these keep the parameters that change *which*
 * items a page shows, and drop the ones that only record where the
 * visitor came from.
 *
 * There are deliberately no hreflang alternates. The English version is an
 * interface translation served from `lang/en.json` at the same URL, chosen
 * by cookie, and the body text of every page stays French until the
 * Clinic translates it. Alternates would advertise two addresses that are
 * the same address, holding pages that are near-identical — worse than
 * saying nothing. They become right the day the content itself is
 * translated and each language has its own URL.
 */
class Seo
{
    /**
     * Parameters that say where a visitor came from, not which page they
     * are on. Left in, every shared link would become its own canonical.
     */
    private const CAMPAIGN_PARAMETERS = [
        'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
        'fbclid', 'gclid', 'msclkid', 'mc_cid', 'mc_eid', 'ref',
    ];

    public static function canonical(Request $request): string
    {
        $parameters = collect($request->query())
            ->except(self::CAMPAIGN_PARAMETERS)
            ->reject(fn ($value) => $value === null || $value === '' || $value === [])
            // Sorted, so ?annee=2026&categorie=x and ?categorie=x&annee=2026
            // resolve to one canonical URL rather than two.
            ->sortKeys()
            ->all();

        return $parameters === []
            ? $request->url()
            : $request->url().'?'.http_build_query($parameters);
    }

    /**
     * Appended to the title of page two onwards, so that the pages of a
     * listing are not six documents with one name.
     */
    public static function pageSuffix(Request $request): string
    {
        $page = (int) $request->query('page', 1);

        return $page > 1 ? ' ('.__('page').' '.$page.')' : '';
    }
}
