import { provideHttpClient } from '@angular/common/http';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { TestBed } from '@angular/core/testing';
import { Observable } from 'rxjs';
import { Observatorio } from './observatorio';

describe('Observatorio', () => {
  let service: Observatorio;
  let http: HttpTestingController;

  beforeEach(() => {
    TestBed.configureTestingModule({ providers: [provideHttpClient(), provideHttpClientTesting()] });
    service = TestBed.inject(Observatorio);
    http = TestBed.inject(HttpTestingController);
  });

  afterEach(() => http.verify());

  it.each([
    ['listarInstituicoes', '/api/instituicoes'],
    ['listarProjetos', '/api/projetos'],
    ['listarIndicadores', '/api/indicadores'],
    ['listarAvisos', '/api/avisos'],
  ] as const)('%s usa a API relativa', (metodo, url) => {
    const chamada = service[metodo] as () => Observable<unknown[]>;
    chamada.call(service).subscribe((dados) => expect(dados).toEqual([]));
    const request = http.expectOne(url);
    expect(request.request.method).toBe('GET');
    request.flush([]);
  });

  it('carrega detalhes pelos identificadores corretos', () => {
    service.obterInstituicao(7).subscribe();
    service.obterProjeto(8).subscribe();
    service.obterIndicador(9).subscribe();
    service.obterAviso('comunicado').subscribe();
    ['/api/instituicoes/7', '/api/projetos/8', '/api/indicadores/9', '/api/avisos/comunicado'].forEach((url) => http.expectOne(url).flush({}));
  });

  it('usa endpoints autenticados para a área do usuário', () => {
    service.login('pessoa@example.com', 'senha-segura').subscribe();
    const login = http.expectOne('/api/login');
    expect(login.request.method).toBe('POST');
    expect(login.request.body.email).toBe('pessoa@example.com');
    login.flush({ id: 1, nome: 'Pessoa', email: 'pessoa@example.com', roles: ['ROLE_USER'] });

    service.meusCadastros().subscribe();
    const cadastros = http.expectOne('/api/meus-cadastros');
    expect(cadastros.request.method).toBe('GET');
    cadastros.flush({ instituicoes: [], projetos: [], indicadores: [] });
  });
});
