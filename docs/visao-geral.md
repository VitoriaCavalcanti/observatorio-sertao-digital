# Visão Geral do Projeto

## Nome

Observatório Sertão Digital

## Propósito

O Observatório Sertão Digital é uma plataforma web para reunir, organizar, consultar e analisar informações estratégicas sobre o território, iniciativas, instituições, indicadores, documentos e atores relacionados ao desenvolvimento digital do Sertão.

## Problema que o sistema busca resolver

Atualmente, informações importantes sobre iniciativas, dados, projetos, instituições e evidências podem estar espalhadas em documentos, planilhas, relatórios ou registros informais.

O sistema busca centralizar essas informações em uma plataforma única, permitindo consulta, cadastro, busca, análise e apoio à tomada de decisão.

## Público-alvo

- Gestores e equipes técnicas
- Pesquisadores
- Instituições parceiras
- Comunidade acadêmica
- Pessoas envolvidas em projetos de desenvolvimento regional e digital

## Funcionalidades previstas

- Cadastro de instituições
- Cadastro de iniciativas e projetos
- Cadastro de documentos e evidências
- Cadastro de indicadores
- Cadastro de territórios/localidades
- Busca textual com Meilisearch
- Painéis de visualização
- API para consumo dos dados
- Possível integração futura com IA local usando Ollama e OpenWebUI

## Tecnologias principais

- Symfony no backend
- Angular no frontend
- PostgreSQL como banco de dados
- Meilisearch para busca
- Docker para ambiente
- Ollama e OpenWebUI como possibilidade futura de IA

## Organização inicial

O projeto está dividido em três áreas principais:

```text
backend/
frontend/
docs/