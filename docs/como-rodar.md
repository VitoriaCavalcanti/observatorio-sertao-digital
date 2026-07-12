# Como rodar

## Pré-requisitos

- Git
- Docker Desktop com Docker Compose

Os domínios terminados em `.localhost` normalmente são resolvidos pelo próprio navegador. Se o Windows ou navegador não resolver os nomes, abra o Bloco de Notas como administrador, edite `C:\Windows\System32\drivers\etc\hosts` e acrescente:

```text
127.0.0.1 observatorio.localhost
127.0.0.1 admin.observatorio.localhost
127.0.0.1 api.observatorio.localhost
127.0.0.1 busca.observatorio.localhost
127.0.0.1 traefik.observatorio.localhost
```

## Subir o ambiente

```powershell
docker compose up -d --build
docker compose ps
```

## Preparar o banco

```powershell
docker compose exec backend php bin/console doctrine:migrations:migrate --no-interaction
docker compose exec backend php bin/console app:create-admin admin@observatorio.local senha-segura "Administrador"
```

Abra `http://admin.observatorio.localhost` e entre com o administrador criado. O portal público fica em `http://observatorio.localhost`.

## Comandos úteis

```powershell
docker compose logs -f backend
docker compose exec backend php bin/console debug:router
docker compose exec backend php bin/phpunit
docker compose exec frontend npm test
docker compose down
```

`docker compose down` preserva os volumes. Não use `-v` se quiser manter o banco e o índice de busca.

## Variáveis

Defina `APP_SECRET` fora do Compose em ambientes compartilhados ou de produção. Senhas do PostgreSQL, chave do Meilisearch, TLS e proteção do dashboard do Traefik também devem ser externalizados antes de publicar o ambiente.
