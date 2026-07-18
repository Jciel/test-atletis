# API de Gerenciamento de Despesas

API REST desenvolvida em **Yii2** para gerenciamento de despesas pessoais, utilizando autenticação via **JWT**, arquitetura baseada em **Services**, **Form Objects** e **Resources**, além de ambiente totalmente containerizado com **Docker**.

## Tecnologias utilizadas

* PHP 8.3
* Yii2 Framework
* MySQL 8
* Docker & Docker Compose
* Firebase PHP-JWT

---

# Como executar o projeto

## Pré-requisitos

Antes de iniciar, certifique-se de possuir instalado em sua máquina:

* Docker
* Docker Compose

---

## 1. Clonar o repositório

```bash
git clone <URL_DO_REPOSITORIO>
cd <NOME_DO_PROJETO>
```

---

## 2. Configurar as variáveis de ambiente

Copie o arquivo de exemplo:

```bash
cp .env.example .env
```

Caso necessário, ajuste as variáveis de ambiente conforme seu ambiente.

Exemplo:

```env
MYSQL_DATABASE=expenses
MYSQL_USER=user
MYSQL_PASSWORD=password
MYSQL_ROOT_PASSWORD=root

JWT_SECRET=sua_chave_secreta
JWT_EXPIRE=3600
```

---

## 3. Subir os containers

Execute:

```bash
docker compose up -d --build
```

Aguarde até que todos os containers estejam em execução.

---

## 4. Instalar as dependências do PHP

Caso ainda não tenham sido instaladas:

```bash
docker compose exec app composer install
```

---

## 5. Executar as migrations

Crie a estrutura do banco de dados:

```bash
docker compose exec app php yii migrate
```

Confirme a execução digitando:

```text
yes
```

---

## 6. Acessar a aplicação

Após a inicialização, a API estará disponível em:

```
http://localhost:8080
```

## Verificando a instalação

Após subir o ambiente, execute uma requisição para qualquer endpoint público, por exemplo:

```
POST /auth/register
```

Se a resposta for retornada corretamente, o ambiente está pronto para utilização.



# Testes Automatizados

## Visão geral

O projeto utiliza **Codeception 5** para execução dos testes automatizados.

Os testes estão organizados por responsabilidade:

```text
tests/
├── Unit/
│   ├── ExpenseServiceTest.php
│   ├── ExpenseCategoryServiceTest.php
│   ├── ExpenseFormTest.php
│   ├── ExpenseCategoryFormTest.php
│   ├── ExpenseSearchFormTest.php
│   └── RegisterFormTest.php
```

A suíte de testes cobre principalmente:

* Services (regras de negócio)
* Forms (validações de entrada)
* Regras de isolamento entre usuários
* Filtros, paginação e ordenação
* Validações de formulários

---

# Executando os testes

## Pré-requisitos

Antes de executar os testes, certifique-se que:

* Os containers Docker estão em execução
* As dependências PHP estão instaladas
* O banco de testes está configurado corretamente

Subir os containers:

```bash
docker compose up -d
```

---

## 5. Executar as migrations no DB de teste

Crie a estrutura do banco de dados:

```bash
docker compose exec app php yii migrate --db=testDb
```

Confirme a execução digitando:

```text
yes
```

---

# Executar todos os testes Unit

Para executar todos os testes unitários:

```bash
docker compose exec app php vendor/bin/codecept run Unit
```

Exemplo de saída esperada:

```text
Codeception PHP Testing Framework

✔ ExpenseServiceTest
✔ ExpenseCategoryServiceTest
✔ ExpenseFormTest
✔ ExpenseCategoryFormTest

OK (XX tests, XX assertions)
```

---

# Limpeza de cache do Codeception

Caso ocorra erro relacionado a grupos antigos de testes:

Exemplo:

```text
GroupManager: File or directory ... set in failed group does not exist
```

Execute:

```bash
docker compose exec app php vendor/bin/codecept clean
```

Depois:

```bash
docker compose exec app php vendor/bin/codecept run Unit
```

---

# Atualizar arquivos auxiliares do Codeception

Caso novos testes, módulos ou configurações sejam adicionados:

```bash
docker compose exec app php vendor/bin/codecept build
```




# Arquitetura do Projeto

A API foi desenvolvida seguindo o padrão **MVC (Model-View-Controller)** do Yii2, com uma separação clara entre 
responsabilidades para facilitar manutenção, testes e evolução da aplicação.
Além da estrutura padrão do framework, foram adicionadas camadas específicas para concentrar regras de negócio,
validações e transformação das respostas da API.

## Estrutura do projeto

```text
app/
├── commands/
├── components/
├── config/
├── controllers/
├── migrations/
├── models/
│   └── forms/
├── resources/
├── services/
└── web/
```

