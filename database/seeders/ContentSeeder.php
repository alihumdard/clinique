<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Fictitious content for development and review. The plan forbids real
 * Clinic data during development, and the Clinic's own first-version
 * content arrives in phase 4.
 */
class ContentSeeder extends Seeder
{
    public function run(): void
    {
        // The rubriques the mock-ups were built around. Narrower than a
        // generic news feed on purpose: a visitor looking for a caravane or
        // a communiqué finds it, and every name here is one the Clinic uses
        // about its own work.
        $categories = collect([
            ['name' => 'Événements', 'tone' => 'blue'],
            ['name' => 'Formations', 'tone' => 'purple'],
            ['name' => 'Caravanes juridiques', 'tone' => 'amber'],
            ['name' => 'Communiqués', 'tone' => 'red'],
            ['name' => 'Publications', 'tone' => 'green'],
        ])->map(fn (array $c, int $i) => Category::updateOrCreate(
            ['type' => 'article', 'slug' => Str::slug($c['name'])],
            ['name' => $c['name'], 'tone' => $c['tone'], 'position' => $i]
        ));

        $byName = $categories->keyBy('name');
        $author = User::first();

        $articles = [
            [
                'title' => 'Lancement de la caravane juridique à Zinder',
                'category' => 'Caravanes juridiques',
                'image' => 'placeholders/news-caravan',
                'published_at' => '2025-06-10 14:32',
                'status' => 'published',
                'excerpt' => "La Clinique Juridique FSJP-UAM a lancé une caravane d'information juridique dans la région de Zinder, à la rencontre des populations les plus éloignées des services de justice.",
                'body' => "La Clinique Juridique FSJP-UAM a lancé, le 10 juin 2025, une caravane d'information juridique dans la région de Zinder. Pendant une semaine, étudiants encadrés et enseignants se sont rendus dans plusieurs communes pour informer les habitants sur leurs droits et les orienter vers les services compétents.\n\nLes équipes ont tenu des permanences gratuites sur les sujets qui reviennent le plus souvent : état civil, succession, mariage et divorce, litiges fonciers, droit du travail. Chaque permanence s'est accompagnée d'une séance publique de questions-réponses animée en langue locale.\n\nPour les étudiants, la caravane est un terrain de formation : ils écoutent une demande réelle, la qualifient juridiquement et rédigent une fiche d'orientation sous la supervision d'un enseignant. C'est la mission première de la Clinique, qui lie l'apprentissage du droit au service rendu au public.\n\nUn bilan de la caravane sera publié dans le rapport annuel d'activité de la Clinique.",
            ],
            [
                'title' => 'Formation sur les droits des femmes',
                'category' => 'Formations',
                'image' => 'placeholders/news-formation',
                'published_at' => '2025-06-08 11:20',
                'status' => 'published',
                'excerpt' => "Une session de formation a réuni les étudiants de la Clinique autour des droits des femmes en matière de mariage, de succession et de protection contre les violences.",
                'body' => "La Clinique Juridique FSJP-UAM a organisé une session de formation consacrée aux droits des femmes, à destination des étudiants qui assurent les consultations et les permanences de terrain.\n\nLa session a porté sur le régime du mariage et de sa dissolution, les droits successoraux, la protection contre les violences faites aux femmes et les voies de recours ouvertes aux victimes. Les intervenants ont insisté sur l'articulation entre les textes nationaux, les conventions internationales ratifiées par le Niger et les pratiques coutumières auxquelles les justiciables se réfèrent.\n\nLa seconde partie a été consacrée à des cas pratiques tirés de demandes réellement reçues par la Clinique : comment accueillir la personne, quelles questions poser, où l'orienter et ce que la Clinique ne peut pas faire à la place d'un avocat.\n\nCes formations sont organisées tout au long de l'année universitaire et conditionnent la participation des étudiants aux activités de consultation.",
            ],
            [
                'title' => 'Nouvelle fiche pratique disponible',
                'category' => 'Publications',
                'image' => 'placeholders/news-guide',
                'published_at' => '2025-06-05 09:15',
                'status' => 'published',
                'excerpt' => "Une nouvelle fiche pratique, rédigée en langage simple, est disponible gratuitement dans la rubrique Ressources du site.",
                'body' => "La Clinique Juridique FSJP-UAM met en ligne une nouvelle fiche pratique, consultable et téléchargeable gratuitement depuis la rubrique Ressources.\n\nLes fiches pratiques répondent à une question de droit courante en quelques pages, dans un langage accessible : ce que dit la loi, les démarches à accomplir, les pièces à réunir et les services à contacter. Elles sont rédigées par les étudiants de la Clinique et relues par un enseignant avant publication.\n\nElles sont conçues pour être imprimées et diffusées lors des caravanes et des séances de sensibilisation, où elles restent entre les mains des participants une fois les équipes reparties.\n\nUne fiche pratique informe mais ne remplace pas une consultation : pour une situation personnelle, la Clinique invite à utiliser le formulaire de pré-saisine.",
            ],
            [
                'title' => 'Appel à candidatures – Stagiaires 2025',
                'category' => 'Communiqués',
                'image' => 'placeholders/gallery-caravan-meeting',
                'published_at' => '2025-06-01 12:10',
                'status' => 'published',
                'excerpt' => "La Clinique Juridique FSJP-UAM recrute sa nouvelle promotion d'étudiants encadrés. Les candidatures sont ouvertes aux étudiants de la Faculté des Sciences Juridiques et Politiques.",
                'body' => "La Clinique Juridique FSJP-UAM lance un appel à candidatures pour intégrer sa nouvelle promotion d'étudiants encadrés.\n\nLes étudiants retenus participent à l'accueil et à la qualification des demandes, à la rédaction des fiches d'orientation, aux séances de sensibilisation et aux caravanes juridiques, sous la supervision permanente d'un enseignant.\n\nLe dossier de candidature comprend une lettre de motivation, un curriculum vitæ et un relevé de notes. La sélection se fait sur dossier, puis par un entretien portant sur la motivation du candidat et sa compréhension du rôle d'une clinique juridique.\n\nLes modalités, les pièces à fournir et la date limite de dépôt sont précisées dans la rubrique Rejoindre la Clinique.",
            ],
            [
                'title' => 'La Clinique FSJP-UAM renforce ses partenariats',
                'category' => 'Communiqués',
                'image' => 'placeholders/about-hero',
                'published_at' => '2025-05-28 16:45',
                'status' => 'published',
                'excerpt' => "Une convention de partenariat a été signée afin de renforcer l'accès au droit dans les communautés rurales et d'élargir la couverture des caravanes juridiques.",
                'body' => "La Clinique Juridique FSJP-UAM a signé une convention de partenariat destinée à renforcer l'accès au droit dans les communautés rurales.\n\nLe partenariat porte sur trois volets : l'organisation conjointe de caravanes juridiques dans des zones que la Clinique n'atteignait pas encore, la formation des relais communautaires chargés d'orienter les habitants, et la production de supports d'information dans les langues les plus parlées.\n\nIl prévoit également l'accueil de stagiaires de la Clinique chez le partenaire, afin que les étudiants découvrent la pratique du terrain au-delà du cadre universitaire.\n\nLa liste des partenaires institutionnels, universitaires et associatifs de la Clinique est tenue à jour dans la rubrique Partenaires.",
            ],
            [
                'title' => 'Publication du rapport annuel 2024',
                'category' => 'Publications',
                'image' => 'placeholders/news-partenariat',
                'published_at' => '2025-05-25 10:22',
                'status' => 'published',
                'excerpt' => "Le rapport annuel d'activité 2024 de la Clinique Juridique FSJP-UAM est disponible : demandes reçues, caravanes organisées et étudiants formés.",
                'body' => "La Clinique Juridique FSJP-UAM publie son rapport annuel d'activité pour l'année 2024, téléchargeable dans la rubrique Ressources.\n\nLe rapport présente les demandes reçues et leur répartition par matière, les permanences et les caravanes organisées dans l'année, le nombre d'étudiants encadrés et les formations qui leur ont été dispensées.\n\nIl revient aussi sur les difficultés rencontrées : l'éloignement géographique de nombreux justiciables, le coût des déplacements et la méconnaissance des voies de recours, qui restent les premiers obstacles à l'accès au droit.\n\nLa Clinique publie ce bilan chaque année, par souci de transparence envers l'Université, ses partenaires et les personnes qui la sollicitent.",
            ],
            [
                'title' => 'Nouvelle capsule Web TV : Accès à la justice',
                'category' => 'Publications',
                'image' => 'placeholders/video-featured',
                'published_at' => null,
                'status' => 'draft',
                'excerpt' => "Une nouvelle capsule vidéo consacrée à l'accès à la justice sera bientôt disponible sur la Web TV de la Clinique.",
                'body' => "La Clinique Juridique FSJP-UAM prépare une nouvelle capsule pour sa Web TV, consacrée à l'accès à la justice.\n\nLa capsule expliquera, en quelques minutes et dans un langage simple, à qui s'adresser selon la nature du litige, ce que coûte une procédure et quelles aides existent pour les personnes sans ressources.\n\nLes capsules de la Web TV sont tournées avec les étudiants de la Clinique et relues par un enseignant avant diffusion.",
            ],
            [
                'title' => 'Clinique juridique présente au forum universitaire',
                'category' => 'Événements',
                'image' => 'placeholders/news-3',
                'published_at' => '2025-05-16 13:18',
                'status' => 'published',
                'excerpt' => "La Clinique a tenu un stand au forum universitaire de l'Université Abdou Moumouni pour présenter ses activités aux étudiants et au public.",
                'body' => "La Clinique Juridique FSJP-UAM a participé au forum universitaire organisé sur le campus de l'Université Abdou Moumouni de Niamey.\n\nLe stand de la Clinique a accueilli des étudiants venus se renseigner sur les conditions d'intégration de la promotion, mais aussi des visiteurs venus poser des questions juridiques concrètes, qui ont été orientés vers la pré-saisine.\n\nLes fiches pratiques de la Clinique ont été distribuées sur place et les capsules de la Web TV diffusées en continu.\n\nCes rencontres font partie du travail d'information de la Clinique : beaucoup de personnes ignorent qu'une consultation juridique gratuite existe à l'Université.",
            ],
            [
                'title' => 'Guide pratique : droits des étudiants',
                'category' => 'Publications',
                'image' => 'placeholders/article-highlight-1',
                'published_at' => '2025-05-12 09:40',
                'status' => 'published',
                'excerpt' => "Inscription, bourse, discipline, logement : un guide pratique réunit les règles qui encadrent la vie universitaire et les recours ouverts aux étudiants.",
                'body' => "La Clinique Juridique FSJP-UAM publie un guide pratique consacré aux droits des étudiants.\n\nLe guide réunit les règles applicables à l'inscription et à la réinscription, à l'attribution et à la suspension des bourses, à la procédure disciplinaire et aux conditions de logement, avec les recours ouverts à chaque étape.\n\nIl est né des demandes reçues par la Clinique : une part importante des consultations sur le campus porte sur des décisions administratives que les étudiants ne savent pas contester.\n\nLe guide est téléchargeable gratuitement dans la rubrique Ressources.",
            ],
            [
                'title' => 'Conférence sur la justice sociale',
                'category' => 'Événements',
                'image' => 'placeholders/news-1',
                // Dated ahead so the admin always has one scheduled article
                // to look at; a relative date keeps it that way whenever the
                // seeder is run.
                'published_at' => '+2 months',
                'status' => 'published',
                'excerpt' => "La Clinique Juridique FSJP-UAM organise une conférence-débat sur la justice sociale et l'accès au droit des personnes vulnérables.",
                'body' => "La Clinique Juridique FSJP-UAM organise une conférence-débat consacrée à la justice sociale et à l'accès au droit des personnes en situation de vulnérabilité.\n\nLa rencontre réunira des enseignants, des praticiens, des représentants d'organisations de la société civile et les étudiants de la Clinique. Les échanges porteront sur les obstacles concrets à l'accès au droit — distance, coût, langue, méconnaissance des procédures — et sur les réponses que les cliniques juridiques peuvent y apporter.\n\nLe programme détaillé et les modalités d'inscription seront publiés sur cette page.",
            ],
            [
                'title' => 'Caravane juridique à Tillabéri',
                'category' => 'Caravanes juridiques',
                'image' => 'placeholders/news-caravan',
                'published_at' => '2024-09-28 10:00',
                'status' => 'published',
                'excerpt' => "La caravane juridique de la Clinique s'est rendue dans la région de Tillabéri, où les permanences gratuites ont rassemblé une forte participation.",
                'body' => "La Clinique Juridique FSJP-UAM a mené une caravane d'information juridique dans la région de Tillabéri, à la rencontre de populations pour lesquelles le premier tribunal est à plusieurs heures de route.\n\nLes permanences ont porté principalement sur l'état civil, les successions et les litiges fonciers. Plusieurs demandes ont donné lieu à une fiche d'orientation remise sur place, et les situations les plus complexes ont été transmises aux services compétents.\n\nLes séances publiques, tenues en langue locale, ont réuni des habitants de plusieurs villages voisins. Les chefs traditionnels et les autorités locales avaient été associés à la préparation de la caravane.\n\nLes images de la caravane sont réunies dans la galerie photo, et une capsule vidéo lui est consacrée sur la Web TV.",
            ],
            [
                'title' => 'Session de sensibilisation des jeunes',
                'category' => 'Formations',
                'image' => 'placeholders/news-3',
                'published_at' => '2024-10-12 14:00',
                'status' => 'published',
                'excerpt' => "Une séance de sensibilisation a réuni des jeunes autour de leurs droits et devoirs, du contrat de travail et des démarches d'état civil.",
                'body' => "La Clinique Juridique FSJP-UAM a animé une séance de sensibilisation destinée aux jeunes, sur leurs droits et devoirs au quotidien.\n\nLes échanges ont porté sur le contrat de travail et l'apprentissage, les démarches d'état civil, les pièces d'identité et les conséquences juridiques des actes commis en ligne.\n\nLa séance a été conduite par les étudiants de la Clinique, à partir de cas simples inspirés de demandes réelles, avec un temps de questions ouvertes à la fin.\n\nLes participants sont repartis avec les fiches pratiques de la Clinique et les coordonnées de la permanence.",
            ],
            [
                'title' => 'Rencontre avec les autorités locales',
                'category' => 'Événements',
                'image' => 'placeholders/news-2',
                'published_at' => '2023-11-15 09:30',
                'status' => 'published',
                'excerpt' => "La Clinique a rencontré des autorités locales et des chefs traditionnels afin de préparer ses prochaines interventions de terrain.",
                'body' => "La Clinique Juridique FSJP-UAM a rencontré des autorités administratives locales et des chefs traditionnels afin de préparer ses prochaines interventions de terrain.\n\nLa rencontre visait à présenter le rôle de la Clinique — informer, orienter, former des étudiants — et à délimiter clairement ce qu'elle ne fait pas : elle ne juge pas, ne représente personne en justice et ne se substitue à aucun service public.\n\nLes participants ont convenu d'un mode de signalement des situations qui méritent une permanence sur place, et d'un appui à l'information des habitants en amont de chaque caravane.\n\nCette préparation locale explique en grande partie la participation observée lors des caravanes.",
            ],
            [
                'title' => 'Atelier sur la médiation communautaire',
                'category' => 'Formations',
                'image' => 'placeholders/news-formation',
                'published_at' => '2023-06-20 15:00',
                'status' => 'published',
                'excerpt' => "Un atelier a formé les étudiants de la Clinique aux techniques de médiation et de règlement amiable des conflits.",
                'body' => "La Clinique Juridique FSJP-UAM a organisé un atelier consacré à la médiation et au règlement amiable des conflits, à destination de ses étudiants encadrés.\n\nL'atelier a abordé la conduite de l'entretien, l'écoute des parties, la reformulation des demandes et la recherche d'un accord, ainsi que les limites de la médiation : les situations où elle est inadaptée et celles où elle est tout simplement interdite.\n\nLes exercices ont pris la forme de mises en situation, dans lesquelles les étudiants tenaient à tour de rôle le rôle du médiateur et celui des parties.\n\nBeaucoup de litiges portés devant la Clinique peuvent être réglés sans procédure : savoir les reconnaître fait partie de la formation pratique des étudiants.",
            ],
            [
                'title' => 'Bilan des activités 2022',
                'category' => 'Publications',
                'image' => 'placeholders/article-highlight-2',
                'published_at' => '2022-12-18 11:00',
                'status' => 'published',
                'excerpt' => "Le bilan des activités 2022 revient sur les permanences tenues, les caravanes organisées et les étudiants formés au sein de la Clinique.",
                'body' => "La Clinique Juridique FSJP-UAM publie le bilan de ses activités pour l'année 2022.\n\nLe document récapitule les permanences tenues à l'Université, les caravanes organisées en région, les séances de sensibilisation et les formations suivies par les étudiants encadrés.\n\nIl présente également la répartition des demandes par matière, qui guide le choix des thèmes des fiches pratiques et des capsules vidéo de l'année suivante.\n\nLe bilan est téléchargeable dans la rubrique Ressources, avec les rapports des années suivantes.",
            ],
        ];

        foreach ($articles as $article) {
            // A relative expression keeps a scheduled article scheduled
            // whenever the seeder is run; a fixed date would eventually
            // fall into the past and the admin would lose that state.
            $publishedAt = $article['published_at'];
            $publishedAt = $publishedAt && str_starts_with($publishedAt, '+')
                ? now()->modify($publishedAt)
                : $publishedAt;

            Article::updateOrCreate(
                ['slug' => Str::slug($article['title'])],
                [
                    'title' => $article['title'],
                    'excerpt' => $article['excerpt'],
                    'body' => $article['body'],
                    'image' => $article['image'],
                    'category_id' => $byName[$article['category']]->id,
                    'author_id' => $author?->id,
                    'status' => $article['status'],
                    'published_at' => $publishedAt,
                ]
            );
        }

        // Rubriques earlier versions of this seeder created, which the five
        // above replace. Only ones left without a single article are
        // removed, so a rubrique the Clinic has since used is kept.
        Category::where('type', 'article')
            ->whereIn('slug', ['actualites', 'formation', 'ressources', 'web-tv', 'appels-a-candidatures', 'rapports', 'institution'])
            ->whereDoesntHave('articles')
            ->delete();

        $this->videos();
    }

