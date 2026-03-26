import { Component, signal, computed } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { toSignal } from '@angular/core/rxjs-interop';
import { Sidebar } from '../sidebar/sidebar';

export interface User {
  id: number;
  name: string;
  email: string;
  department: string;
  role: 'Admin' | 'Researcher' | 'Viewer';
  status: 0 | 1 | 2; // 0: inactive, 1: active, 2: pending
  avatar: string;
}

@Component({
  selector: 'app-user-management',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, Sidebar],
  templateUrl: './user-management.html',
  styleUrl: './user-management.css',
})
export class UserManagement {
  // Mock Data
  mockUsers = signal<User[]>([
    {
      id: 1,
      name: "Dr. Somchai Kaewprom",
      email: "somchai.k@up.ac.th",
      department: "Computer Science",
      role: "Admin",
      status: 1,
      avatar: "SK",
    },
    {
      id: 2,
      name: "Prof. Wichai Srisuk",
      email: "wichai.s@up.ac.th",
      department: "Information Technology",
      role: "Researcher",
      status: 1,
      avatar: "WS",
    },
    {
      id: 3,
      name: "Asst. Prof. Nattapong Meesa",
      email: "nattapong.m@up.ac.th",
      department: "Data Science",
      role: "Researcher",
      status: 1,
      avatar: "NM",
    },
    {
      id: 4,
      name: "Dr. Kittisak Panya",
      email: "kittisak.p@up.ac.th",
      department: "Software Engineering",
      role: "Researcher",
      status: 2,
      avatar: "KP",
    },
    {
      id: 5,
      name: "Dr. Supachai Wong",
      email: "supachai.w@up.ac.th",
      department: "Cybersecurity",
      role: "Viewer",
      status: 0,
      avatar: "SW",
    },
    {
      id: 6,
      name: "Prof. Chaiyaporn Musikapong",
      email: "chaiyaporn.m@up.ac.th",
      department: "Computer Science",
      role: "Researcher",
      status: 1,
      avatar: "CM",
    },
  ]);

  // Form Controls
  searchControl = new FormControl('');
  roleFilter = new FormControl('all');
  statusFilter = new FormControl('all');

  // Signals derived from Form Controls
  searchQuery = toSignal(this.searchControl.valueChanges, { initialValue: '' });
  selectedRole = toSignal(this.roleFilter.valueChanges, { initialValue: 'all' });
  selectedStatus = toSignal(this.statusFilter.valueChanges, { initialValue: 'all' });

  // Modal State
  editModalOpen = signal(false);
  selectedUser = signal<User | null>(null);

  // Computed Values
  filteredUsers = computed(() => {
    const query = (this.searchQuery() ?? '').toLowerCase();
    const role = this.selectedRole() ?? 'all';
    const status = this.selectedStatus() ?? 'all';

    return this.mockUsers().filter((user) => {
      const matchesSearch = user.name.toLowerCase().includes(query) ||
        user.email.toLowerCase().includes(query);
      const matchesRole = role === 'all' || user.role === role;
      const matchesStatus = status === 'all' ||
        (status === 'active' && user.status === 1) ||
        (status === 'inactive' && user.status === 0) ||
        (status === 'pending' && user.status === 2);
      return matchesSearch && matchesRole && matchesStatus;
    });
  });

  stats = computed(() => {
    const users = this.mockUsers();
    return {
      total: users.length,
      active: users.filter(u => u.status === 1).length,
      inactive: users.filter(u => u.status === 0).length,
      pending: users.filter(u => u.status === 2).length,
    };
  });

  getStatusLabel(status: number): string {
    switch (status) {
      case 1: return 'active';
      case 0: return 'inactive';
      case 2: return 'pending';
      default: return 'unknown';
    }
  }

  // Event Handlers
  handleEdit(user: User) {
    this.selectedUser.set(user);
    this.editModalOpen.set(true);
  }

  closeEditModal() {
    this.editModalOpen.set(false);
    this.selectedUser.set(null);
  }

  onView(user: User) { console.log("View", user); }

  onApprove(user: User) {
    this.mockUsers.update(users =>
      users.map(u => u.id === user.id ? { ...u, status: 1 } : u)
    );
  }

  onStatusChange(user: User, event: any) {
    const newStatus = Number(event.target.value) as (0 | 1 | 2);
    this.mockUsers.update(users =>
      users.map(u => u.id === user.id ? { ...u, status: newStatus } : u)
    );
  }

  onDelete(user: User) {
    this.mockUsers.update(users => users.filter(u => u.id !== user.id));
  }

  onRoleChange(user: User, event: any) {
    const newRole = event.target.value;
    this.mockUsers.update(users =>
      users.map(u => u.id === user.id ? { ...u, role: newRole } : u)
    );
  }

  onChangeRole(user: User) { console.log("Change role", user); }

  onSubmitEdit(user: User, data: any) {
    this.mockUsers.update(users =>
      users.map(u => u.id === user.id ? {
        ...u,
        role: data.role,
        status: Number(data.status) as (0 | 1 | 2)
      } : u)
    );
    this.closeEditModal();
  }
}

