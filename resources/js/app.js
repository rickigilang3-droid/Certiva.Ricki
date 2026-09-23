import Alpine from 'alpinejs';

window.Alpine = Alpine;

const getInitialTheme = () => {
    const saved = localStorage.getItem('certiva_theme');
    if (saved === 'dark') return true;
    if (saved === 'light') return false;
    return Boolean(window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
};

document.addEventListener('alpine:init', () => {
    Alpine.store('theme', {
        isDark: getInitialTheme(),
        init() {
            this.apply();
        },
        apply() {
            if (this.isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        },
        toggle() {
            this.isDark = !this.isDark;
            localStorage.setItem('certiva_theme', this.isDark ? 'dark' : 'light');
            this.apply();
        }
    });

    // Automatically synchronize theme state across tabs/windows in real time
    window.addEventListener('storage', (e) => {
        if (e.key === 'certiva_theme') {
            const store = Alpine.store('theme');
            if (store) {
                store.isDark = (e.newValue === 'dark');
                store.apply();
            }
        }
    });
});

Alpine.start();
