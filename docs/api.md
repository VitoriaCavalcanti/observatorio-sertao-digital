# API

Base principal: `http://observatorio.localhost/api`. A API também responde em `http://api.observatorio.localhost/api`.

## Endpoints públicos

| Método | Rota | Descrição |
|---|---|---|
| GET | `/api/instituicoes` | Lista instituições |
| GET | `/api/instituicoes/{id}` | Exibe uma instituição |
| GET | `/api/projetos` | Lista projetos |
| GET | `/api/projetos/{id}` | Exibe um projeto |
| GET | `/api/indicadores` | Lista indicadores |
| GET | `/api/indicadores/{id}` | Exibe um indicador |
| GET | `/api/avisos` | Lista avisos públicos publicados |
| GET | `/api/avisos/{slug}` | Exibe um aviso público |

## Autenticação

`POST /api/login` recebe `{"email":"...","password":"..."}` e inicia uma sessão. `GET /api/me` retorna o usuário autenticado.

## Escrita protegida

Os recursos `instituicoes`, `projetos` e `indicadores` aceitam `POST`, `PUT`, `PATCH` e `DELETE` para usuários com `ROLE_EDITOR`. Inclusões retornam `201`; exclusões retornam `204`; dados inválidos retornam `422` com `violacoes` quando aplicável.

Exemplo:

```json
{
  "nome": "Instituto do Sertão",
  "sigla": "IS",
  "municipio": "Petrolina",
  "uf": "PE"
}
```
