import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

export interface Instituicao {
  id: number;
  nome: string;
  sigla?: string | null;
  tipo?: string | null;
  email?: string | null;
  site?: string | null;
  municipio?: string | null;
  uf?: string | null;
}

export interface Projeto {
  id: number;
  titulo: string;
  resumo?: string | null;
  status?: string | null;
  dataInicio?: string | null;
  dataFim?: string | null;
  instituicao?: {
    id: number;
    nome: string;
    sigla?: string | null;
  } | null;
}

export interface Indicador {
  id: number;
  nome: string;
  descricao?: string | null;
  unidade?: string | null;
  valor?: number | null;
  anoReferencia?: number | null;
  projeto?: {
    id: number;
    titulo: string;
  } | null;
}

@Injectable({
  providedIn: 'root'
})
export class Observatorio {
  private readonly apiUrl = 'http://localhost:8000/api';

  constructor(private readonly http: HttpClient) {}

  listarInstituicoes(): Observable<Instituicao[]> {
    return this.http.get<Instituicao[]>(`${this.apiUrl}/instituicoes`);
  }

  listarProjetos(): Observable<Projeto[]> {
    return this.http.get<Projeto[]>(`${this.apiUrl}/projetos`);
  }

  listarIndicadores(): Observable<Indicador[]> {
    return this.http.get<Indicador[]>(`${this.apiUrl}/indicadores`);
  }
}