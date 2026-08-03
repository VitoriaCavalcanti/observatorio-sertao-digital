import { Component, OnInit, signal } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { Observable } from 'rxjs';
import { Aviso, Indicador, Instituicao, Observatorio, Projeto, Recurso } from '../services/observatorio';

type Tipo = 'instituicoes' | 'projetos' | 'indicadores' | 'avisos';

@Component({
  selector: 'app-detail-page',
  imports: [RouterLink],
  template: `
    <main class="page narrow">
      <a class="back" [routerLink]="['/', tipo]">← Voltar para {{ tipo }}</a>
      @if (carregando()) { <div class="status">Carregando...</div> }
      @else if (erro()) { <div class="status error">{{ erro() }}</div> }
      @else if (item(); as dado) {
        <article class="detail">
          <p class="eyebrow">{{ etiqueta(dado) }}</p><h1>{{ nome(dado) }}</h1>
          @if (texto(dado)) { <p class="detail-lead">{{ texto(dado) }}</p> }
          <dl>
            @for (campo of campos(dado); track campo[0]) { <div><dt>{{ campo[0] }}</dt><dd>{{ campo[1] }}</dd></div> }
          </dl>
          @if (site(dado); as url) { <a class="button" [href]="url" target="_blank" rel="noopener">Visitar site</a> }
        </article>
      }
    </main>
  `,
})
export class DetailPage implements OnInit {
  readonly item = signal<Recurso | null>(null);
  readonly carregando = signal(true);
  readonly erro = signal<string | null>(null);
  tipo!: Tipo;
  constructor(private readonly route: ActivatedRoute, private readonly api: Observatorio) {}
  ngOnInit(): void { this.tipo = this.route.snapshot.data['tipo'] as Tipo; this.fonte().subscribe({ next: (item) => { this.item.set(item); this.carregando.set(false); }, error: () => { this.erro.set('Registro não encontrado ou indisponível.'); this.carregando.set(false); } }); }
  private fonte(): Observable<Recurso> { const p = this.route.snapshot.paramMap; if (this.tipo === 'instituicoes') return this.api.obterInstituicao(Number(p.get('id'))); if (this.tipo === 'projetos') return this.api.obterProjeto(Number(p.get('id'))); if (this.tipo === 'indicadores') return this.api.obterIndicador(Number(p.get('id'))); return this.api.obterAviso(p.get('slug') || ''); }
  nome(i: Recurso): string { return 'nome' in i ? i.nome : 'titulo' in i ? i.titulo : ''; }
  etiqueta(i: Recurso): string { if (this.tipo === 'instituicoes') return (i as Instituicao).tipo || 'Instituição'; if (this.tipo === 'projetos') return (i as Projeto).status || 'Projeto'; if (this.tipo === 'indicadores') return 'Indicador'; return 'Comunicado'; }
  texto(i: Recurso): string | null | undefined { if (this.tipo === 'projetos') return (i as Projeto).resumo; if (this.tipo === 'indicadores') return (i as Indicador).descricao; if (this.tipo === 'avisos') return (i as Aviso).conteudo || (i as Aviso).resumo; return null; }
  site(i: Recurso): string | null { return this.tipo === 'instituicoes' ? (i as Instituicao).site || null : null; }
  campos(i: Recurso): [string, string][] {
    if (this.tipo === 'instituicoes') { const x = i as Instituicao; return this.presentes([['Sigla', x.sigla], ['Município', x.municipio], ['UF', x.uf], ['E-mail', x.email]]); }
    if (this.tipo === 'projetos') { const x = i as Projeto; return this.presentes([['Instituição', x.instituicao?.nome], ['Início', x.dataInicio], ['Término', x.dataFim]]); }
    if (this.tipo === 'indicadores') { const x = i as Indicador; return this.presentes([['Valor', `${x.valor ?? '—'} ${x.unidade || ''}`.trim()], ['Ano de referência', x.anoReferencia?.toString()], ['Projeto', x.projeto?.titulo]]); }
    const x = i as Aviso; return this.presentes([['Publicado em', x.publicadoEm ? new Date(x.publicadoEm).toLocaleDateString('pt-BR') : null], ['Autor', x.autor]]);
  }
  private presentes(campos: [string, string | null | undefined][]): [string, string][] { return campos.filter((c): c is [string, string] => Boolean(c[1])); }
}
