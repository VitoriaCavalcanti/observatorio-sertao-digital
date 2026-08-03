import { Component, OnInit, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { Auth } from '../services/auth';
import { Indicador, Instituicao, MeusCadastros, Observatorio, Projeto, StatusCadastro } from '../services/observatorio';

@Component({
  selector: 'app-user-area-page',
  imports: [FormsModule],
  template: `
    <main class="page">
      <header class="user-heading"><div><p class="eyebrow">Área do usuário</p><h1>Meus cadastros</h1><p>Acompanhe e mantenha os dados sob sua responsabilidade.</p></div><button class="link-button" (click)="sair()">Sair</button></header>
      @if (mensagem()) { <div class="status" [class.error]="erro()">{{ mensagem() }}</div> }
      @if (carregando()) { <div class="status">Carregando seus cadastros...</div> }
      @else {
        <section class="workspace-section"><div class="section-heading"><div><p class="eyebrow">Minha conta</p><h2>Dados de acesso</h2></div></div><form class="data-form" (ngSubmit)="salvarConta()"><label>Nome<input name="contaNome" [(ngModel)]="conta.nome" required /></label><label>E-mail<input type="email" name="contaEmail" [(ngModel)]="conta.email" required /></label><label>Nova senha <small>deixe vazia para manter</small><input type="password" name="contaSenha" [(ngModel)]="conta.senha" minlength="8" /></label><div class="form-actions"><button class="button">Atualizar conta</button></div></form></section>
        <section class="workspace-section"><div class="section-heading"><div><p class="eyebrow">Instituições</p><h2>{{ instituicao.id ? 'Editar instituição' : 'Nova instituição' }}</h2></div></div>
          <form class="data-form" (ngSubmit)="salvarInstituicao()"><label>Nome<input name="instNome" [(ngModel)]="instituicao.nome" required /></label><label>Sigla<input name="instSigla" [(ngModel)]="instituicao.sigla" /></label><label>Tipo<input name="instTipo" [(ngModel)]="instituicao.tipo" /></label><label>Município<input name="instMunicipio" [(ngModel)]="instituicao.municipio" /></label><label>UF<input name="instUf" [(ngModel)]="instituicao.uf" maxlength="2" /></label><label>E-mail<input type="email" name="instEmail" [(ngModel)]="instituicao.email" /></label><div class="form-actions"><button class="button">Salvar rascunho</button>@if (instituicao.id) { <button type="button" class="link-button" (click)="limparInstituicao()">Cancelar edição</button> }</div></form>
          <div class="user-records">@for (item of dados().instituicoes; track item.id) { <article><div><span class="tag">{{ status(item.statusCadastro) }}</span><h3>{{ item.nome }}</h3>@if (item.publicado && item.statusCadastro !== 'publicado') { <small class="published-note">A versão anterior continua pública.</small> }@if (item.observacaoRevisao) { <p class="review-note">{{ item.observacaoRevisao }}</p> }</div><div class="record-actions"><button (click)="editarInstituicao(item)" [disabled]="item.statusCadastro === 'em_analise'">Editar</button><button (click)="enviar('instituicoes', item.id)" [disabled]="item.statusCadastro === 'em_analise' || item.statusCadastro === 'publicado' || item.statusCadastro === 'rejeitado'">Enviar para análise</button></div></article> }</div>
        </section>

        <section class="workspace-section"><div class="section-heading"><div><p class="eyebrow">Projetos</p><h2>{{ projeto.id ? 'Editar projeto' : 'Novo projeto' }}</h2></div></div>
          <form class="data-form" (ngSubmit)="salvarProjeto()"><label>Título<input name="projTitulo" [(ngModel)]="projeto.titulo" required /></label><label>Status<input name="projStatus" [(ngModel)]="projeto.status" /></label><label class="wide">Resumo<textarea name="projResumo" [(ngModel)]="projeto.resumo" rows="3"></textarea></label><label>Data inicial<input type="date" name="projInicio" [(ngModel)]="projeto.dataInicio" /></label><label>Data final<input type="date" name="projFim" [(ngModel)]="projeto.dataFim" /></label><label>Instituição<select name="projInst" [(ngModel)]="projeto.instituicaoId"><option [ngValue]="null">Sem instituição</option>@for (i of dados().instituicoes; track i.id) { <option [ngValue]="i.id">{{ i.nome }}</option> }</select></label><div class="form-actions"><button class="button">Salvar rascunho</button>@if (projeto.id) { <button type="button" class="link-button" (click)="limparProjeto()">Cancelar edição</button> }</div></form>
          <div class="user-records">@for (item of dados().projetos; track item.id) { <article><div><span class="tag">{{ status(item.statusCadastro) }}</span><h3>{{ item.titulo }}</h3>@if (item.publicado && item.statusCadastro !== 'publicado') { <small class="published-note">A versão anterior continua pública.</small> }@if (item.observacaoRevisao) { <p class="review-note">{{ item.observacaoRevisao }}</p> }</div><div class="record-actions"><button (click)="editarProjeto(item)" [disabled]="item.statusCadastro === 'em_analise'">Editar</button><button (click)="enviar('projetos', item.id)" [disabled]="item.statusCadastro === 'em_analise' || item.statusCadastro === 'publicado' || item.statusCadastro === 'rejeitado'">Enviar para análise</button></div></article> }</div>
        </section>

        <section class="workspace-section"><div class="section-heading"><div><p class="eyebrow">Indicadores</p><h2>{{ indicador.id ? 'Editar indicador' : 'Novo indicador' }}</h2></div></div>
          <form class="data-form" (ngSubmit)="salvarIndicador()"><label>Nome<input name="indNome" [(ngModel)]="indicador.nome" required /></label><label>Valor<input type="number" name="indValor" [(ngModel)]="indicador.valor" /></label><label>Unidade<input name="indUnidade" [(ngModel)]="indicador.unidade" /></label><label>Ano de referência<input type="number" name="indAno" [(ngModel)]="indicador.anoReferencia" /></label><label>Projeto<select name="indProjeto" [(ngModel)]="indicador.projetoId"><option [ngValue]="null">Sem projeto</option>@for (p of dados().projetos; track p.id) { <option [ngValue]="p.id">{{ p.titulo }}</option> }</select></label><label class="wide">Descrição<textarea name="indDescricao" [(ngModel)]="indicador.descricao" rows="3"></textarea></label><div class="form-actions"><button class="button">Salvar rascunho</button>@if (indicador.id) { <button type="button" class="link-button" (click)="limparIndicador()">Cancelar edição</button> }</div></form>
          <div class="user-records">@for (item of dados().indicadores; track item.id) { <article><div><span class="tag">{{ status(item.statusCadastro) }}</span><h3>{{ item.nome }}</h3>@if (item.publicado && item.statusCadastro !== 'publicado') { <small class="published-note">A versão anterior continua pública.</small> }@if (item.observacaoRevisao) { <p class="review-note">{{ item.observacaoRevisao }}</p> }</div><div class="record-actions"><button (click)="editarIndicador(item)" [disabled]="item.statusCadastro === 'em_analise'">Editar</button><button (click)="enviar('indicadores', item.id)" [disabled]="item.statusCadastro === 'em_analise' || item.statusCadastro === 'publicado' || item.statusCadastro === 'rejeitado'">Enviar para análise</button></div></article> }</div>
        </section>
      }
    </main>
  `,
})
export class UserAreaPage implements OnInit {
  readonly dados = signal<MeusCadastros>({ instituicoes: [], projetos: [], indicadores: [] });
  readonly carregando = signal(true); readonly mensagem = signal<string | null>(null); readonly erro = signal(false);
  conta = { nome: '', email: '', senha: '' };
  instituicao: Partial<Instituicao> = {}; projeto: Partial<Projeto> = {}; indicador: Partial<Indicador> = {};
  constructor(private readonly api: Observatorio, private readonly auth: Auth, private readonly router: Router) {}
  ngOnInit(): void { this.api.minhaConta().subscribe({ next: (u) => { this.conta.nome = u.nome; this.conta.email = u.email; }, error: () => this.router.navigateByUrl('/entrar') }); this.carregar(); }
  carregar(): void { this.api.meusCadastros().subscribe({ next: (d) => { this.dados.set(d); this.carregando.set(false); }, error: (e) => { if (e.status === 401) this.router.navigateByUrl('/entrar'); else this.falha('Não foi possível carregar seus cadastros.'); } }); }
  salvarInstituicao(): void { this.salvar<Instituicao>('instituicoes', this.instituicao, () => this.limparInstituicao()); }
  salvarProjeto(): void { this.salvar<Projeto>('projetos', this.projeto, () => this.limparProjeto()); }
  salvarIndicador(): void { this.salvar<Indicador>('indicadores', this.indicador, () => this.limparIndicador()); }
  private salvar<T extends { id: number }>(tipo: string, modelo: Partial<T>, limpar: () => void): void { const chamada = modelo.id ? this.api.atualizarCadastro<T>(tipo, modelo.id, modelo) : this.api.criarCadastro<T>(tipo, modelo); chamada.subscribe({ next: () => { limpar(); this.sucesso('Rascunho salvo.'); this.carregar(); }, error: (e) => this.falha(e.error?.erro || 'Não foi possível salvar.') }); }
  enviar(tipo: string, id: number): void { this.api.enviarCadastro(tipo, id).subscribe({ next: () => { this.sucesso('Cadastro enviado para análise.'); this.carregar(); }, error: (e) => this.falha(e.error?.erro || 'Não foi possível enviar.') }); }
  salvarConta(): void { const dados = { ...this.conta, senha: this.conta.senha || undefined }; this.api.atualizarConta(dados).subscribe({ next: (u) => { this.auth.usuario.set(u); this.conta.senha = ''; this.sucesso('Conta atualizada.'); }, error: (e) => this.falha(e.error?.erro || 'Não foi possível atualizar a conta.') }); }
  editarInstituicao(i: Instituicao): void { this.instituicao = { ...i }; } editarProjeto(i: Projeto): void { this.projeto = { ...i }; } editarIndicador(i: Indicador): void { this.indicador = { ...i }; }
  limparInstituicao(): void { this.instituicao = {}; } limparProjeto(): void { this.projeto = {}; } limparIndicador(): void { this.indicador = {}; }
  status(s?: StatusCadastro): string { return ({ rascunho: 'Rascunho', em_analise: 'Em análise', publicado: 'Publicado', devolvido: 'Correção solicitada', rejeitado: 'Rejeitado' } as Record<string, string>)[s || 'rascunho']; }
  sair(): void { this.auth.sair().subscribe({ next: () => this.router.navigateByUrl('/') }); }
  private sucesso(m: string): void { this.erro.set(false); this.mensagem.set(m); }
  private falha(m: string): void { this.erro.set(true); this.mensagem.set(m); this.carregando.set(false); }
}
