<?php
/**
 * IA1 - Schema.org JSON-LD
 * 
 * Injecte les balises Schema.org dans le <head> de toutes les pages
 * pour améliorer le référencement dans les moteurs de recherche et les IA.
 * 
 * Types implémentés :
 *   - SoftwareApplication (le plugin IA1)
 *   - Organization (R2C SYSTEM SAS)
 *   - FAQPage (automatique sur les pages contenant [ia1_faq])
 * 
 * @package IA1
 * @since   3.2.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Hook principal : injecte le JSON-LD dans le <head>
 */
add_action( 'wp_head', 'ia1_inject_schema_jsonld', 5 );

function ia1_inject_schema_jsonld() {

    $schemas = [];

    // -------------------------------------------------------
    // 1. SoftwareApplication — toujours présent
    // -------------------------------------------------------
    $schemas[] = [
        '@context'            => 'https://schema.org',
        '@type'               => 'SoftwareApplication',
        'name'                => 'IA1 – Intelligence Artificielle Locale',
        'alternateName'       => 'IA1',
        'description'         => 'Extension WordPress d\'IA conversationnelle locale basée sur RAG (Retrieval-Augmented Generation). IA1 répond uniquement à partir des contenus de votre site, sans chercher sur Internet, avec Mistral AI. Données hébergées en Europe, conformité RGPD, open source.',
        'url'                 => 'https://ia1.fr',
        'downloadUrl'         => 'https://github.com/Jean-Christophe-Gilbert/ia1-plugin/archive/refs/heads/main.zip',
        'softwareVersion'     => '3.2.4',
        'applicationCategory' => 'BusinessApplication',
        'applicationSubCategory' => 'Artificial Intelligence',
        'operatingSystem'     => 'WordPress 6.0+',
        'softwareRequirements'=> 'WordPress, PHP 7.4+, Clé API Mistral AI',
        'keywords'            => 'intelligence artificielle locale, RAG, WordPress, Mistral AI, chatbot, souveraineté numérique, RGPD, France',
        'inLanguage'          => 'fr',
        'license'             => 'https://opensource.org/licenses/MIT',
        'isAccessibleForFree' => true,
        'offers'              => [
            [
                '@type'         => 'Offer',
                'name'          => 'Version open source',
                'price'         => '0',
                'priceCurrency' => 'EUR',
                'description'   => 'Extension complète, toutes fonctionnalités, auto-hébergée',
            ],
            [
                '@type'         => 'Offer',
                'name'          => 'Essentiel',
                'price'         => '45',
                'priceCurrency' => 'EUR',
                'billingIncrement' => 'P1M',
                'description'   => 'Support prioritaire, configuration par nos soins, mises à jour automatiques, 1h de formation',
            ],
            [
                '@type'         => 'Offer',
                'name'          => 'Pro',
                'price'         => '99',
                'priceCurrency' => 'EUR',
                'billingIncrement' => 'P1M',
                'description'   => 'Accompagnement complet, 3h de formation, optimisation des contenus',
            ],
        ],
        'creator' => [
            '@type'       => 'Organization',
            'name'        => 'R2C SYSTEM SAS',
            'alternateName' => 'IA1',
            'url'         => 'https://ia1.fr',
            'email'       => 'jc@ia1.fr',
            'address'     => [
                '@type'           => 'PostalAddress',
                'addressLocality' => 'Niort',
                'addressRegion'   => 'Nouvelle-Aquitaine',
                'addressCountry'  => 'FR',
            ],
            'founder' => [
                '@type' => 'Person',
                'name'  => 'Jean-Christophe Gilbert',
            ],
            'sameAs' => [
                'https://github.com/Jean-Christophe-Gilbert/ia1-plugin',
            ],
        ],
        'screenshot'  => 'https://ia1.fr/wp-content/uploads/ia1-screenshot.png',
        'sameAs'      => [
            'https://github.com/Jean-Christophe-Gilbert/ia1-plugin',
        ],
    ];

    // -------------------------------------------------------
    // 2. Organization — toujours présent
    // -------------------------------------------------------
    $schemas[] = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Organization',
        'name'        => 'R2C SYSTEM SAS',
        'alternateName' => 'IA1',
        'url'         => 'https://ia1.fr',
        'logo'        => 'https://ia1.fr/wp-content/uploads/ia1-logo.png',
        'email'       => 'jc@ia1.fr',
        'description' => 'Éditeur du plugin WordPress IA1 – IA conversationnelle locale et souveraine, artisanale, cultivée à Niort.',
        'address'     => [
            '@type'           => 'PostalAddress',
            'addressLocality' => 'Niort',
            'addressRegion'   => 'Nouvelle-Aquitaine',
            'postalCode'      => '79000',
            'addressCountry'  => 'FR',
        ],
        'areaServed'  => 'FR',
        'knowsAbout'  => [
            'Intelligence artificielle locale',
            'Souveraineté numérique',
            'WordPress',
            'Mistral AI',
            'RAG – Retrieval-Augmented Generation',
        ],
        'sameAs' => [
            'https://github.com/Jean-Christophe-Gilbert/ia1-plugin',
            'https://jcgilbert.fr',
        ],
    ];

    // -------------------------------------------------------
    // 3. FAQPage — uniquement sur la page d'accueil
    //    (détecté par is_front_page OU présence du shortcode [ia1_faq])
    // -------------------------------------------------------
    if ( is_front_page() || ia1_page_has_shortcode( 'ia1_faq' ) ) {

        $faq_items = ia1_get_faq_schema_items();

        if ( ! empty( $faq_items ) ) {
            $schemas[] = [
                '@context'   => 'https://schema.org',
                '@type'      => 'FAQPage',
                'mainEntity' => $faq_items,
            ];
        }
    }

    // -------------------------------------------------------
    // Rendu : une balise <script> par schema
    // -------------------------------------------------------
    foreach ( $schemas as $schema ) {
        echo "\n" . '<script type="application/ld+json">' . "\n";
        echo wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
        echo "\n" . '</script>' . "\n";
    }
}

