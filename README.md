# Observatório Sertão Digital

Plataforma para reunir, revisar e publicar instituições, projetos, indicadores e avisos relacionados ao ecossistema digital do Sertão. O Angular oferece consulta pública e uma área na qual cada usuário mantém os próprios cadastros. O Symfony é o gerenciador interno usado pela equipe do Sertão Digital para revisar, publicar e corrigir os dados.

## Aplicações

- **Portal público Angular:** `http://observatorio.localhost`
- **Administração Symfony:** `http://admin.observatorio.localhost`
- **API:** `http://api.observatorio.localhost` ou `/api` no portal
- **Meilisearch:** `http://busca.observatorio.localhost`
- **Traefik:** `http://traefik.observatorio.localhost`

O backend usa o [Symfony Demo](https://github.com/symfony/demo) como aplicação-base: homepage própria, Bootstrap/Bootswatch, AssetMapper, Sass, UX Icons, Stimulus, layouts público e administrativo, autenticação pelo Security Bundle, formulários Twig e uma central de avisos derivada da estrutura de Posts. Ao abrir `http://api.observatorio.localhost`, a homepage do Observatório substitui o placeholder padrão do Symfony.

## Início rápido

```powershell
docker compose up -d --build
docker compose exec backend php bin/console doctrine:migrations:migrate --no-interaction
docker compose exec backend php bin/console app:create-admin admin@observatorio.local senha-segura "Administrador"
```

Em instalações com o executável legado, substitua `docker compose` por `docker-compose`.

Mais informações em [docs/como-rodar.md](docs/como-rodar.md), [docs/arquitetura.md](docs/arquitetura.md), [docs/api.md](docs/api.md) e [docs/autenticacao.md](docs/autenticacao.md).
