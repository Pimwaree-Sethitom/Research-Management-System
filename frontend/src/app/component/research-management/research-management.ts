// research-management.component.ts
import { Component, signal, computed } from '@angular/core';
import { toSignal } from '@angular/core/rxjs-interop';
import { CommonModule } from '@angular/common';
import { FormControl, ReactiveFormsModule } from '@angular/forms';

export interface ResearchItem {
  id: number;
  titleEn: string;
  titleTh: string;
  authors: string[];
  researchType: string;
  quartile: string;
  year: number;
  department: string;
}

import { Sidebar } from '../sidebar/sidebar';

@Component({
  selector: 'app-research-management',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, Sidebar],
  templateUrl: './research-management.component.html',
  styleUrls: ['./research-management.component.scss']
})
export class ResearchManagementComponent {
  // State
  // Form Controls
  searchControl = new FormControl('');
  quartileFilter = new FormControl('all');

  // Signals derived from Form Controls
  searchQuery = toSignal(this.searchControl.valueChanges, { initialValue: '' });
  selectedQuartile = toSignal(this.quartileFilter.valueChanges, { initialValue: 'all' });

  viewMode = signal<'grid' | 'list'>('grid');

  mockResearch = signal<ResearchItem[]>([
    {
      id: 1,
      titleEn: "Deep Learning Approaches for Natural Language Processing in Thai",
      titleTh: "การเรียนรู้เชิงลึกสำหรับการประมวลผลภาษาธรรมชาติภาษาไทย",
      authors: ["Dr. Somchai Kaewprom", "Prof. Wichai Srisuk", "Dr. Nattapong Meesa"],
      researchType: "Journal Article",
      quartile: "Q1",
      year: 2024,
      department: "Computer Science",
    },
    {
      id: 2,
      titleEn: "Blockchain-Based Security Framework for IoT Networks",
      titleTh: "กรอบการรักษาความปลอดภัยบล็อกเชนสำหรับเครือข่าย IoT",
      authors: ["Asst. Prof. Prasert Tanawong", "Dr. Kittisak Panya"],
      researchType: "Conference Paper",
      quartile: "Q2",
      year: 2024,
      department: "Information Technology",
    },
    {
      id: 3,
      titleEn: "Machine Learning for Predictive Analytics in Healthcare",
      titleTh: "การเรียนรู้ของเครื่องสำหรับการวิเคราะห์เชิงพยากรณ์ในการดูแลสุขภาพ",
      authors: ["Dr. Supachai Wong", "Prof. Anong Thamsatit", "Dr. Parichat Laksamee", "Dr. Jirawan Sukkan"],
      researchType: "Journal Article",
      quartile: "Q1",
      year: 2025,
      department: "Data Science",
    },
    {
      id: 4,
      titleEn: "Cloud Computing Optimization Strategies for Enterprise Systems",
      titleTh: "กลยุทธ์การเพิ่มประสิทธิภาพการประมวลผลแบบคลาวด์สำหรับระบบองค์กร",
      authors: ["Dr. Weerachai Jitkaew", "Asst. Prof. Narong Phakdee"],
      researchType: "Book Chapter",
      quartile: "Q3",
      year: 2023,
      department: "Software Engineering",
    },
    {
      id: 5,
      titleEn: "Cybersecurity Threat Detection Using AI-Powered Systems",
      titleTh: "การตรวจจับภัยคุกคามความปลอดภัยไซเบอร์โดยใช้ระบบ AI",
      authors: ["Prof. Chaiyaporn Musikapong", "Dr. Siriporn Detcharoen"],
      researchType: "Journal Article",
      quartile: "Q2",
      year: 2024,
      department: "Cybersecurity",
    },
    {
      id: 6,
      titleEn: "Smart City Infrastructure Development Using Edge Computing",
      titleTh: "การพัฒนาโครงสร้างพื้นฐานเมืองอัจฉริยะโดยใช้ Edge Computing",
      authors: ["Dr. Apinya Sunthornpan", "Dr. Kanchai Wiriyatham", "Prof. Montri Suwanprasit"],
      researchType: "Conference Paper",
      quartile: "Q1",
      year: 2025,
      department: "Computer Science",
    },
  ]);

  filteredResearch = computed(() => {
    const query = (this.searchQuery() ?? '').toLowerCase();
    const quartile = this.selectedQuartile() ?? 'all';

    return this.mockResearch().filter((item: ResearchItem) => {
      const matchesSearch = item.titleEn.toLowerCase().includes(query) ||
        item.titleTh.includes(query) ||
        item.authors.some((a: string) => a.toLowerCase().includes(query));
      const matchesQuartile = quartile === 'all' || item.quartile === quartile;
      return matchesSearch && matchesQuartile;
    });
  });

  setViewMode(mode: 'grid' | 'list') {
    this.viewMode.set(mode);
  }

  addResearch() {
    console.log('Add research clicked');
  }

  researchTypeHash(type: string): number {
    let hash = 0;
    for (let i = 0; i < type.length; i++) {
      hash = type.charCodeAt(i) + ((hash << 5) - hash);
    }
    return Math.abs(hash % 360);
  }

  onView(id: number) { console.log('View', id); }
  onEdit(id: number) { console.log('Edit', id); }
  onDelete(id: number) { console.log('Delete', id); }
}