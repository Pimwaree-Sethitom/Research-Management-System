import { Component, signal } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-sidebar',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './sidebar.html',
  styleUrl: './sidebar.css',
})
export class Sidebar {
  protected isSidebarCollapsed = signal(false);

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
}
