// Point d’entrée du module de vote.
// Ce fichier sera enrichi lorsque le backend de vote sera connecté.

export function mountVoteWidget(rootElementId = 'vote-root') {
    const root = document.getElementById(rootElementId);
    if (!root) {
        return;
    }

    // Placeholder simple en attendant la vraie UI de vote.
    root.innerHTML = '<p>Module de vote à venir…</p>';
}

