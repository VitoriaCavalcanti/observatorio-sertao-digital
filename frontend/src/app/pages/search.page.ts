import { Component, OnInit, computed, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { forkJoin } from 'rxjs';
import { Aviso, Indicador, Instituicao, Observatorio, Projeto } from '../services/observatorio';

interface Resultado { id: number; tipo: string; titulo: string; descricao: string; link: (string | number)[]; texto: string; }

@Component({
  selector: 'app-search-page',
  imports: [FormsModule, RouterLink],
  template: `
    <main class="page">
      <header class="page-heading"><p class="eyebrow">Busca integrada</p><h1>Explore o Observatório</h1><p>Pesquise simultaneamente em instituições, projetos, indicadores e avisos.</p></header>
      <label class="search-box"><span class="sr-only">Termo de busca</span><input type="search" [ngModel]="termo()" (ngModelChange)="termo.set($event)" placeholder="Digite um nome, município, projeto ou tema..." autofocus /><span>⌕</span></label>
      @if (carregando()) { <div class="status">Preparando o acervo...</div> }
      @else if (erro()) { <div class="status error">{{ erro() }}</div> }
      @else if (termo().trim().length < 2) { <p class="search-hint">Digite pelo menos dois caracteres para pesquisar em {{ todos().length }} registros.</p> }
      @else {
        <p class="result-count">{{ resultados().length }} resultado(s) para “{{ termo() }}”</p>
        <div class="resource-list">
          @for (item of resultados(); track item.tipo + item.id) { <a class="resource-row" [routerLink]="item.link"><div><span class="tag">{{ item.tipo }}</span><h2>{{ item.titulo }}</h2><p>{{ item.descricao }}</p></div><span class="arrow">→</span></a> }
          @empty { <p class="empty">Nenhum resultado encontrado. Tente um termo mais amplo.</p> }
        </div>
      }
    </main>
  `,
})
export class SearchPage implements OnInit {
  readonly termo = signal('');
  readonly todos = signal<Resultado[]>([]);
  readonly carregando = signal(true);
  readonly erro = signal<string | null>(null);
  readonly resultados = computed(() => { const q = this.normalizar(this.termo()); return q.length < 2 ? [] : this.todos().filter((item) => this.normalizar(item.texto).includes(q)); });
  constructor(private readonly api: Observatorio) {}
  ngOnInit(): void {
    forkJoin({ instituicoes: this.api.listarInstituicoes(), projetos: this.api.listarProjetos(), indicadores: this.api.listarIndicadores(), avisos: this.api.listarAvisos() }).subscribe({
      next: ({ instituicoes, projetos, indicadores, avisos }) => { this.todos.set([...instituicoes.map(this.instituicao), ...projetos.map(this.projeto), ...indicadores.map(this.indicador), ...avisos.map(this.aviso)]); this.carregando.set(false); },
      error: () => { this.erro.set('Não foi possível carregar o índice de busca.'); this.carregando.set(false); },
    });
  }
  private readonly instituicao = (i: Instituicao): Resultado => ({ id: i.id, tipo: 'Instituição', titulo: i.nome, descricao: [i.tipo, i.municipio, i.uf].filter(Boolean).join(' · '), link: ['/instituicoes', i.id], texto: Object.values(i).join(' ') });
  private readonly projeto = (i: Projeto): Resultado => ({ id: i.id, tipo: 'Projeto', titulo: i.titulo, descricao: i.resumo || i.status || '', link: ['/projetos', i.id], texto: `${i.titulo} ${i.resumo} ${i.status} ${i.instituicao?.nome}` });
  private readonly indicador = (i: Indicador): Resultado => ({ id: i.id, tipo: 'Indicador', titulo: i.nome, descricao: `${i.valor ?? '—'} ${i.unidade || ''}`, link: ['/indicadores', i.id], texto: `${i.nome} ${i.descricao} ${i.unidade} ${i.anoReferencia} ${i.projeto?.titulo}` });
  private readonly aviso = (i: Aviso): Resultado => ({ id: i.id, tipo: 'Aviso', titulo: i.titulo, descricao: i.resumo, link: ['/avisos', i.slug], texto: `${i.titulo} ${i.resumo} ${i.autor}` });
  private normalizar(valor: string): string { return valor.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLocaleLowerCase('pt-BR').trim(); }
}