---

# Controllers

Os Controllers possuem apenas a responsabilidade de receber a requisição HTTP, validar os dados de entrada e delegar o 
processamento para a camada de Services.
Não existe regra de negócio implementada nos Controllers.

Exemplo de responsabilidades:

* Receber parâmetros da requisição
* Carregar os Form Objects
* Validar os dados recebidos
* Definir códigos HTTP da resposta
* Retornar os Resources produzidos pelos Services

---

# Models (ActiveRecord)

Os Models representam as entidades persistidas no banco de dados utilizando o ActiveRecord do Yii2.

Sua responsabilidade está limitada a:

* Mapeamento das tabelas
* Relacionamentos entre entidades
* Validações da entidade
* Behaviors
* Métodos relacionados à autenticação (User)

Toda a lógica de negócio permanece na camada de Services.

---

# Form Objects

Os Form Objects são responsáveis exclusivamente pela validação dos dados recebidos pela API.
Cada operação possui seu próprio objeto de validação, desacoplando as regras de entrada dos Models de persistência.

Exemplos:

* RegisterForm
* LoginForm
* ExpenseForm
* ExpenseSearchForm
* ExpenseCategoryForm

Essa abordagem evita utilizar os Models diretamente como objetos de entrada da API, permitindo regras específicas para 
cada operação.

---

# Services

Toda a regra de negócio da aplicação foi centralizada na camada de Services.
Cada Service é responsável por executar uma funcionalidade específica da aplicação, mantendo os Controllers simples e 
os Models focados apenas na persistência.

Exemplos:

* AuthService
* ExpenseService
* ExpenseCategoryService

Entre as responsabilidades dessa camada estão:

* Cadastro de usuários
* Autenticação
* Geração de JWT
* Validação das regras de negócio
* Persistência das entidades
* Busca de registros
* Exclusão de registros

Essa separação segue o princípio da **Responsabilidade Única (SRP)** do SOLID.

---

# BaseService

Foi criada uma classe BaseService contendo funcionalidades reutilizáveis utilizadas pelos demais Services.

Entre elas:

* saveOrFail()
* deleteOrFail()
* findOrFail()
* findOneOrFail()

Essa abordagem reduz duplicação de código e padroniza o tratamento de erros da aplicação.

---

# Resources/Tranformers

Os Resources são responsáveis por transformar os Models em respostas JSON.
Dessa forma, a estrutura da resposta da API fica desacoplada da estrutura interna das entidades.

Exemplos:

* ExpenseResource
* ExpenseCategoryResource

Isso facilita futuras alterações sem impactar os consumidores da API.

---

# Autenticação

A autenticação foi implementada utilizando **JWT (JSON Web Token)** através da biblioteca **firebase/php-jwt**.

Foi criado um componente próprio (`JwtService`) responsável por:

* geração dos tokens
* validação dos tokens
* configuração do algoritmo utilizado

As rotas protegidas utilizam autenticação Bearer Token através do componente `HttpBearerAuth`.

---

# Banco de dados

Toda a estrutura do banco foi criada utilizando **Yii Migrations**, permitindo versionamento do schema e reprodução 
completa do ambiente.

As entidades principais são:

* Users
* Expense
* ExpenseCategory

Os relacionamentos são garantidos através de Foreign Keys.

---

# Decisões Técnicas

Durante o desenvolvimento foram adotadas algumas decisões arquiteturais visando organização, reutilização de código e 
facilidade de manutenção.

## Separação da regra de negócio

Toda a lógica da aplicação foi implementada nos Services.
Os Controllers apenas recebem a requisição e retornam a resposta.
Essa abordagem facilita testes, manutenção e reutilização das regras de negócio.

---

## Utilização de Form Objects

Ao invés de utilizar diretamente os Models para validar requisições HTTP, foram criados Form Objects específicos para 
cada operação.
Essa separação evita acoplamento entre persistência e entrada de dados.

---

## Utilização de Resources

Foi criada uma camada de Resources para controlar exatamente quais informações são expostas pela API.
Isso evita retornar diretamente os Models e permite personalizar facilmente as respostas.

---

## Docker

O ambiente foi totalmente containerizado utilizando Docker e Docker Compose.
Isso garante facilidade de instalação e reprodutibilidade do ambiente de desenvolvimento.

---

## Organização em camadas

A arquitetura adotada pode ser resumida pelo seguinte fluxo:

```text
Request
    │
    ▼
Controller
    │
    ▼
Form Object
    │
    ▼
Service
    │
    ▼
Model (ActiveRecord)
    │
    ▼
Resource
    │
    ▼
JSON Response
```

Essa separação torna o projeto mais organizado, facilita manutenção, reduz acoplamento entre as camadas e melhora a 
legibilidade do código.

