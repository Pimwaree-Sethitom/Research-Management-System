import { Routes } from '@angular/router';
import { Login } from './component/login/login';
import { ResearchManagementComponent } from './component/research-management/research-management';

export const routes: Routes = [
    { path: '', redirectTo: 'login', pathMatch: 'full' },
    { path: 'login', component: Login },
    { path: 'dashboard', component: ResearchManagementComponent },
    { path: 'research-management', component: ResearchManagementComponent },
];
