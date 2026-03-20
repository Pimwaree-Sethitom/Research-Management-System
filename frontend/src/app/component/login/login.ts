import { Component, signal, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { ThemeService } from '../../services/theme.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './login.html',
  styleUrl: './login.css',
})
export class Login {
  protected email = signal('');
  protected password = signal('');
  protected showPassword = signal(false);
  protected loading = signal(false);
  protected focused = signal<string | null>(null);

  private themeService = inject(ThemeService);
  protected isDarkMode = this.themeService.isDarkMode;

  constructor(private router: Router) { }

  async onSubmit() {
    this.loading.set(true);
    await new Promise(resolve => setTimeout(resolve, 1500));
    this.loading.set(false);
    this.router.navigate(['/dashboard']);
  }

  toggleTheme() {
    this.themeService.toggleTheme();
  }

  togglePassword() {
    this.showPassword.set(!this.showPassword());
  }

  setFocused(id: string | null) {
    this.focused.set(id);
  }
}
