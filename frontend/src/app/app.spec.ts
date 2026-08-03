import { provideHttpClient } from '@angular/common/http';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { TestBed } from '@angular/core/testing';
import { provideRouter } from '@angular/router';
import { App } from './app';
import { routes } from './app.routes';

describe('App', () => {
  it('cria a navegação principal do portal', async () => {
    await TestBed.configureTestingModule({ imports: [App], providers: [provideHttpClient(), provideHttpClientTesting(), provideRouter(routes)] }).compileComponents();
    const fixture = TestBed.createComponent(App);
    fixture.detectChanges();
    TestBed.inject(HttpTestingController).expectOne('/api/me').flush({}, { status: 401, statusText: 'Unauthorized' });
    const links = Array.from((fixture.nativeElement as HTMLElement).querySelectorAll('nav a')).map((item) => item.textContent?.trim());
    expect(fixture.componentInstance).toBeTruthy();
    expect(links).toContain('Instituições');
    expect(links).toContain('Buscar');
  });
});
