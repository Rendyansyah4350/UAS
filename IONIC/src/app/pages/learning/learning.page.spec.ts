import { ComponentFixture, TestBed } from '@angular/core/testing';
import { LearningPage } from './learning.page';

describe('LearningPage', () => {
  let component: LearningPage;
  let fixture: ComponentFixture<LearningPage>;

  beforeEach(() => {
    fixture = TestBed.createComponent(LearningPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
