<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dashboard\EventDraftController;
use App\Http\Controllers\Dashboard\MyEventController;
use App\Http\Controllers\Ticketing\EventController as PublicEventController;
use App\Http\Controllers\Ticketing\OrderIntentController;
use Illuminate\Support\Facades\Route;

// Changement de langue (sans préfixe locale)
Route::get('/locale/{locale}', [\App\Http\Controllers\LocaleController::class, 'switch'])
    ->name('locale.switch')
    ->where(['locale' => 'fr|en']);

// Groupe principal avec locale dans l'URL
Route::middleware('setlocale')->prefix('{locale}')->where(['locale' => 'fr|en'])->group(function () {
    $staticPageData = static function (string $locale, string $page): array {
        $isFr = $locale === 'fr';
        $contactUrl = route('contact', ['locale' => $locale]);
        $faqUrl = route('static.faq', ['locale' => $locale]);
        $sellUrl = route('static.sell', ['locale' => $locale]);
        $pricingUrl = route('static.pricing', ['locale' => $locale]);
        $registerUrl = route('register', ['locale' => $locale]);

        return match ($page) {
            'about' => [
                'summary' => $isFr
                    ? 'Votix est une plateforme de billetterie moderne pour créer, vendre et gérer vos événements.'
                    : 'Votix is a modern ticketing platform to create, sell, and manage your events.',
                'sections' => [
                    [
                        'heading' => $isFr ? 'Notre mission' : 'Our mission',
                        'body' => $isFr
                            ? 'Aider les organisateurs à se concentrer sur leur public pendant que nous gérons la technologie de billetterie.'
                            : 'Help organizers focus on their audience while we handle ticketing technology.',
                    ],
                    [
                        'heading' => $isFr ? 'Pour qui ?' : 'Who is it for?',
                        'list' => $isFr
                            ? ['Concerts et festivals', 'Conférences et ateliers', 'Associations et communautés']
                            : ['Concerts and festivals', 'Conferences and workshops', 'Associations and communities'],
                    ],
                ],
                'cta' => [
                    'title' => $isFr ? 'Prêt à lancer votre prochain événement ?' : 'Ready to launch your next event?',
                    'text' => $isFr ? 'Créez votre compte et publiez votre premier événement en quelques minutes.' : 'Create your account and publish your first event in minutes.',
                    'label' => $isFr ? 'Créer un compte' : 'Create account',
                    'url' => $registerUrl,
                ],
            ],
            'help' => [
                'summary' => $isFr
                    ? 'Le centre d’aide regroupe les ressources essentielles pour organisateurs et acheteurs.'
                    : 'The help center gathers essential resources for organizers and ticket buyers.',
                'sections' => [
                    [
                        'heading' => $isFr ? 'Ressources principales' : 'Main resources',
                        'list' => $isFr
                            ? ['FAQ : réponses rapides aux questions fréquentes', 'Support : aide compte, paiement et accès', 'Documentation : bonnes pratiques de mise en ligne']
                            : ['FAQ: quick answers to common questions', 'Support: account, payment and access help', 'Documentation: publication best practices'],
                    ],
                ],
                'cta' => [
                    'title' => $isFr ? 'Besoin d’aide personnalisée ?' : 'Need personalized help?',
                    'text' => $isFr ? 'Contactez notre équipe et décrivez votre besoin.' : 'Contact our team and describe your issue.',
                    'label' => $isFr ? 'Nous contacter' : 'Contact us',
                    'url' => $contactUrl,
                ],
            ],
            'faq' => [
                'summary' => $isFr
                    ? 'Les réponses aux questions les plus posées sur les billets, paiements et comptes.'
                    : 'Answers to the most common questions about tickets, payments, and accounts.',
                'sections' => [
                    [
                        'heading' => $isFr ? 'Achat et billets' : 'Purchase and tickets',
                        'list' => $isFr
                            ? ['Après paiement, vos billets sont envoyés par e-mail.', 'Retrouvez vos billets dans votre espace personnel.', 'Les conditions de remboursement dépendent de l’organisateur.']
                            : ['After payment, your tickets are sent by email.', 'You can also find tickets in your account.', 'Refund policy depends on the organizer.'],
                    ],
                    [
                        'heading' => $isFr ? 'Comptes et sécurité' : 'Accounts and security',
                        'list' => $isFr
                            ? ['Connexion classique ou sociale disponible.', 'Les paiements sont traités via des partenaires certifiés.']
                            : ['Standard or social login is available.', 'Payments are processed via certified partners.'],
                    ],
                ],
                'cta' => [
                    'title' => $isFr ? 'Question non couverte ?' : 'Question not covered?',
                    'text' => $isFr ? 'Écrivez-nous et nous vous répondrons rapidement.' : 'Write to us and we will get back quickly.',
                    'label' => $isFr ? 'Contacter le support' : 'Contact support',
                    'url' => $contactUrl,
                ],
            ],
            'contact' => [
                'summary' => $isFr
                    ? 'Notre équipe accompagne les organisateurs et les acheteurs sur tous les sujets opérationnels. Choisissez le canal qui vous convient.'
                    : 'Our team supports organizers and ticket buyers on all operational topics. Pick the channel that works best for you.',
                'cards' => [
                    [
                        'title' => $isFr ? 'Email principal' : 'Main email',
                        'text' => $isFr ? 'Pour toute demande générale, partenariat ou support.' : 'For general requests, partnerships, or support.',
                        'href' => 'mailto:contact@votxevent.com',
                        'link_label' => 'contact@votxevent.com',
                        'icon' => 'fa-envelope',
                    ],
                    [
                        'title' => $isFr ? 'Ligne support 1' : 'Support line 1',
                        'text' => $isFr ? 'Assistance rapide organisateurs et participants.' : 'Fast help for organizers and attendees.',
                        'href' => 'tel:+22890190060',
                        'link_label' => '+228 90 19 00 60',
                        'icon' => 'fa-phone',
                    ],
                    [
                        'title' => $isFr ? 'Ligne support 2' : 'Support line 2',
                        'text' => $isFr ? 'Suivi commandes et accès billets.' : 'Order follow-up and ticket access.',
                        'href' => 'tel:+22879197635',
                        'link_label' => '+228 79 19 76 35',
                        'icon' => 'fa-headset',
                    ],
                    [
                        'title' => $isFr ? 'Ligne support 3' : 'Support line 3',
                        'text' => $isFr ? 'Aide publication événement et configuration.' : 'Help with event publishing and setup.',
                        'href' => 'tel:+22890491506',
                        'link_label' => '+228 90 49 15 06',
                        'icon' => 'fa-ticket',
                    ],
                    [
                        'title' => $isFr ? 'Ligne support 4' : 'Support line 4',
                        'text' => $isFr ? 'Demandes techniques et suivi prioritaire.' : 'Technical requests and priority follow-up.',
                        'href' => 'tel:+22890491534',
                        'link_label' => '+228 90 49 15 34',
                        'icon' => 'fa-screwdriver-wrench',
                    ],
                ],
                'sections' => [
                    [
                        'heading' => $isFr ? 'Conseils pour un traitement rapide' : 'Tips for faster handling',
                        'body' => $isFr
                            ? 'Indiquez votre référence de commande, le nom de l’événement et une description claire du besoin.'
                            : 'Include your order reference, event name, and a clear description of your request.',
                    ],
                    [
                        'list' => $isFr
                            ? ['Réponse prioritaire pour les événements en cours', 'Support disponible pour organisateurs et acheteurs']
                            : ['Priority response for live events', 'Support available for organizers and buyers'],
                    ],
                ],
                'cta' => [
                    'title' => $isFr ? 'Besoin d’un accompagnement complet ?' : 'Need end-to-end support?',
                    'text' => $isFr ? 'Notre équipe peut vous aider à préparer votre événement de A à Z.' : 'Our team can help you prepare your event from start to finish.',
                    'label' => $isFr ? 'Écrire à contact@votxevent.com' : 'Write to contact@votxevent.com',
                    'url' => 'mailto:contact@votxevent.com',
                ],
            ],
            'sell' => [
                'summary' => $isFr
                    ? 'Vendez vos billets en ligne avec une expérience fluide pour mobile et desktop.'
                    : 'Sell your tickets online with a smooth mobile and desktop experience.',
                'sections' => [
                    [
                        'heading' => $isFr ? 'Ce que vous pouvez faire avec Votix' : 'What you can do with Votix',
                        'list' => $isFr
                            ? ['Créer des pages événement optimisées', 'Configurer billets gratuits et payants', 'Suivre les ventes et commandes en temps réel']
                            : ['Create optimized event pages', 'Configure free and paid tickets', 'Track sales and orders in real time'],
                    ],
                ],
                'cta' => [
                    'title' => $isFr ? 'Commencer maintenant' : 'Get started now',
                    'text' => $isFr ? 'Créez votre compte organisateur et publiez votre premier événement.' : 'Create your organizer account and publish your first event.',
                    'label' => $isFr ? 'Créer mon compte' : 'Create my account',
                    'url' => $registerUrl,
                ],
            ],
            'privacy' => [
                'summary' => $isFr
                    ? 'Votre confiance est essentielle. Cette politique décrit de manière claire les données traitées, pourquoi elles le sont et vos droits.'
                    : 'Your trust matters. This policy clearly explains which data is processed, why it is processed, and your rights.',
                'cards' => [
                    [
                        'title' => $isFr ? 'Protection des données' : 'Data protection',
                        'text' => $isFr ? 'Mesures techniques et organisationnelles pour protéger vos informations.' : 'Technical and organizational safeguards to protect your information.',
                        'icon' => 'fa-shield-halved',
                    ],
                    [
                        'title' => $isFr ? 'Contrôle utilisateur' : 'User control',
                        'text' => $isFr ? 'Accès, rectification, suppression : vous gardez le contrôle.' : 'Access, correction, deletion: you stay in control.',
                        'icon' => 'fa-user-check',
                    ],
                    [
                        'title' => $isFr ? 'Transparence' : 'Transparency',
                        'text' => $isFr ? 'Nous expliquons les usages essentiels des données de façon lisible.' : 'We explain essential data usage in plain language.',
                        'icon' => 'fa-circle-info',
                    ],
                ],
                'sections' => [
                    [
                        'heading' => $isFr ? 'Données collectées' : 'Data collected',
                        'body' => $isFr
                            ? 'Nous collectons les informations de compte (nom, e-mail, téléphone), les données liées aux commandes, et certains éléments techniques (logs, appareil, sécurité de session).'
                            : 'We collect account information (name, email, phone), order-related data, and technical elements (logs, device, session security).',
                    ],
                    [
                        'heading' => $isFr ? 'Finalités du traitement' : 'Purpose of processing',
                        'body' => $isFr
                            ? 'Ces données servent à gérer les achats de billets, envoyer les confirmations, prévenir la fraude, assurer le support client et améliorer les performances de la plateforme.'
                            : 'This data is used to process ticket purchases, send confirmations, prevent fraud, provide support, and improve platform performance.',
                    ],
                    [
                        'heading' => $isFr ? 'Conservation et sécurité' : 'Retention and security',
                        'body' => $isFr
                            ? 'Les données sont conservées uniquement le temps nécessaire à nos obligations légales et opérationnelles. Nous appliquons des contrôles d’accès et des mesures de sécurisation adaptées.'
                            : 'Data is retained only for as long as necessary for legal and operational obligations. We apply access controls and appropriate security measures.',
                    ],
                    [
                        'heading' => $isFr ? 'Vos droits' : 'Your rights',
                        'body' => $isFr
                            ? 'Vous pouvez demander l’accès, la rectification, la suppression ou la limitation du traitement de vos données, selon la réglementation en vigueur.'
                            : 'You may request access, correction, deletion, or processing limitation, according to applicable regulations.',
                    ],
                ],
                'cta' => [
                    'title' => $isFr ? 'Exercer vos droits' : 'Exercise your rights',
                    'text' => $isFr ? 'Pour toute demande relative aux données personnelles, contactez-nous par e-mail.' : 'For any personal data request, contact us by email.',
                    'label' => $isFr ? 'Contacter : contact@votxevent.com' : 'Contact: contact@votxevent.com',
                    'url' => 'mailto:contact@votxevent.com',
                ],
            ],
            'terms' => [
                'summary' => $isFr
                    ? 'Les conditions générales encadrent l’utilisation de la plateforme Votix.'
                    : 'Terms and conditions define how Votix platform is used.',
                'sections' => [
                    [
                        'heading' => $isFr ? 'Service proposé' : 'Provided service',
                        'body' => $isFr
                            ? 'Votix met à disposition une solution de découverte d’événements et de vente de billets en ligne.'
                            : 'Votix provides an online event discovery and ticketing solution.',
                    ],
                    [
                        'heading' => $isFr ? 'Responsabilités' : 'Responsibilities',
                        'body' => $isFr
                            ? 'Les organisateurs restent responsables du contenu, de la tenue de leurs événements et des politiques de remboursement.'
                            : 'Organizers remain responsible for content, event delivery, and refund policies.',
                    ],
                    [
                        'heading' => $isFr ? 'Évolutions' : 'Updates',
                        'body' => $isFr
                            ? 'Ces conditions peuvent évoluer ; l’usage continu de la plateforme vaut acceptation.'
                            : 'These terms may evolve; continued platform usage implies acceptance.',
                    ],
                ],
            ],
            'pricing' => [
                'summary' => $isFr
                    ? 'Une tarification transparente adaptée à votre volume de ventes.'
                    : 'Transparent pricing adapted to your sales volume.',
                'sections' => [
                    [
                        'heading' => $isFr ? 'Principes tarifaires' : 'Pricing principles',
                        'list' => $isFr
                            ? ['Création de compte gratuite', 'Frais liés à la publication/vente selon configuration', 'Tarification personnalisée pour grands volumes']
                            : ['Free account creation', 'Publishing/sales fees depending on setup', 'Custom pricing for high volume events'],
                    ],
                ],
                'cta' => [
                    'title' => $isFr ? 'Besoin d’une offre sur mesure ?' : 'Need a custom offer?',
                    'text' => $isFr ? 'Contactez-nous pour une proposition adaptée à votre activité.' : 'Contact us for a tailored proposal.',
                    'label' => $isFr ? 'Parler à l’équipe' : 'Talk to the team',
                    'url' => $contactUrl,
                ],
            ],
            default => [],
        };
    };

    // Auth (routes détaillées dans routes/auth.php)
    require __DIR__ . '/auth.php';

    // Home publique
    Route::get('/', [PublicEventController::class, 'home'])->name('home');

    // Ticketing (liste + ancien détail)
    Route::prefix('ticketing')->name('ticketing.')->group(function () {
        Route::get('/', [PublicEventController::class, 'home'])->name('index');
        Route::get('/events', [PublicEventController::class, 'index'])->name('events');
        Route::get('/events/{id}', [PublicEventController::class, 'showLegacy'])->name('events.show.legacy');
        Route::get('/cart', function (string $locale) {
            return view('ticketing.cart');
        })->name('cart');

        Route::middleware(['auth'])->prefix('checkout')->name('checkout.')->group(function () {
            Route::post('/prepare', [OrderIntentController::class, 'prepare'])->name('prepare');
            Route::get('/{key}/return', [OrderIntentController::class, 'returnPage'])
                ->where('key', '[0-9a-fA-F-]{36}')
                ->name('return');
            Route::post('/{key}/cancel', [OrderIntentController::class, 'cancel'])
                ->where('key', '[0-9a-fA-F-]{36}')
                ->name('cancel');
            Route::post('/{key}/pay', [OrderIntentController::class, 'pay'])
                ->where('key', '[0-9a-fA-F-]{36}')
                ->name('pay');
            Route::get('/{key}', [OrderIntentController::class, 'show'])
                ->where('key', '[0-9a-fA-F-]{36}')
                ->name('show');
        });
    });

    // Nouveau détail public : /{locale}/evenements/{slug}
    Route::get('/evenements/{slug}', [PublicEventController::class, 'show'])
        ->name('events.show');

    // Tableau de bord (utilisateur connecté) : routes/dashboard/*.php
    // Chargées automatiquement sous middleware `auth` (voir prompt).
    Route::middleware(['auth'])->group(function () {
        foreach (glob(base_path('routes/dashboard/*.php')) as $routeFile) {
            require $routeFile;
        }
    });

    // Pages statiques (footer)
    Route::get('/a-propos', function (string $locale) use ($staticPageData) {
        return view('pages.static', ['locale' => $locale, 'page' => 'about', 'title' => __('About Us'), 'pageData' => $staticPageData($locale, 'about')]);
    })->name('static.about');

    Route::get('/centre-aide', function (string $locale) use ($staticPageData) {
        return view('pages.static', ['locale' => $locale, 'page' => 'help', 'title' => __('Help Center'), 'pageData' => $staticPageData($locale, 'help')]);
    })->name('static.help');

    Route::get('/faq', function (string $locale) use ($staticPageData) {
        return view('pages.static', ['locale' => $locale, 'page' => 'faq', 'title' => __('FAQ'), 'pageData' => $staticPageData($locale, 'faq')]);
    })->name('static.faq');

    Route::get('/nous-contacter', function (string $locale) use ($staticPageData) {
        return view('pages.static', ['locale' => $locale, 'page' => 'contact', 'title' => __('Contact Us'), 'pageData' => $staticPageData($locale, 'contact')]);
    })->name('contact');

    Route::get('/vendre-billets-en-ligne', function (string $locale) use ($staticPageData) {
        return view('pages.static', ['locale' => $locale, 'page' => 'sell', 'title' => __('Sell Tickets Online'), 'pageData' => $staticPageData($locale, 'sell')]);
    })->name('static.sell');

    Route::get('/confidentialite', function (string $locale) use ($staticPageData) {
        return view('pages.static', ['locale' => $locale, 'page' => 'privacy', 'title' => __('Privacy Policy'), 'pageData' => $staticPageData($locale, 'privacy')]);
    })->name('static.privacy');

    Route::get('/conditions-generales', function (string $locale) use ($staticPageData) {
        return view('pages.static', ['locale' => $locale, 'page' => 'terms', 'title' => __('Terms & Conditions'), 'pageData' => $staticPageData($locale, 'terms')]);
    })->name('static.terms');

    Route::get('/tarifs', function (string $locale) use ($staticPageData) {
        return view('pages.static', ['locale' => $locale, 'page' => 'pricing', 'title' => __('Pricing'), 'pageData' => $staticPageData($locale, 'pricing')]);
    })->name('static.pricing');
}
);

