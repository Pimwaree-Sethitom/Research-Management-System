import { Component, signal, Inject, PLATFORM_ID } from '@angular/core';
import { CommonModule, isPlatformBrowser } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';

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

  constructor(
    private router: Router,
    @Inject(PLATFORM_ID) private platformId: Object
  ) { }

  async onSubmit() {
    this.loading.set(true);

    // Simulate login delay
    await new Promise(resolve => setTimeout(resolve, 1500));

    this.loading.set(false);
    this.router.navigate(['/dashboard']);
  }

  togglePassword() {
    this.showPassword.set(!this.showPassword());
  }

  setFocused(id: string | null) {
    this.focused.set(id);
  }
}
