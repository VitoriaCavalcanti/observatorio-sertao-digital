import { Component, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { Auth } from '../services/auth';
import { Observatorio } from '../services/observatorio';

@Component({
  selector: 'app-account-page',
  imports: [FormsModule],
  template: `
    <main class="page narrow">
      <header class="page-heading"><p class="eyebrow">Área do usuário</p><h1>Acesse seus cadastros</h1><p>Entre para cadastrar e acompanhar instituições, projetos e indicadores enviados ao Observatório.</p></header>
      @if (mensagem()) { <div class="status" [class.error]="erro()">{{ mensagem() }}</div> }
      <div class="account-grid">
        <form class="form-card" (ngSubmit)="entrar()"><h2>Entrar</h2><label>E-mail<input type="email" name="loginEmail" [(ngModel)]="login.email" required /></label><label>Senha<input type="password" name="loginSenha" [(ngModel)]="login.senha" required /></label><button class="button" [disabled]="ocupado()">Entrar</button></form>
        <form class="form-card" (ngSubmit)="registrar()"><h2>Criar conta</h2><label>Nome<input name="nome" [(ngModel)]="registro.nome" required /></label><label>E-mail<input type="email" name="registroEmail" [(ngModel)]="registro.email" required /></label><label>Senha <small>mínimo de 8 caracteres</small><input type="password" name="registroSenha" [(ngModel)]="registro.senha" minlength="8" required /></label><button class="button" [disabled]="ocupado()">Criar conta</button></form>
      </div>
    </main>
  `,
})
export class AccountPage {
  login = { email: '', senha: '' };
  registro = { nome: '', email: '', senha: '' };
  readonly ocupado = signal(false);
  readonly mensagem = signal<string | null>(null);
  readonly erro = signal(false);
  constructor(private readonly auth: Auth, private readonly api: Observatorio, private readonly router: Router) {}
  entrar(): void { this.ocupado.set(true); this.mensagem.set(null); this.auth.entrar(this.login.email, this.login.senha).subscribe({ next: () => this.router.navigateByUrl('/minha-area'), error: () => { this.falha('E-mail ou senha inválidos.'); } }); }
  registrar(): void { this.ocupado.set(true); this.mensagem.set(null); this.api.registrar(this.registro.nome, this.registro.email, this.registro.senha).subscribe({ next: () => { this.auth.entrar(this.registro.email, this.registro.senha).subscribe({ next: () => this.router.navigateByUrl('/minha-area'), error: () => this.falha('Conta criada. Entre com seus dados.') }); }, error: (e) => this.falha(e.error?.erro || 'Não foi possível criar a conta.') }); }
  private falha(texto: string): void { this.erro.set(true); this.mensagem.set(texto); this.ocupado.set(false); }
}
