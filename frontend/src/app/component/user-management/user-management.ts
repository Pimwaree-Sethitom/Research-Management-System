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
  status: 'active' | 'inactive' | 'pending';
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
      status: "active",
      avatar: "SK",
    },
    {
      id: 2,
      name: "Prof. Wichai Srisuk",
      email: "wichai.s@up.ac.th",
      department: "Information Technology",
      role: "Researcher",
      status: "active",
      avatar: "WS",
    },
    {
      id: 3,
      name: "Asst. Prof. Nattapong Meesa",
      email: "nattapong.m@up.ac.th",
      department: "Data Science",
      role: "Researcher",
      status: "active",
      avatar: "NM",
    },
    {
      id: 4,
      name: "Dr. Kittisak Panya",
      email: "kittisak.p@up.ac.th",
      department: "Software Engineering",
      role: "Researcher",
      status: "pending",
      avatar: "KP",
    },
    {
      id: 5,
      name: "Dr. Supachai Wong",
      email: "supachai.w@up.ac.th",
      department: "Cybersecurity",
      role: "Viewer",
      status: "inactive",
      avatar: "SW",
    },
    {
      id: 6,
      name: "Prof. Chaiyaporn Musikapong",
      email: "chaiyaporn.m@up.ac.th",
      department: "Computer Science",
      role: "Researcher",
      status: "active",
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
      const matchesStatus = status === 'all' || user.status === status;
      return matchesSearch && matchesRole && matchesStatus;
    });
  });

  stats = computed(() => {
    const users = this.mockUsers();
    return {
      total: users.length,
      active: users.filter(u => u.status === "active").length,
      inactive: users.filter(u => u.status === "inactive").length,
      pending: users.filter(u => u.status === "pending").length,
    };
  });

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
  onDelete(user: User) { console.log("Delete", user); }
  onChangeRole(user: User) { console.log("Change role", user); }
  onSubmitEdit(data: any) {
    console.log("Submit", data);
    this.closeEditModal();
  }
}

