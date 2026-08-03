import { Routes } from '@angular/router';
import { DetailPage } from './pages/detail.page';
import { HomePage } from './pages/home.page';
import { ListPage } from './pages/list.page';
import { SearchPage } from './pages/search.page';
import { AccountPage } from './pages/account.page';
import { UserAreaPage } from './pages/user-area.page';

export const routes: Routes = [
  { path: '', component: HomePage, title: 'Observatório Sertão Digital' },
  { path: 'instituicoes', component: ListPage, data: { tipo: 'instituicoes' }, title: 'Instituições | Observatório' },
  { path: 'instituicoes/:id', component: DetailPage, data: { tipo: 'instituicoes' }, title: 'Instituição | Observatório' },
  { path: 'projetos', component: ListPage, data: { tipo: 'projetos' }, title: 'Projetos | Observatório' },
  { path: 'projetos/:id', component: DetailPage, data: { tipo: 'projetos' }, title: 'Projeto | Observatório' },
  { path: 'indicadores', component: ListPage, data: { tipo: 'indicadores' }, title: 'Indicadores | Observatório' },
  { path: 'indicadores/:id', component: DetailPage, data: { tipo: 'indicadores' }, title: 'Indicador | Observatório' },
  { path: 'avisos', component: ListPage, data: { tipo: 'avisos' }, title: 'Avisos | Observatório' },
  { path: 'avisos/:slug', component: DetailPage, data: { tipo: 'avisos' }, title: 'Aviso | Observatório' },
  { path: 'busca', component: SearchPage, title: 'Busca | Observatório' },
  { path: 'entrar', component: AccountPage, title: 'Entrar | Observatório' },
  { path: 'minha-area', component: UserAreaPage, title: 'Meus cadastros | Observatório' },
  { path: '**', redirectTo: '' },
];
