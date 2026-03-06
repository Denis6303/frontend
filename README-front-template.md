# Frontend Votix - Intégration template billeterie

Ce projet est un frontend Laravel + Vite pour la billeterie évènementielle Votix, qui intégrera un système de vote ultérieurement.

## Structure principale liée au template

- `resources/views/layouts/app.blade.php` : layout global (head, header, footer, `@yield('content')`).
- `resources/views/partials/header.blade.php` et `resources/views/partials/footer.blade.php` : éléments communs, à remplacer par le header/footer du template.
- `resources/views/ticketing/*.blade.php` :
  - `home.blade.php` : accueil billeterie.
  - `events.blade.php` : listing des événements.
  - `event-detail.blade.php` : détail d’un événement + bloc réservé au module de vote (`#vote-root`).
  - `cart.blade.php` : page panier.
- `resources/css/template/` : futurs fichiers CSS du template.
- `resources/js/template/` : futurs fichiers JS du template.
- `resources/assets/template/` : images, icônes, fonts du template.
- `resources/js/vote/index.js` : point d’entrée JS du module de vote (placeholder).

## Vite

`vite.config.js` est configuré avec les entrées classiques Laravel (`resources/css/app.css`, `resources/js/app.js`) et peut être étendu avec des entrées spécifiques au template (CSS/JS) dès que les fichiers seront ajoutés.

## Routes billeterie

Les routes web principales liées à la billeterie se trouvent dans `routes/web.php` :

- `/` (`home`) : vue `welcome` qui utilise désormais le layout global.
- `/ticketing` (`ticketing.home`) : accueil billeterie.
- `/ticketing/events` (`ticketing.events`) : listing des événements.
- `/ticketing/events/{id}` (`ticketing.events.show`) : détail d’un événement.
- `/ticketing/cart` (`ticketing.cart`) : panier.

## Intégration du template

Lorsque tu ajouteras le template (HTML/CSS/JS/assets) :

1. Copie les CSS/SCSS dans `resources/css/template/` et les JS dans `resources/js/template/`.
2. Copie les images/icônes/fonts dans `resources/assets/template/` (ou `public/` si nécessaire).
3. Branche les fichiers dans `vite.config.js` en les ajoutant dans `input`.
4. Remplace progressivement le contenu des vues `ticketing/*` et des partials `header`/`footer` par le HTML du template, en gardant `@extends('layouts.app')` et les sections Blade.
5. Monte le module de vote via `resources/js/vote/index.js` sur les pages qui en ont besoin (par ex. `event-detail.blade.php`).

