# Arquitetura Inicial

## Visão geral

O Observatório Sertão Digital será organizado em uma arquitetura web separando frontend, backend, banco de dados, mecanismo de busca e possíveis serviços de inteligência artificial.

## Componentes principais

```text
Usuário
  |
  v
Frontend Angular
  |
  v
Backend Symfony API
  |
  +--> PostgreSQL
  |
  +--> Meilisearch
  |
  +--> IA futura: Ollama + OpenWebUI