/**
 * Vérifie si le contenu de la page courante contient un shortcode donné.
 *
 * @param  string $shortcode
 * @return bool
 */
function ia1_page_has_shortcode( $shortcode ) {
    global $post;
    if ( ! $post ) {
        return false;
    }
    return has_shortcode( $post->post_content, $shortcode );
}

/**
 * Retourne les questions/réponses de la FAQ au format Schema.org.
 * 
 * Les items sont définis ici en dur pour garantir la qualité SEO.
 * À faire évoluer si la FAQ devient dynamique.
 *
 * @return array
 */
function ia1_get_faq_schema_items() {
    return [
        [
            '@type'          => 'Question',
            'name'           => 'C\'est quoi IA1 ?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => 'IA1 est une intelligence artificielle de connaissance locale qui répond uniquement à partir des contenus de votre site WordPress (articles, pages, produits). Elle n\'invente rien, ne cherche pas sur Internet, et reste 100% fidèle à vos contenus.',
            ],
        ],
        [
            '@type'          => 'Question',
            'name'           => 'Pour qui est fait IA1 ?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => 'Pour les sites WordPress qui veulent aider leurs visiteurs à trouver rapidement des informations : entreprises locales, associations, collectivités, e-commerce, médias. Si vous avez du contenu à valoriser, IA1 est fait pour vous.',
            ],
        ],
        [
            '@type'          => 'Question',
            'name'           => 'Combien ça coûte ?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => 'L\'extension est open source et gratuite. Vous payez uniquement votre consommation Mistral AI (qui offre des crédits gratuits pour tester). Pour un accompagnement pro, des formules d\'abonnement sont disponibles à partir de 45€/mois.',
            ],
        ],
        [
            '@type'          => 'Question',
            'name'           => 'Quelle est la différence avec ChatGPT ?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => 'ChatGPT connaît tout Internet mais ne connaît pas votre site en profondeur. IA1 est l\'inverse : il connaît parfaitement votre site mais uniquement votre site. Il cite toujours ses sources avec des liens directs.',
            ],
        ],
        [
            '@type'          => 'Question',
            'name'           => 'Est-ce conforme RGPD ?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => 'Oui. IA1 ne collecte aucune donnée personnelle des visiteurs, n\'utilise pas de cookies, et ne conserve aucun historique de conversations. Les données restent hébergées sur votre serveur WordPress.',
            ],
        ],
        [
            '@type'          => 'Question',
            'name'           => 'Est-ce compliqué à installer ?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => 'Non. C\'est une extension WordPress classique. Il vous faut : 1) créer un compte Mistral AI gratuit, 2) télécharger l\'extension, 3) coller votre clé API. Temps total : environ 5 minutes.',
            ],
        ],
        [
            '@type'          => 'Question',
            'name'           => 'Où sont stockées mes données ?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => 'Sur votre serveur WordPress. IA1 n\'exporte rien, ne stocke rien ailleurs. Vos données ne quittent jamais votre infrastructure. Mistral AI est hébergé en Europe.',
            ],
        ],
        [
            '@type'          => 'Question',
            'name'           => 'Est-ce que IA1 modifie mon site ?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => 'Non. IA1 est en lecture seule. Il lit vos contenus pour répondre aux questions mais ne modifie jamais rien sur votre site.',
            ],
        ],
    ];
}
