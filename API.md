# Documentação da API

Para facilitar os testes e a exploração dos endpoints, o projeto disponibiliza uma collection pronta para importação.
O arquivo está localizado na raiz do projeto:
**API_Collection_Atletis.json**
Essa collection pode ser importada tanto no Insomnia quanto no Postman, contendo todos os endpoints disponíveis, 
exemplos de requisições e configurações necessárias para testar a API.

<br>

## URL Base

```text
http://localhost:8080
```

---

# Autenticação

A API utiliza autenticação via **JWT (JSON Web Token)**.

Após realizar o login, um token será retornado e deverá ser enviado em todas as requisições protegidas.

### Header de autenticação

```http
Authorization: Bearer <TOKEN>
```

---

# Endpoints

## Autenticação

### Registrar usuário

**POST** `/auth/register`

Cria um novo usuário.

### Corpo da requisição

```json
{
    "username": "joao",
    "email": "joao@email.com",
    "password": "123456"
}
```

### Resposta

**201 Created**

```json
{
    "token": "eyJhbGciOiJIUzI1NiIs...",
    "user": {
        "id": 1,
        "username": "joao",
        "email": "joao@email.com"
    }
}
```

---

### Login

**POST** `/auth/login`

Realiza a autenticação do usuário.

### Corpo da requisição

```json
{
    "email": "joao@email.com",
    "password": "123456"
}
```

### Resposta

**200 OK**

```json
{
    "token": "eyJhbGciOiJIUzI1NiIs...",
    "user": {
        "id": 1,
        "username": "joao",
        "email": "joao@email.com"
    }
}
```

---

# Categorias

> Todos os endpoints abaixo exigem autenticação.

---

## Listar categorias

**GET** `/expense-categories`

### Resposta

**200 OK**

```json
[
    {
        "id": 1,
        "name": "Alimentação",
        "slug": "alimentacao"
    },
    {
        "id": 2,
        "name": "Transporte",
        "slug": "transporte"
    }
]
```

---

## Buscar categoria

**GET** `/expense-categories/{id}`

### Resposta

**200 OK**

```json
{
    "id": 1,
    "name": "Alimentação",
    "slug": "alimentacao"
}
```

---

## Criar categoria

**POST** `/expense-categories`

### Corpo da requisição

```json
{
    "name": "Educação"
}
```

### Resposta

**201 Created**

```json
{
    "id": 4,
    "name": "Educação",
    "slug": "educacao"
}
```

---

## Atualizar categoria

**PUT** `/expense-categories/{id}`

### Corpo da requisição

```json
{
    "name": "Mercado"
}
```

### Resposta

**200 OK**

```json
{
    "id": 1,
    "name": "Mercado",
    "slug": "mercado"
}
```

---

## Excluir categoria

**DELETE** `/expense-categories/{id}`

### Resposta

**204 No Content**

---

# Despesas

> Todos os endpoints abaixo exigem autenticação.

---

## Listar despesas

**GET** `/expenses`

### Parâmetros de consulta

| Parâmetro   | Tipo       | Obrigatório | Descrição             |
| ----------- | ---------- | ----------- | --------------------- |
| category_id | integer    | Não         | Filtra por categoria  |
| month       | integer    | Não         | Mês da despesa        |
| year        | integer    | Não         | Ano da despesa        |
| sort        | asc / desc | Não         | Ordenação por data    |
| page        | integer    | Não         | Página                |
| per_page    | integer    | Não         | Quantidade por página |

### Exemplo

```text
GET /expenses?category_id=1&month=7&year=2026&sort=desc&page=1&per_page=10
```

### Resposta

```json
{
    "items": [
        {
            "id": 1,
            "description": "Almoço",
            "amount": 45.90,
            "expense_date": "2026-07-15",
            "category": {
                "id": 1,
                "name": "Alimentação",
                "slug": "alimentacao"
            }
        }
    ],
    "pagination": {
        "page": 1,
        "per_page": 10,
        "total": 25,
        "pages": 3
    }
}
```

---

## Buscar despesa

**GET** `/expenses/{id}`

### Resposta

```json
{
    "id": 1,
    "description": "Almoço",
    "amount": 45.90,
    "expense_date": "2026-07-15",
    "category": {
        "id": 1,
        "name": "Alimentação",
        "slug": "alimentacao"
    }
}
```

---

## Criar despesa

**POST** `/expenses`

### Corpo da requisição

```json
{
    "category_id": 1,
    "description": "Almoço",
    "amount": 45.90,
    "expense_date": "2026-07-15"
}
```

### Resposta

**201 Created**

```json
{
    "id": 10,
    "description": "Almoço",
    "amount": 45.90,
    "expense_date": "2026-07-15",
    "category": {
        "id": 1,
        "name": "Alimentação",
        "slug": "alimentacao"
    }
}
```

---

## Atualizar despesa

**PUT** `/expenses/{id}`

### Corpo da requisição

```json
{
    "category_id": 2,
    "description": "Combustível",
    "amount": 150.00,
    "expense_date": "2026-07-16"
}
```

### Resposta

**200 OK**

```json
{
    "id": 10,
    "description": "Combustível",
    "amount": 150.00,
    "expense_date": "2026-07-16",
    "category": {
        "id": 2,
        "name": "Transporte",
        "slug": "transporte"
    }
}
```

---

## Excluir despesa

**DELETE** `/expense/{id}`

### Resposta

**204 No Content**

---

# Códigos de resposta HTTP

| Código                       | Descrição                                |
| ---------------------------- | ---------------------------------------- |
| **200 OK**                   | Requisição executada com sucesso.        |
| **201 Created**              | Recurso criado com sucesso.              |
| **204 No Content**           | Recurso removido com sucesso.            |
| **400 Bad Request**          | Dados inválidos enviados na requisição.  |
| **401 Unauthorized**         | Token JWT ausente, inválido ou expirado. |
| **404 Not Found**            | Recurso não encontrado.                  |
| **422 Unprocessable Entity** | Erros de validação dos dados enviados.   |

---

# Observações

* Todas as despesas são vinculadas ao usuário autenticado.
* Um usuário não pode visualizar, editar ou excluir despesas de outros usuários.
* As respostas são retornadas no formato JSON.
* As datas devem ser enviadas no formato **YYYY-MM-DD**.
* Os valores monetários devem utilizar ponto (`.`) como separador decimal.
