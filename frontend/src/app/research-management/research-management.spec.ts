import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ResearchManagement } from './research-management';

describe('ResearchManagement', () => {
  let component: ResearchManagement;
  let fixture: ComponentFixture<ResearchManagement>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ResearchManagement]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ResearchManagement);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
