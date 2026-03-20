import { Component, signal, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ThemeService } from '../../services/theme.service';

@Component({
  selector: 'app-sidebar',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './sidebar.html',
  styleUrl: './sidebar.css',
})
export class Sidebar {
  protected isSidebarCollapsed = signal(false);
  private themeService = inject(ThemeService);
  protected isDarkMode = this.themeService.isDarkMode;

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
    this.themeService.toggleTheme();
  }
}