    /**
     * Web TV capsules. Real addresses are not used: the ids below are
     * fictitious, so a seeded capsule shows the click-to-load poster and
     * never embeds someone else's video.
     */
    private function videos(): void
    {
        $rubriques = collect([
            ['name' => 'Émissions', 'tone' => 'blue'],
            ['name' => 'Caravanes juridiques', 'tone' => 'amber'],
            ['name' => 'Sensibilisations', 'tone' => 'purple'],
            ['name' => 'Formations', 'tone' => 'green'],
            ['name' => 'Témoignages', 'tone' => 'red'],
            ['name' => 'Interviews', 'tone' => 'slate'],
            ['name' => 'Événements officiels', 'tone' => 'pink'],
        ])->map(fn (array $c, int $i) => Category::updateOrCreate(
            ['type' => 'video', 'slug' => Str::slug($c['name'])],
            ['name' => $c['name'], 'tone' => $c['tone'], 'position' => $i]
        ))->keyBy('name');

        $videos = [
            ["Lancement des activités de la Clinique Juridique FSJP-UAM", 'Événements officiels', 'placeholders/video-featured', '12:35', 2450, '2025-06-12 10:00', 'published'],
            ["Caravane juridique à Tillabéri : accès au droit pour les populations", 'Caravanes juridiques', 'placeholders/news-caravan', '8:24', 1250, '2025-05-28 09:00', 'published'],
            ["Les droits des femmes : paroles d'étudiantes", 'Sensibilisations', 'placeholders/news-3', '6:18', 980, '2025-05-12 14:00', 'published'],
            ['Session de formation sur la médiation', 'Formations', 'placeholders/news-formation', '15:42', 1420, '2025-04-05 11:00', 'published'],
            ['Entretien avec le Doyen de la FSJP sur la Clinique Juridique', 'Interviews', 'placeholders/webtv-interview', '10:36', 2100, '2025-03-20 16:00', 'published'],
            ["Témoignages : l'impact des caravanes juridiques", 'Témoignages', 'placeholders/news-2', '7:50', 1780, '2025-02-15 10:30', 'published'],
            ['Accès au droit : enjeux et perspectives', 'Émissions', 'placeholders/news-1', '9:12', 1560, '2025-01-03 12:00', 'published'],
            ['Table ronde : justice et développement local', 'Événements officiels', 'placeholders/about-hero', '12:20', 1320, '2024-11-25 15:00', 'published'],
            ['Protection des enfants : que dit la loi ?', 'Sensibilisations', 'placeholders/news-candidatures', '6:45', 940, '2024-10-18 09:45', 'published'],
            ['Les mécanismes de règlement des conflits', 'Formations', 'placeholders/news-partenariat', '11:08', 1110, '2024-09-10 13:00', 'published'],
            ['Capsule : comprendre la procédure civile', 'Émissions', 'placeholders/article-highlight-1', '5:40', 0, null, 'draft'],
            ['Rencontre avec les chefs traditionnels', 'Interviews', 'placeholders/about-team', '14:02', 0, '2026-12-01 10:00', 'published'],
        ];

        foreach ($videos as [$title, $rubrique, $cover, $duration, $views, $publishedAt, $status]) {
            // Only the lead capsule carries a transcription, which is how
            // the plan describes it: "lorsqu'elle existe".
            $transcript = $title === $videos[0][0]
                ? "Transcription d'exemple de la capsule « {$title} ». La transcription réelle sera fournie par la Clinique."
                : null;

            // A plausible 11-character id without pointing at a real video.
            $id = substr(str_replace('-', '_', Str::slug($title)) . 'aaaaaaaaaaa', 0, 11);

            $video = Video::updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'summary' => "Capsule d'exemple : « {$title} ». Le contenu réel de la Clinique sera intégré ultérieurement.",
                    'video_url' => "https://www.youtube.com/watch?v={$id}",
                    'provider' => 'youtube',
                    'provider_id' => $id,
                    'cover' => $cover,
                    'duration' => $duration,
                    'transcript' => $transcript,
                    'category_id' => $rubriques[$rubrique]->id,
                    'status' => $status,
                    'published_at' => $publishedAt,
                ]
            );

            // views is a counter, not editor input, so it is not fillable.
            $video->forceFill(['views' => $views])->save();
        }
    }
}
