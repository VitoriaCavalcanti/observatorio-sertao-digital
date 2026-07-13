import { provideHttpClient } from '@angular/common/http';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { TestBed } from '@angular/core/testing';
import { Observatorio } from './observatorio';

describe('Observatorio', () => {
  it('usa a API relativa roteada pelo Traefik', () => {
    TestBed.configureTestingModule({ providers: [provideHttpClient(), provideHttpClientTesting()] });
    const service = TestBed.inject(Observatorio); const http = TestBed.inject(HttpTestingController);
    service.listarAvisos().subscribe((dados) => expect(dados).toEqual([]));
    const request = http.expectOne('/api/avisos'); expect(request.request.method).toBe('GET'); request.flush([]); http.verify();
  });
});
