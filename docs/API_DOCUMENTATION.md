# API Machado Meyer - Documentação Completa

## Índice
1. [Visão Geral](#visão-geral)
2. [Autenticação](#autenticação)
3. [Formatos de URL](#formatos-de-url)
4. [Parâmetros Globais](#parâmetros-globais)
5. [Endpoints Disponíveis](#endpoints-disponíveis)
6. [Exemplos de Uso](#exemplos-de-uso)
7. [Códigos de Resposta](#códigos-de-resposta)

---

## Visão Geral

A API do Machado Meyer fornece acesso programático aos dados do site, incluindo artigos, advogados, categorias e muito mais. A API suporta tanto URLs SEF amigáveis quanto URLs diretas tradicionais.

### Base URL
- **Produção:** `http://machadomeyer.local`
- **Desenvolvimento:** `http://localhost/machadomeyer`

### Formato de Resposta
Todas as respostas são retornadas em formato JSON.

---

## Autenticação

### Token de Acesso
Alguns endpoints requerem autenticação via token:
```
?token=l2ZkZUO2oOJxp3Fk6aZ4
```

### Login
Para obter tokens de acesso:
```
GET /pt/api/login/login?username=USER&password=PASS
```

---

## Formatos de URL

### 1. URLs SEF (Amigáveis)
```
/{lang}/api/{app}/{resource}[/{id}][?parameters]
```

**Exemplos:**
- `http://machadomeyer.local/pt/api/articles/articles`
- `http://machadomeyer.local/en/api/advogados/advogado/123`
- `http://machadomeyer.local/pt/api/articles/latest?limit=10`

### 2. URLs Diretas
```
/index.php?option=com_api&app={app}&resource={resource}&format=json&lang={lang}[&other_params]
```

**Exemplo:**
```
http://machadomeyer.local/index.php?option=com_api&app=articles&resource=articles&format=json&lang=pt&limit=10
```

---

## Parâmetros Globais

### Idioma
- **`lang`** (string): Idioma da consulta
  - `pt` - Português (padrão)
  - `en` - Inglês

### Paginação
- **`limit`** (int): Número de itens por página (padrão: 20)
- **`nolimit`** (int): Se `1`, remove limitação e retorna todos os itens
- **`offset`** (int): Deslocamento inicial (padrão: 0)
- **`limitstart`** (int): Alias para `offset`

### Ordenação
- **`listOrder`** (string): Ordem dos resultados
  - `ASC` - Crescente
  - `DESC` - Decrescente (padrão)

### Busca
- **`search`** (string): Termo de busca em títulos e conteúdo

---

## Endpoints Disponíveis

### 1. ARTICLES (Artigos Gerais)

#### 1.1 Articles - Múltiplos Artigos
**Endpoint:** `/api/articles/articles`

**Parâmetros específicos:**
- **`id`** (int): ID específico do artigo
- **`category_id`** (int): Filtrar por categoria
- **`featured`** (int): Artigos em destaque (1 = sim, 0 = não)
- **`created_by`** (int): ID do autor
- **`ij`** (int): Filtrar artigos de Inteligência Jurídica (1 = sim)

**Exemplo SEF:**
```
GET /pt/api/articles/articles?limit=10&featured=1
```

**Exemplo Direto:**
```
GET /index.php?option=com_api&app=articles&resource=articles&format=json&lang=pt&limit=10&featured=1
```

#### 1.2 Article - Artigo Individual
**Endpoint:** `/api/articles/article`

**Parâmetros obrigatórios:**
- **`id`** (int): ID do artigo

**Exemplo:**
```
GET /pt/api/articles/article?id=123
```

#### 1.3 Articles Latest - Últimos Artigos
**Endpoint:** `/api/articles/latest`

**Parâmetros específicos:**
- **`catid`** (int): Filtrar por categoria

**Exemplo:**
```
GET /pt/api/articles/latest?limit=5&catid=2
```

### 2. ARTICLESIJ (Inteligência Jurídica)

#### 2.1 ArticlesIJ - Múltiplos Artigos IJ
**Endpoint:** `/api/articlesij/articlesij`

**Parâmetros específicos:**
- **`id`** (int): ID específico do artigo
- **`category_id`** (int): Subcategoria dentro da IJ (diferente de 137)
- **`featured`** (int): Artigos em destaque
- **`created_by`** (int): ID do autor
- **`ij`** (int): Se `1`, retorna formato simplificado
- **`date_filtering`** (string): Tipo de filtro de data (`range`)
- **`start_date_range`** (string): Data inicial (formato: YYYY-MM-DD)
- **`end_date_range`** (string): Data final (formato: YYYY-MM-DD)

**Exemplo SEF:**
```
GET /pt/api/articlesij/articlesij?nolimit=1&featured=1
```

**Exemplo com filtro de data:**
```
GET /pt/api/articlesij/articlesij?date_filtering=range&start_date_range=2024-01-01&end_date_range=2024-12-31
```

#### 2.2 ArticleIJ - Artigo IJ Individual
**Endpoint:** `/api/articlesij/articleij`

**Parâmetros obrigatórios:**
- **`id`** (int): ID do artigo

**Exemplo:**
```
GET /pt/api/articlesij/articleij?id=456
```

#### 2.3 ArticlesIJ Latest - Últimos Artigos IJ
**Endpoint:** `/api/articlesij/latest`

**Parâmetros específicos:**
- **`categoryId`** (int): Filtrar por subcategoria

**Exemplo:**
```
GET /pt/api/articlesij/latest?limit=10
```

#### 2.4 ArticlesIJ Category - Por Categoria
**Endpoint:** `/api/articlesij/category`

**Parâmetros específicos:**
- **`category_id`** (int): ID da categoria (obrigatório)

**Exemplo:**
```
GET /pt/api/articlesij/category?category_id=150&limit=20
```

### 3. ADVOGADOS (Lawyers)

#### 3.1 Advogados - Múltiplos Advogados
**Endpoint:** `/api/advogados/advogados`

**Parâmetros específicos:**
- **`id`** (int): ID específico
- **`estado`** (string): Filtrar por estado
- **`escritorio`** (string): Filtrar por escritório
- **`area_de_atuacao`** (string): Filtrar por área de atuação
- **`cargo`** (string): Filtrar por cargo
- **`codigo`** (string): Código único do advogado

**Exemplo:**
```
GET /pt/api/advogados/advogados?estado=SP&limit=50
```

#### 3.2 Advogado - Advogado Individual
**Endpoint:** `/api/advogados/advogado`

**Parâmetros obrigatórios:**
- **`id`** (int) OU **`codigo`** (string): Identificador do advogado

**Exemplos:**
```
GET /pt/api/advogados/advogado?id=123
GET /pt/api/advogados/advogado?codigo=ABC123
```

#### 3.3 Area - Áreas de Atuação
**Endpoint:** `/api/advogados/area`

**Exemplo:**
```
GET /pt/api/advogados/area?nolimit=1
```

### 4. CATEGORIES (Categorias)

#### 4.1 Categories - Múltiplas Categorias
**Endpoint:** `/api/categories/categories`

**Parâmetros específicos:**
- **`parent_id`** (int): Filtrar por categoria pai
- **`level`** (int): Nível hierárquico

**Exemplo:**
```
GET /pt/api/categories/categories?parent_id=1&nolimit=1
```

### 5. USERS (Usuários)

#### 5.1 Users - Múltiplos Usuários
**Endpoint:** `/api/users/users`

**Requer autenticação via token.**

**Exemplo:**
```
GET /pt/api/users/users?token=l2ZkZUO2oOJxp3Fk6aZ4&limit=10
```

### 6. LOGIN (Autenticação)

#### 6.1 Login - Autenticação
**Endpoint:** `/api/login/login`

**Parâmetros obrigatórios:**
- **`username`** (string): Nome de usuário
- **`password`** (string): Senha

**Exemplo:**
```
POST /pt/api/login/login?username=admin&password=senha123
```

---

## Exemplos de Uso

### Caso 1: Listar todos os artigos em destaque
```bash
curl "http://machadomeyer.local/pt/api/articles/articles?featured=1&nolimit=1"
```

### Caso 2: Buscar artigos por termo
```bash
curl "http://machadomeyer.local/pt/api/articles/articles?search=direito&limit=20"
```

### Caso 3: Obter artigo específico
```bash
curl "http://machadomeyer.local/pt/api/articles/article?id=123"
```

### Caso 4: Listar advogados de São Paulo
```bash
curl "http://machadomeyer.local/pt/api/advogados/advogados?estado=SP&limit=50"
```

### Caso 5: Obter últimos artigos IJ
```bash
curl "http://machadomeyer.local/pt/api/articlesij/latest?limit=10"
```

### Caso 6: Buscar por período específico (IJ)
```bash
curl "http://machadomeyer.local/pt/api/articlesij/articlesij?date_filtering=range&start_date_range=2024-01-01&end_date_range=2024-12-31"
```

---

## Códigos de Resposta

### Estrutura de Resposta de Sucesso
```json
{
  "success": true,
  "data": {
    "results": [...],
    "total": 150
  }
}
```

### Estrutura de Resposta de Erro
```json
{
  "success": false,
  "message": "Descrição do erro",
  "err_code": 404,
  "err_msg": "Resource not found"
}
```

### Códigos HTTP
- **200** - Sucesso
- **303** - Redirecionamento (SEF URLs)
- **400** - Requisição inválida
- **404** - Recurso não encontrado
- **500** - Erro interno do servidor

---

## Notas Importantes

1. **Limite padrão:** 20 itens por consulta
2. **Sem limite:** Use `nolimit=1` para obter todos os registros
3. **Idiomas:** O sistema suporta `pt` (português) e `en` (inglês)
4. **Cache:** Limpe o cache após mudanças: `rm -rf administrator/cache/* cache/* tmp/*`
5. **Logs:** Verifique `administrator/logs/apisef_debug.log` para debug
6. **Autenticação:** Alguns endpoints requerem token válido
7. **Formato:** Sempre especifique `format=json` em URLs diretas

---

## Contato e Suporte

Para questões técnicas sobre a API, consulte os logs do sistema ou entre em contato com a equipe de desenvolvimento.

**Última atualização:** 5 de agosto de 2025
