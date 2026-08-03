import { Injectable, signal } from '@angular/core';
import { catchError, Observable, of, tap } from 'rxjs';
import { Observatorio, Usuario } from './observatorio';

@Injectable({ providedIn: 'root' })
export class Auth {
  readonly usuario = signal<Usuario | null>(null);
  readonly verificado = signal(false);
  constructor(private readonly api: Observatorio) {}
  verificar(): Observable<Usuario | null> { return this.api.minhaConta().pipe(tap((u) => { this.usuario.set(u); this.verificado.set(true); }), catchError(() => { this.usuario.set(null); this.verificado.set(true); return of(null); })); }
  entrar(email: string, senha: string): Observable<Usuario> { return this.api.login(email, senha).pipe(tap((u) => this.usuario.set(u))); }
  sair(): Observable<void> { return this.api.logout().pipe(tap(() => this.usuario.set(null))); }
}
