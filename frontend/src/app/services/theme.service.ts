import { Injectable, signal, effect, inject, PLATFORM_ID } from '@angular/core';
import { isPlatformBrowser } from '@angular/common';

@Injectable({
    providedIn: 'root'
})
export class ThemeService {
    private platformId = inject(PLATFORM_ID);
    isDarkMode = signal<boolean>(true);

    constructor() {
        if (isPlatformBrowser(this.platformId)) {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme) {
                this.isDarkMode.set(savedTheme === 'dark');
            } else {
                // Optional: match system preference
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                this.isDarkMode.set(prefersDark);
            }
            this.updateDocument();
        }

        // Reactively update document class whenever isDarkMode changes
        effect(() => {
            this.updateDocument();
        });
    }

    toggleTheme() {
        this.isDarkMode.update(dark => !dark);
        if (isPlatformBrowser(this.platformId)) {
            localStorage.setItem('theme', this.isDarkMode() ? 'dark' : 'light');
        }
    }

    private updateDocument() {
        if (isPlatformBrowser(this.platformId)) {
            if (this.isDarkMode()) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    }
}
