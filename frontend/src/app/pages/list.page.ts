import { Component, OnInit, signal } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { Observable } from 'rxjs';
import { Aviso, Indicador, Instituicao, Observatorio, Projeto, Recurso } from '../services/observatorio';

type Tipo = 'instituicoes' | 'projetos' | 'indicadores' | 'avisos';

@Component({
  selector: 'app-list-page',
  imports: [RouterLink],
  template: `
    <main class="page">
      <header class="page-heading"><p class="eyebrow">Acervo público</p><h1>{{ titulo }}</h1><p>{{ descricao }}</p></header>
      @if (carregando()) { <div class="status">Carregando...</div> }
      @else if (erro()) { <div class="status error">{{ erro() }}</div> }
      @else {
        <div class="resource-list">
          @for (item of itens(); track item.id) {
            <a class="resource-row" [routerLink]="link(item)">
              <div><span class="tag">{{ rotulo(item) }}</span><h2>{{ nome(item) }}</h2><p>{{ resumo(item) }}</p></div><span class="arrow">→</span>
            </a>
          } @empty { <p class="empty">Nenhum registro publicado nesta seção.</p> }
        </div>
      }
    </main>
  `,
})
export class ListPage implements OnInit {
  readonly itens = signal<Recurso[]>([]);
  readonly carregando = signal(true);
  readonly erro = signal<string | null>(null);
  tipo!: Tipo;
  titulo = '';
  descricao = '';

  constructor(private readonly route: ActivatedRoute, private readonly api: Observatorio) {}

  ngOnInit(): void {
    this.tipo = this.route.snapshot.data['tipo'] as Tipo;
    const textos = {
      instituicoes: ['Instituições', 'Organizações que formam e apoiam o ecossistema digital do Sertão.'],
      projetos: ['Projetos', 'Iniciativas de inovação, inclusão, pesquisa e transformação digital.'],
      indicadores: ['Indicadores', 'Evidências para acompanhar a evolução digital do território.'],
      avisos: ['Avisos', 'Notícias e comunicados publicados pela equipe do Observatório.'],
    } as const;
    [this.titulo, this.descricao] = textos[this.tipo];
    this.fonte().subscribe({ next: (itens) => { this.itens.set(itens); this.carregando.set(false); }, error: () => { this.erro.set('Não foi possível carregar esta seção.'); this.carregando.set(false); } });
  }

  private fonte(): Observable<Recurso[]> {
    if (this.tipo === 'instituicoes') return this.api.listarInstituicoes();
    if (this.tipo === 'projetos') return this.api.listarProjetos();
    if (this.tipo === 'indicadores') return this.api.listarIndicadores();
    return this.api.listarAvisos();
  }
  link(item: Recurso): (string | number)[] { return ['/', this.tipo, this.tipo === 'avisos' ? (item as Aviso).slug : item.id]; }
  nome(item: Recurso): string { return 'nome' in item ? item.nome : 'titulo' in item ? item.titulo : ''; }
  rotulo(item: Recurso): string {
    if (this.tipo === 'instituicoes') return (item as Instituicao).tipo || 'Instituição';
    if (this.tipo === 'projetos') return (item as Projeto).status || 'Projeto';
    if (this.tipo === 'indicadores') return (item as Indicador).anoReferencia?.toString() || 'Indicador';
    return (item as Aviso).fixado ? 'Aviso em destaque' : 'Comunicado';
  }
  resumo(item: Recurso): string {
    if (this.tipo === 'instituicoes') { const i = item as Instituicao; return [i.sigla, i.municipio, i.uf].filter(Boolean).join(' · ') || 'Conheça esta organização.'; }
    if (this.tipo === 'projetos') return (item as Projeto).resumo || 'Conheça esta iniciativa.';
    if (this.tipo === 'indicadores') { const i = item as Indicador; return `${i.valor ?? '—'} ${i.unidade || ''}`.trim(); }
    return (item as Aviso).resumo;
  }
}
