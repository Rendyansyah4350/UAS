import { ComponentFixture, TestBed } from '@angular/core/testing';
import { CoursePlayerPage } from './course-player.page';

describe('CoursePlayerPage', () => {
  let component: CoursePlayerPage;
  let fixture: ComponentFixture<CoursePlayerPage>;

  beforeEach(() => {
    fixture = TestBed.createComponent(CoursePlayerPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
