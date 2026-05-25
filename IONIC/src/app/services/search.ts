import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class SearchService {
  // BehaviorSubject bertindak sebagai "penyimpan data"
  private searchSource = new BehaviorSubject<string>('');
  currentKeyword = this.searchSource.asObservable();

  constructor() { }

  changeKeyword(keyword: string) {
    this.searchSource.next(keyword);
  }
}