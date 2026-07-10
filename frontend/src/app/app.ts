import { Component, OnInit, signal } from '@angular/core';
import { Observatorio, Instituicao, Projeto, Indicador } from './services/observatorio';

@Component({
  selector: 'app-root',
  imports: [],
  templateUrl: './app.html',
  styleUrl: './app.scss'
})
export class App implements OnInit {
  protected readonly instituicoes = signal<Instituicao[]>([]);
  protected readonly projetos = signal<Projeto[]>([]);
  protected readonly indicadores = signal<Indicador[]>([]);
  protected readonly carregando = signal(true);
  protected readonly erro = signal<string | null>(null);

  constructor(private readonly observatorio: Observatorio) {}

  ngOnInit(): void {
    this.carregarDados();
  }

  private carregarDados(): void {
    this.carregando.set(true);
    this.erro.set(null);

    this.observatorio.listarInstituicoes().subscribe({
      next: (dados) => this.instituicoes.set(dados),
      error: () => this.erro.set('Não foi possível carregar as instituições.')
    });

    this.observatorio.listarProjetos().subscribe({
      next: (dados) => this.projetos.set(dados),
      error: () => this.erro.set('Não foi possível carregar os projetos.')
    });

    this.observatorio.listarIndicadores().subscribe({
      next: (dados) => {
        this.indicadores.set(dados);
        this.carregando.set(false);
      },
      error: () => {
        this.erro.set('Não foi possível carregar os indicadores.');
        this.carregando.set(false);
      }
    });
  }
}