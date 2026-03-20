import { Component, signal, Inject, PLATFORM_ID } from '@angular/core';
import { CommonModule, isPlatformBrowser } from '@angular/common';

@Component({
  selector: 'app-sidebar',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './sidebar.html',
  styleUrl: './sidebar.css',
})
export class Sidebar {
  protected isSidebarCollapsed = signal(false);
  protected isDarkMode = signal(true); // Default to dark mode as per mockup

  constructor(@Inject(PLATFORM_ID) private platformId: Object) {
    // Initial theme set - only in browser
    if (isPlatformBrowser(this.platformId)) {
      this.updateTheme();
    }
  }

  menuItems = [
    { name: 'Dashboard', icon: 'dashboard', path: '#' },
    { name: 'User Management', icon: 'users', path: '#' },
    { name: 'Research Management', icon: 'research', path: '#' },
    { name: 'Workload Management', icon: 'workload', path: '#' },
    { name: 'Research Types', icon: 'types', path: '#' },
    { name: 'Quartiles Management', icon: 'quartiles', path: '#' },
    { name: 'Settings', icon: 'settings', path: '#' },
  ];

  toggleSidebar() {
    this.isSidebarCollapsed.set(!this.isSidebarCollapsed());
  }

  toggleTheme() {
    this.isDarkMode.set(!this.isDarkMode());
    this.updateTheme();
  }

  private updateTheme() {
    if (!isPlatformBrowser(this.platformId)) return;

    if (this.isDarkMode()) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  }
}
