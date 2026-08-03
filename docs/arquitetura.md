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

No ambiente local, o backend usa o servidor CLI do PHP com múltiplos workers. Isso evita que o healthcheck e as chamadas simultâneas do painel Angular bloqueiem uma única fila de requisições. Em produção, o backend deve ser servido por PHP-FPM ou outro servidor de aplicação apropriado.

As dependências Composer e o diretório de cache do Symfony usam volumes Linux dedicados (`backend_vendor` e `backend_var`). O código permanece montado a partir do workspace, mas os milhares de arquivos de dependências não atravessam o bind mount do Windows em cada requisição.

## Separação de responsabilidades

- Angular apresenta início, listas, detalhes, busca pública e a área autenticada na qual o usuário mantém os próprios cadastros.
- Symfony/Twig fornece o gerenciador interno para revisão, publicação, manutenção e correção dos dados pela equipe do Sertão Digital.
- A API expõe leituras públicas e exige `ROLE_EDITOR` para escrita.
- PostgreSQL é a fonte persistente.
- Meilisearch será o índice de busca, sem substituir o banco.

No estágio atual, a busca do portal combina no navegador os quatro endpoints públicos. Essa implementação mantém a busca funcional sem expor a chave do Meilisearch. A evolução prevista é sincronizar o índice no backend e trocar apenas a fonte dos resultados, preservando a interface pública.

## Padrões do Symfony Demo

O Symfony Demo é a base navegável do backend, incluindo homepage, herança de templates, Bootstrap/Bootswatch, AssetMapper/importmap, Sass, UX Icons, Stimulus, layout administrativo, `User` persistido pelo Doctrine, login via formulário com CSRF, remember-me, hierarquia de papéis, formulários Symfony, flash messages e o modelo de Post. O Post foi especializado como aviso, com status, visibilidade, fixação, prioridade, autor e datas.

## Domínio

- `Instituicao` possui projetos.
- `Projeto` pertence opcionalmente a uma instituição e possui indicadores.
- `Indicador` pertence opcionalmente a um projeto.
- `Post` representa avisos internos ou comunicados públicos.
- `User` é autor de avisos e acessa a administração.
- `User` também pode ser responsável por instituições, projetos e indicadores enviados pelo portal.

## Fluxo de cadastro

1. O usuário cria uma conta ou entra pelo Angular.
2. Cria e edita seus registros como rascunho.
3. Envia o cadastro para análise e ele fica bloqueado para edição.
4. A equipe revisa no Symfony e publica ou devolve para correção.
5. Apenas registros com situação `publicado` aparecem nas APIs e páginas públicas.
- `Tag` classifica avisos e permite evolução da central.
