import { Component, OnInit, signal } from '@angular/core';
import { forkJoin } from 'rxjs';
import { Aviso, Indicador, Instituicao, Observatorio, Projeto } from './services/observatorio';

@Component({ selector: 'app-root', imports: [], templateUrl: './app.html', styleUrls: ['./app.scss'] })
export class App implements OnInit {
  protected readonly instituicoes = signal<Instituicao[]>([]);
  protected readonly projetos = signal<Projeto[]>([]);
  protected readonly indicadores = signal<Indicador[]>([]);
  protected readonly avisos = signal<Aviso[]>([]);
  protected readonly carregando = signal(true);
  protected readonly erro = signal<string | null>(null);

  constructor(private readonly observatorio: Observatorio) {}
  ngOnInit(): void {
    forkJoin({ instituicoes: this.observatorio.listarInstituicoes(), projetos: this.observatorio.listarProjetos(), indicadores: this.observatorio.listarIndicadores(), avisos: this.observatorio.listarAvisos() }).subscribe({
      next: ({ instituicoes, projetos, indicadores, avisos }) => { this.instituicoes.set(instituicoes); this.projetos.set(projetos); this.indicadores.set(indicadores); this.avisos.set(avisos); this.carregando.set(false); },
      error: () => { this.erro.set('Não foi possível carregar os dados do Observatório.'); this.carregando.set(false); }
    });
  }
}
