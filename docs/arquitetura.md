# Arquitetura

## Visão geral

```text
Navegador
   |
Traefik :80
   |-- observatorio.localhost --------> Angular (portal público)
   |        `-- /api -----------------> Symfony API
   |-- admin.observatorio.localhost --> Symfony + Twig (administração)
   |-- api.observatorio.localhost ----> Symfony API (acesso direto)
   `-- busca.observatorio.localhost --> Meilisearch

Symfony --> PostgreSQL
```

O Traefik resolve os nomes HTTP e encaminha cada requisição para a porta interna do container. Os serviços de dados ficam em uma rede separada e não expõem portas diretamente no host.

## Separação de responsabilidades

- Angular apresenta dados públicos e consome a API por `/api`.
- Symfony/Twig fornece a área interna para manutenção e correção dos dados.
- A API expõe leituras públicas e exige `ROLE_EDITOR` para escrita.
- PostgreSQL é a fonte persistente.
- Meilisearch será o índice de busca, sem substituir o banco.

## Padrões do Symfony Demo

Foram adotados `User` persistido pelo Doctrine, login via formulário com CSRF, remember-me, hierarquia de papéis, formulários Symfony, flash messages, controllers administrativos e o modelo de Post. O Post foi especializado como aviso, com status, visibilidade, fixação, prioridade, autor e datas.

## Domínio

- `Instituicao` possui projetos.
- `Projeto` pertence opcionalmente a uma instituição e possui indicadores.
- `Indicador` pertence opcionalmente a um projeto.
- `Post` representa avisos internos ou comunicados públicos.
- `User` é autor de avisos e acessa a administração.
- `Tag` classifica avisos e permite evolução da central.
