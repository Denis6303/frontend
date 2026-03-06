import './bootstrap';

// Night mode toggle based on template CSS (night-mode.css)
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('night-mode');
    if (!toggle) {
        return;
    }

    const root = document.documentElement;
    const NIGHT_MODE_CLASS = 'night-mode';

    const applyNightMode = (enabled) => {
        if (enabled) {
            root.classList.add(NIGHT_MODE_CLASS);
        } else {
            root.classList.remove(NIGHT_MODE_CLASS);
        }
    };

    // État initial depuis le localStorage
    const stored = window.localStorage.getItem('night-mode');
    if (stored === 'on') {
        applyNightMode(true);
    }

    toggle.addEventListener('click', () => {
        const enabled = !root.classList.contains(NIGHT_MODE_CLASS);
        applyNightMode(enabled);
        window.localStorage.setItem('night-mode', enabled ? 'on' : 'off');
    });
});
