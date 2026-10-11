import './bootstrap';
import Alpine from 'alpinejs';

Alpine.store('theme', {
    dark: localStorage.getItem('color-theme') === 'dark' || localStorage.getItem('theme') === 'dark' || (!('color-theme' in localStorage) && !('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
    init() {
        if (this.dark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    },
    toggle() {
        this.dark = !this.dark;
        if (this.dark) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('color-theme', 'dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('color-theme', 'light');
            localStorage.setItem('theme', 'light');
        }
    }
});

window.Alpine = Alpine;
Alpine.start();

