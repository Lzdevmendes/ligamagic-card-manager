# LigaMagic Card Manager

Portal administrativo para gestão de cartas colecionáveis (Magic: The Gathering,
Pokémon e Yu-Gi-Oh!), protegido por login e senha, com CRUD completo de cartas.

Desafio técnico: **backend em PHP puro (sem framework) + MySQL** e **frontend em
HTML5/CSS3/JavaScript vanilla (sem framework/biblioteca)**.

## Stack

| Camada | Tecnologia |
|---|---|
| Backend | PHP 8.3 puro, sem Composer/framework — Clean Architecture (Domain → Application → Infrastructure/Interface) |
| Banco de dados | MySQL 8.0 |
| Frontend | HTML5 + CSS3 + JavaScript vanilla (ES Modules nativos), sem build step |
| Ambiente | Docker Compose (nginx + PHP-Apache + MySQL + Adminer) |

## Como rodar o projeto

Pré-requisito: Docker e Docker Compose instalados.

```bash
git clone <url-do-repositorio>
cd ligamagic-card-manager
cp .env.example .env   # opcional: os valores padrão já funcionam
docker compose up -d --build
```

Isso sobe 4 serviços:

| Serviço | URL | Descrição |
|---|---|---|
| `web` | http://localhost:8080 | Frontend estático (nginx) |
| `api` | http://localhost:8091 | API REST (PHP + Apache) |
| `db` | localhost:3306 | MySQL, já com schema e massa de dados inicial |
| `adminer` | http://localhost:8092 | Inspeção do banco (servidor `db`, usuário `ligamagic`) |

O schema (`backend/database/schema.sql`) e o seed (`backend/database/seed.sql`) são
executados automaticamente pelo MySQL na primeira inicialização do volume. Para
recomeçar do zero: `docker compose down -v && docker compose up -d --build`.

Acesse **http://localhost:8080** e faça login com as credenciais abaixo.

### Credenciais de teste

```
E-mail: admin@ligamagic.local
Senha:  H9m9fz0MVa5S!8O
```

Usuário único, criado via seed SQL — não há cadastro de novos administradores
neste desafio.

## Endpoints da API

Todos os endpoints (exceto login) exigem sessão autenticada (cookie `PHPSESSID`).

| Método | Rota | Descrição |
|---|---|---|
| POST | `/api/auth/login` | Autentica e-mail/senha, abre sessão |
| POST | `/api/auth/logout` | Encerra a sessão |
| GET | `/api/auth/me` | Usuário autenticado atual |
| GET | `/api/cards` | Lista todas as cartas |
| GET | `/api/cards/{id}` | Detalhe de uma carta |
| POST | `/api/cards` | Cria uma carta |
| PUT | `/api/cards/{id}` | Atualiza uma carta |
| DELETE | `/api/cards/{id}` | Exclui uma carta |
| GET | `/api/editions?game={magic\|pokemon\|yugioh}` | Edições de um Card Game |

Erros seguem o formato `{"error":{"message":"...","fields":{...}}}`, com status
401 (não autenticado), 404 (não encontrado), 422 (validação) ou 500.

## Decisões de UX / produto

**1. Erro de rede no campo de Edição mostra estado de erro com "tentar
novamente", em vez de travar em loading.** O campo de Edição depende de um
fetch disparado ao selecionar o Card Game. Se essa requisição falhar (rede
instável, API fora do ar), o usuário — que pode ter pouca familiaridade com
tecnologia — não tem como saber se deve esperar mais, recarregar a página ou
já desistiu de usar o sistema. Em vez de deixar o campo preso em "Carregando
edições…" indefinidamente, ele passa a mostrar "Não foi possível carregar as
edições" com um botão "Tentar novamente" que refaz a mesma busca sem perder o
que já foi preenchido no restante do formulário.

**2. Confirmação de exclusão via modal próprio da aplicação, citando o nome da
carta, em vez do `confirm()` nativo do navegador.** O `confirm()` do browser é
genérico ("A página diz: tem certeza?"), não segue o visual do restante do
portal e não deixa claro *qual* carta está prestes a ser apagada quando a
lista tem várias linhas parecidas. Um modal customizado que repete o nome da
carta ("Tem certeza que deseja excluir 'Charizard'?") reduz o risco de exclusão
acidental por parte de um usuário menos atento — o custo de errar numa
exclusão é alto (não há lixeira/desfazer), então essa confirmação explícita
compensa a complexidade extra de implementar o modal na mão.

## Notas técnicas

- **Frontend e API em origens diferentes de propósito** (portas 8080 e 8091),
  para exercitar CORS explícito e cookie de sessão `HttpOnly`/`SameSite=Lax`
  entre origens — decisão deliberada para demonstrar esse conhecimento, não uma
  limitação. Como são o mesmo host (`localhost`), o cookie de sessão é
  considerado *same-site* e trafega normalmente sem precisar de HTTPS local.
- **Sem Composer nem build step no frontend**: autoload manual via
  `spl_autoload_register` no backend; frontend consumido direto via ES Modules
  nativos (`<script type="module">`), sem bundler.
- **Backend em camadas** (`Domain` → `Application` → `Infrastructure`/`Interface`):
  regras de negócio (validação de carta, raridade, edição pertencente ao jogo)
  isoladas de PDO e HTTP, para ficar claro o que é regra e o que é
  infraestrutura.
- Raridade é uma taxonomia fixa: `common`, `uncommon`, `rare`, `mythic`,
  `secret`. Imagem da carta é uma URL (texto), não upload de arquivo.
