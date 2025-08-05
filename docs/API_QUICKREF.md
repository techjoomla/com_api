# API Machado Meyer - Referência Rápida

## URLs Base
- **SEF:** `http://machadomeyer.local/{lang}/api/{app}/{resource}`
- **Direta:** `http://machadomeyer.local/index.php?option=com_api&app={app}&resource={resource}&format=json&lang={lang}`

## Parâmetros Essenciais
- `lang`: `pt` ou `en`
- `limit`: Número de itens (padrão: 20)
- `nolimit=1`: Remove limitação
- `search`: Termo de busca

## Endpoints Principais

### ARTICLES (Artigos)
```
GET /pt/api/articles/articles              # Múltiplos artigos
GET /pt/api/articles/article?id={id}       # Artigo específico
GET /pt/api/articles/latest                # Últimos artigos
```

### ARTICLESIJ (Inteligência Jurídica)
```
GET /pt/api/articlesij/articlesij           # Múltiplos artigos IJ
GET /pt/api/articlesij/articleij?id={id}    # Artigo IJ específico
GET /pt/api/articlesij/latest               # Últimos artigos IJ
GET /pt/api/articlesij/category?category_id={id}  # Por categoria
```

### ADVOGADOS (Lawyers)
```
GET /pt/api/advogados/advogados             # Múltiplos advogados
GET /pt/api/advogados/advogado?id={id}      # Advogado por ID
GET /pt/api/advogados/advogado?codigo={cod} # Advogado por código
GET /pt/api/advogados/area                  # Áreas de atuação
```

### CATEGORIES
```
GET /pt/api/categories/categories           # Múltiplas categorias
```

### USERS (Requer token)
```
GET /pt/api/users/users?token={token}       # Usuários
```

### LOGIN
```
POST /pt/api/login/login?username={user}&password={pass}
```

## Exemplos Rápidos

### Buscar artigos
```bash
# Todos os artigos
curl "http://machadomeyer.local/pt/api/articles/articles?nolimit=1"

# Artigos em destaque
curl "http://machadomeyer.local/pt/api/articles/articles?featured=1&limit=5"

# Busca por termo
curl "http://machadomeyer.local/pt/api/articles/articles?search=direito&limit=10"
```

### Buscar advogados
```bash
# Advogados por estado
curl "http://machadomeyer.local/pt/api/advogados/advogados?estado=SP&limit=20"

# Advogado específico
curl "http://machadomeyer.local/pt/api/advogados/advogado?id=123"
```

### Artigos IJ
```bash
# Todos os artigos IJ
curl "http://machadomeyer.local/pt/api/articlesij/articlesij?nolimit=1"

# Por período
curl "http://machadomeyer.local/pt/api/articlesij/articlesij?date_filtering=range&start_date_range=2024-01-01&end_date_range=2024-12-31"
```

## Filtros Comuns

### Articles
- `featured=1`: Artigos em destaque
- `category_id={id}`: Por categoria
- `created_by={id}`: Por autor
- `ij=1`: Artigos de IJ

### ArticlesIJ
- `featured=1`: Em destaque
- `category_id={id}`: Subcategoria IJ
- `ij=1`: Formato simplificado
- `date_filtering=range`: Filtro por data

### Advogados
- `estado={UF}`: Por estado
- `escritorio={nome}`: Por escritório
- `area_de_atuacao={area}`: Por área
- `cargo={cargo}`: Por cargo

## Códigos de Status
- `200`: Sucesso
- `303`: Redirecionamento (SEF)
- `404`: Não encontrado
- `500`: Erro interno

## Debug
- Logs: `administrator/logs/apisef_debug.log`
- Cache: `rm -rf administrator/cache/* cache/* tmp/*`
