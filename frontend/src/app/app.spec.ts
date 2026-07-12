import { provideHttpClient } from '@angular/common/http';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { TestBed } from '@angular/core/testing';
import { App } from './app';

describe('App', () => {
  beforeEach(async () => { await TestBed.configureTestingModule({ imports: [App], providers: [provideHttpClient(), provideHttpClientTesting()] }).compileComponents(); });
  it('cria e carrega o portal público', () => {
    const fixture = TestBed.createComponent(App); fixture.detectChanges();
    const http = TestBed.inject(HttpTestingController);
    ['/api/instituicoes', '/api/projetos', '/api/indicadores', '/api/avisos'].forEach((url) => http.expectOne(url).flush([]));
    fixture.detectChanges();
    expect(fixture.componentInstance).toBeTruthy();
    expect((fixture.nativeElement as HTMLElement).querySelector('h1')?.textContent).toContain('Painel inicial');
    http.verify();
  });
});
