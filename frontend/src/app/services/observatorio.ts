import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

export type StatusCadastro = 'rascunho' | 'em_analise' | 'publicado' | 'devolvido';
export interface Instituicao { id: number; nome: string; sigla?: string | null; tipo?: string | null; email?: string | null; site?: string | null; municipio?: string | null; uf?: string | null; statusCadastro?: StatusCadastro; }
export interface Projeto { id: number; titulo: string; resumo?: string | null; status?: string | null; dataInicio?: string | null; dataFim?: string | null; instituicao?: { id: number; nome: string; sigla?: string | null } | null; instituicaoId?: number | null; statusCadastro?: StatusCadastro; }
export interface Indicador { id: number; nome: string; descricao?: string | null; unidade?: string | null; valor?: number | null; anoReferencia?: number | null; projeto?: { id: number; titulo: string } | null; projetoId?: number | null; statusCadastro?: StatusCadastro; }
export interface Aviso { id: number; titulo: string; slug: string; resumo: string; conteudo?: string | null; fixado: boolean; prioridade: number; publicadoEm?: string | null; autor?: string | null; }
export type Recurso = Instituicao | Projeto | Indicador | Aviso;
export interface Usuario { id: number; nome: string; email: string; roles: string[]; }
export interface MeusCadastros { instituicoes: Instituicao[]; projetos: Projeto[]; indicadores: Indicador[]; }

@Injectable({ providedIn: 'root' })
export class Observatorio {
  private readonly apiUrl = '/api';
  constructor(private readonly http: HttpClient) {}
  listarInstituicoes(): Observable<Instituicao[]> { return this.http.get<Instituicao[]>(`${this.apiUrl}/instituicoes`); }
  listarProjetos(): Observable<Projeto[]> { return this.http.get<Projeto[]>(`${this.apiUrl}/projetos`); }
  listarIndicadores(): Observable<Indicador[]> { return this.http.get<Indicador[]>(`${this.apiUrl}/indicadores`); }
  listarAvisos(): Observable<Aviso[]> { return this.http.get<Aviso[]>(`${this.apiUrl}/avisos`); }
  obterInstituicao(id: number): Observable<Instituicao> { return this.http.get<Instituicao>(`${this.apiUrl}/instituicoes/${id}`); }
  obterProjeto(id: number): Observable<Projeto> { return this.http.get<Projeto>(`${this.apiUrl}/projetos/${id}`); }
  obterIndicador(id: number): Observable<Indicador> { return this.http.get<Indicador>(`${this.apiUrl}/indicadores/${id}`); }
  obterAviso(slug: string): Observable<Aviso> { return this.http.get<Aviso>(`${this.apiUrl}/avisos/${slug}`); }
  login(email: string, password: string): Observable<Usuario> { return this.http.post<Usuario>(`${this.apiUrl}/login`, { email, password }); }
  registrar(nome: string, email: string, senha: string): Observable<Usuario> { return this.http.post<Usuario>(`${this.apiUrl}/registro`, { nome, email, senha }); }
  minhaConta(): Observable<Usuario> { return this.http.get<Usuario>(`${this.apiUrl}/me`); }
  atualizarConta(dados: { nome?: string; email?: string; senha?: string }): Observable<Usuario> { return this.http.patch<Usuario>(`${this.apiUrl}/minha-conta`, dados); }
  logout(): Observable<void> { return this.http.post<void>(`${this.apiUrl}/logout`, {}); }
  meusCadastros(): Observable<MeusCadastros> { return this.http.get<MeusCadastros>(`${this.apiUrl}/meus-cadastros`); }
  criarCadastro<T>(tipo: string, dados: object): Observable<T> { return this.http.post<T>(`${this.apiUrl}/meus-cadastros/${tipo}`, dados); }
  atualizarCadastro<T>(tipo: string, id: number, dados: object): Observable<T> { return this.http.patch<T>(`${this.apiUrl}/meus-cadastros/${tipo}/${id}`, dados); }
  enviarCadastro<T>(tipo: string, id: number): Observable<T> { return this.http.post<T>(`${this.apiUrl}/meus-cadastros/${tipo}/${id}/enviar`, {}); }
}
