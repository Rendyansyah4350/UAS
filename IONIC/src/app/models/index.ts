// Model untuk Data User
export interface User {
  id: number;
  name: string;
  email: string;
  role: 'admin' | 'student' ; // Membedakan akses user
  avatar?: string; // Tanda tanya (?) artinya opsional
  created_at?: string;
}

// Model untuk Kategori Kursus
export interface Category {
  id: number;
  name: string;
  icon?: string;
}

// Model untuk Kursus (Utama)
export interface Course {
  id: number;
  title: string;
  slug: string;
  description: string;
  price: number;
  thumbnail: string;
  instructor_name: string;
  category_id: number;
  category?: Category;
  rating: number;
  total_students: number;
  modules?: CourseModule[]; // Relasi ke materi/modul
}

// Model untuk Modul di dalam kursus
export interface CourseModule {
  id: number;
  course_id: number;
  title: string;
  video_url?: string;
  duration?: string;
  is_locked: boolean; // Jika user belum beli, materi terkunci
}