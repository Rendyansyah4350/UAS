// src/app/services/course.service.ts
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class CourseService {
  // Ganti dengan URL API backend marketplace kamu
  private apiUrl = 'https://eduvan.rehalivan.com/api/courses';

  constructor(private http: HttpClient) { }
  
  getCourses(): Observable<any> {
    return this.http.get(this.apiUrl);
  }
  getCourseById(id: string): Observable<any> {
  // Pastikan pakai backticks (``) bukan petik biasa ('')
  return this.http.get(`${this.apiUrl}/${id}`); 
  }
}