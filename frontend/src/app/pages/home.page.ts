import { Component, OnInit, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { forkJoin } from 'rxjs';
import { Aviso, Indicador, Instituicao, Observatorio, Projeto } from '../services/observatorio';

@Component({
  selector: 'app-home-page',
  imports: [RouterLink],
  template: `
    <main class="page">
      <section class="hero">
        <div>
          <p class="eyebrow">Dados, conexões e oportunidades</p>
          <h1>O ecossistema digital do Sertão em um só lugar.</h1>
          <p class="lead">Consulte instituições, projetos e indicadores que ajudam a compreender e fortalecer o território.</p>
          <div class="actions"><a class="button" routerLink="/busca">Explorar dados</a><a class="text-link" routerLink="/avisos">Ver comunicados</a></div>
        </div>
        <aside class="hero-note"><span>Observatório aberto</span><strong>Informação pública para decisões melhores.</strong></aside>
      </section>

      @if (carregando()) { <div class="status">Carregando dados do Observatório...</div> }
      @else if (erro()) { <div class="status error">{{ erro() }}</div> }
      @else {
        <section class="metrics" aria-label="Resumo do acervo">
          <a routerLink="/instituicoes"><span>Instituições</span><strong>{{ instituicoes().length }}</strong><small>Conheça a rede →</small></a>
          <a routerLink="/projetos"><span>Projetos</span><strong>{{ projetos().length }}</strong><small>Explore iniciativas →</small></a>
          <a routerLink="/indicadores"><span>Indicadores</span><strong>{{ indicadores().length }}</strong><small>Consulte evidências →</small></a>
        </section>

        <section class="section-heading"><div><p class="eyebrow">Em destaque</p><h2>Iniciativas recentes</h2></div><a routerLink="/projetos">Ver todos</a></section>
        <div class="card-grid">
          @for (item of projetos().slice(0, 3); track item.id) {
            <a class="card" [routerLink]="['/projetos', item.id]"><span class="tag">{{ item.status || 'Projeto' }}</span><h3>{{ item.titulo }}</h3><p>{{ item.resumo || 'Conheça os detalhes desta iniciativa.' }}</p><small>{{ item.instituicao?.nome || 'Iniciativa do ecossistema' }}</small></a>
          } @empty { <p class="empty">Nenhum projeto publicado até o momento.</p> }
        </div>

        @if (avisos().length) {
          <section class="notice"><div><p class="eyebrow">Comunicados</p><h2>{{ avisos()[0].titulo }}</h2><p>{{ avisos()[0].resumo }}</p></div><a class="button secondary" [routerLink]="['/avisos', avisos()[0].slug]">Ler aviso</a></section>
        }
      }
    </main>
  `,
})
export class HomePage implements OnInit {
  readonly instituicoes = signal<Instituicao[]>([]);
  readonly projetos = signal<Projeto[]>([]);
  readonly indicadores = signal<Indicador[]>([]);
  readonly avisos = signal<Aviso[]>([]);
  readonly carregando = signal(true);
  readonly erro = signal<string | null>(null);

  constructor(private readonly api: Observatorio) {}

  ngOnInit(): void {
    forkJoin({ instituicoes: this.api.listarInstituicoes(), projetos: this.api.listarProjetos(), indicadores: this.api.listarIndicadores(), avisos: this.api.listarAvisos() }).subscribe({
      next: (dados) => { this.instituicoes.set(dados.instituicoes); this.projetos.set(dados.projetos); this.indicadores.set(dados.indicadores); this.avisos.set(dados.avisos); this.carregando.set(false); },
      error: () => { this.erro.set('Não foi possível carregar os dados agora. Tente novamente em instantes.'); this.carregando.set(false); },
    });
  }
}
