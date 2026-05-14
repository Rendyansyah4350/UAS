import { ComponentFixture, TestBed } from '@angular/core/testing';
import { RiwayatTransaksiPage } from './riwayat-transaksi.page';

describe('RiwayatTransaksiPage', () => {
  let component: RiwayatTransaksiPage;
  let fixture: ComponentFixture<RiwayatTransaksiPage>;

  beforeEach(() => {
    fixture = TestBed.createComponent(RiwayatTransaksiPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
