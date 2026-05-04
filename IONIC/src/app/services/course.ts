import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class CourseService {
  private apiUrl = environment.apiUrl;

  constructor(private http: HttpClient) { }

  // Ambil semua course untuk halaman Home
  getAllCourses() {
    return this.http.get(`${this.apiUrl}/courses`);
  }

  // Ambil detail course berdasarkan ID
  getCourseDetail(id: string) {
    return this.http.get(`${this.apiUrl}/courses/${id}`);
  }

  // Ambil progress belajar
  getLearningProgress(courseId: string) {
    const token = localStorage.getItem('auth-token');
    const headers = new HttpHeaders().set('Authorization', `Bearer ${token}`);
    return this.http.get(`${this.apiUrl}/courses/${courseId}/progress`, { headers });
  }
}