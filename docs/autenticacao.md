# Autenticação e autorização

## Login administrativo

O painel usa o `form_login` do Symfony Security, proteção CSRF, sessão e remember-me. O login está em `http://admin.observatorio.localhost/login`.

## Papéis

- `ROLE_USER`: usuário autenticado.
- `ROLE_EDITOR`: herda `ROLE_USER` e mantém dados e avisos.
- `ROLE_ADMIN`: herda `ROLE_EDITOR` e executa exclusões e administração sensível.

## Criar administrador

```powershell
docker compose exec backend php bin/console app:create-admin email senha "Nome"
```

Não registre senhas reais em documentação, fixtures versionadas ou arquivos `.env` enviados ao Git.

## API

O login JSON usa a mesma entidade e sessão do painel. Em chamadas originadas de outro domínio, o cliente precisa enviar credenciais/cookies e o CORS deve permitir explicitamente a origem. Pelo portal principal, `/api` mantém a mesma origem graças ao Traefik.

Usuários com `ROLE_USER` acessam somente a própria conta e seus cadastros no Angular. `ROLE_EDITOR` revisa e publica dados pelo Symfony. `ROLE_ADMIN` também administra usuários e executa exclusões.
