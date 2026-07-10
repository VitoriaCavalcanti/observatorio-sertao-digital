import { Component, OnInit, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Observatorio, Instituicao, Projeto, Indicador } from './services/observatorio';

@Component({
  selector: 'app-root',
  imports: [FormsModule],
  templateUrl: './app.html',
  styleUrls: ['./app.scss']
})
export class App implements OnInit {
  protected readonly instituicoes = signal<Instituicao[]>([]);
  protected readonly projetos = signal<Projeto[]>([]);
  protected readonly indicadores = signal<Indicador[]>([]);
  protected readonly carregando = signal(true);
  protected readonly erro = signal<string | null>(null);

  protected novaInstituicao = {
    nome: '',
    sigla: '',
    tipo: '',
    email: '',
    site: '',
    municipio: '',
    uf: ''
  };

  protected salvandoInstituicao = signal(false);
  protected mensagemInstituicao = signal<string | null>(null);

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
      next: (dados) => this.indicadores.set(dados),
      error: () => this.erro.set('Não foi possível carregar os indicadores.')
    });

    this.carregando.set(false);
  }

  protected salvarInstituicao(): void {
    this.salvandoInstituicao.set(true);
    this.mensagemInstituicao.set(null);
    this.erro.set(null);

    this.observatorio.criarInstituicao(this.novaInstituicao).subscribe({
      next: (instituicao) => {
        this.instituicoes.update((atuais) => [...atuais, instituicao]);
        this.novaInstituicao = {
          nome: '',
          sigla: '',
          tipo: '',
          email: '',
          site: '',
          municipio: '',
          uf: ''
        };
        this.mensagemInstituicao.set('Instituição cadastrada com sucesso.');
        this.salvandoInstituicao.set(false);
      },
      error: () => {
        this.erro.set('Não foi possível cadastrar a instituição.');
        this.salvandoInstituicao.set(false);
      }
    });
  }
}