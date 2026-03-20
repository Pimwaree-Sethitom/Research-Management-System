import { Component, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ResearchManagementComponent } from './component/research-management/research-management';

@Component({
  selector: 'app-root',
  imports: [CommonModule, ResearchManagementComponent],
  templateUrl: './app.html',
  styleUrl: './app.css'
})
export class App {
  protected readonly title = signal('frontend');
}
