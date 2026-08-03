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

`POST /api/registro` cria uma conta comum com nome, e-mail e senha de no mínimo oito caracteres. `PATCH /api/minha-conta` atualiza os dados da conta e `POST /api/logout` encerra a sessão.

## Área do usuário

| Método | Rota | Descrição |
|---|---|---|
| GET | `/api/meus-cadastros` | Lista os registros pertencentes ao usuário |
| POST | `/api/meus-cadastros/{tipo}` | Cria um rascunho próprio |
| PATCH | `/api/meus-cadastros/{tipo}/{id}` | Atualiza um cadastro próprio fora de análise |
| POST | `/api/meus-cadastros/{tipo}/{id}/enviar` | Envia o cadastro para revisão |

Os tipos aceitos são `instituicoes`, `projetos` e `indicadores`. O servidor verifica a propriedade do registro; um usuário não pode consultar ou alterar cadastros pertencentes a outra conta.

## Escrita protegida

Os recursos `instituicoes`, `projetos` e `indicadores` aceitam `POST`, `PUT` e `PATCH` para usuários com `ROLE_EDITOR`. Exclusões com `DELETE` exigem `ROLE_ADMIN`. Inclusões retornam `201`; exclusões retornam `204`; dados inválidos retornam `422` com `violacoes` quando aplicável.

Exemplo:

```json
{
  "nome": "Instituto do Sertão",
  "sigla": "IS",
  "municipio": "Petrolina",
  "uf": "PE"
}
```
