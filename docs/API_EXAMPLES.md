# API Machado Meyer - Exemplos Práticos

## Comandos cURL para Testes

### 1. ARTICLES - Artigos Gerais

#### Listar todos os artigos (sem limite)
```bash
curl -s "http://machadomeyer.local/pt/api/articles/articles?nolimit=1" | head -c 1000
```

#### Listar 5 artigos em destaque
```bash
curl -s "http://machadomeyer.local/pt/api/articles/articles?featured=1&limit=5"
```

#### Buscar artigos por termo específico
```bash
curl -s "http://machadomeyer.local/pt/api/articles/articles?search=direito&limit=10"
```

#### Obter artigo específico por ID
```bash
curl -s "http://machadomeyer.local/pt/api/articles/article?id=123"
```

#### Últimos 10 artigos
```bash
curl -s "http://machadomeyer.local/pt/api/articles/latest?limit=10"
```

#### Artigos de uma categoria específica
```bash
curl -s "http://machadomeyer.local/pt/api/articles/articles?category_id=2&limit=20"
```

### 2. ARTICLESIJ - Inteligência Jurídica

#### Listar todos os artigos IJ (sem limite)
```bash
curl -s "http://machadomeyer.local/pt/api/articlesij/articlesij?nolimit=1" | head -c 1000
```

#### Artigos IJ em destaque
```bash
curl -s "http://machadomeyer.local/pt/api/articlesij/articlesij?featured=1&limit=10"
```

#### Artigos IJ por período específico
```bash
curl -s "http://machadomeyer.local/pt/api/articlesij/articlesij?date_filtering=range&start_date_range=2024-01-01&end_date_range=2024-12-31&limit=5"
```

#### Artigo IJ específico por ID
```bash
curl -s "http://machadomeyer.local/pt/api/articlesij/articleij?id=456"
```

#### Últimos artigos IJ
```bash
curl -s "http://machadomeyer.local/pt/api/articlesij/latest?limit=5"
```

#### Artigos IJ por categoria
```bash
curl -s "http://machadomeyer.local/pt/api/articlesij/category?category_id=150&limit=10"
```

#### Formato simplificado para IJ
```bash
curl -s "http://machadomeyer.local/pt/api/articlesij/articlesij?ij=1&limit=5"
```

### 3. ADVOGADOS - Lawyers

#### Listar todos os advogados (sem limite)
```bash
curl -s "http://machadomeyer.local/pt/api/advogados/advogados?nolimit=1" | head -c 1000
```

#### Advogados por estado
```bash
curl -s "http://machadomeyer.local/pt/api/advogados/advogados?estado=SP&limit=20"
```

#### Advogados por área de atuação
```bash
curl -s "http://machadomeyer.local/pt/api/advogados/advogados?area_de_atuacao=Corporativo&limit=15"
```

#### Advogados por escritório
```bash
curl -s "http://machadomeyer.local/pt/api/advogados/advogados?escritorio=São Paulo&limit=10"
```

#### Advogado específico por ID
```bash
curl -s "http://machadomeyer.local/pt/api/advogados/advogado?id=123"
```

#### Advogado específico por código
```bash
curl -s "http://machadomeyer.local/pt/api/advogados/advogado?codigo=ABC123"
```

#### Buscar advogados por nome
```bash
curl -s "http://machadomeyer.local/pt/api/advogados/advogados?search=João Silva&limit=5"
```

#### Listar áreas de atuação
```bash
curl -s "http://machadomeyer.local/pt/api/advogados/area?nolimit=1"
```

### 4. CATEGORIES - Categorias

#### Listar todas as categorias
```bash
curl -s "http://machadomeyer.local/pt/api/categories/categories?nolimit=1"
```

#### Categorias por nível hierárquico
```bash
curl -s "http://machadomeyer.local/pt/api/categories/categories?level=1&limit=20"
```

#### Subcategorias de uma categoria pai
```bash
curl -s "http://machadomeyer.local/pt/api/categories/categories?parent_id=1&limit=15"
```

### 5. USERS - Usuários (Requer Token)

#### Listar usuários (requer autenticação)
```bash
curl -s "http://machadomeyer.local/pt/api/users/users?token=l2ZkZUO2oOJxp3Fk6aZ4&limit=10"
```

### 6. LOGIN - Autenticação

#### Fazer login
```bash
curl -X POST "http://machadomeyer.local/pt/api/login/login?username=admin&password=senha123"
```

## URLs Diretas (Sem SEF)

### Exemplos com URLs tradicionais

#### Artigos gerais
```bash
curl -s "http://machadomeyer.local/index.php?option=com_api&app=articles&resource=articles&format=json&lang=pt&limit=5"
```

#### Artigos IJ
```bash
curl -s "http://machadomeyer.local/index.php?option=com_api&app=articlesij&resource=articlesij&format=json&lang=pt&nolimit=1" | head -c 1000
```

#### Advogados
```bash
curl -s "http://machadomeyer.local/index.php?option=com_api&app=advogados&resource=advogados&format=json&lang=pt&estado=SP&limit=10"
```

## Testando Performance e Limites

### Verificar tamanho de resposta sem limite
```bash
# Articles
curl -s "http://machadomeyer.local/pt/api/articles/articles?nolimit=1" > /tmp/all_articles.json
wc -c /tmp/all_articles.json

# ArticlesIJ
curl -s "http://machadomeyer.local/pt/api/articlesij/articlesij?nolimit=1" > /tmp/all_articlesij.json
wc -c /tmp/all_articlesij.json

# Advogados
curl -s "http://machadomeyer.local/pt/api/advogados/advogados?nolimit=1" > /tmp/all_advogados.json
wc -c /tmp/all_advogados.json
```

### Comparar com limite vs sem limite
```bash
# Com limite de 5
curl -s "http://machadomeyer.local/pt/api/articles/articles?limit=5" > /tmp/articles_limit5.json

# Sem limite
curl -s "http://machadomeyer.local/pt/api/articles/articles?nolimit=1" > /tmp/articles_nolimit.json

# Comparar tamanhos
ls -la /tmp/articles_*.json
```

## Processamento com jq (se disponível)

### Contar registros
```bash
curl -s "http://machadomeyer.local/pt/api/articles/articles?limit=10" | jq '.data.results | length'
```

### Extrair apenas títulos
```bash
curl -s "http://machadomeyer.local/pt/api/articles/latest?limit=5" | jq '.data.results[].title'
```

### Filtrar por campo específico
```bash
curl -s "http://machadomeyer.local/pt/api/advogados/advogados?limit=5" | jq '.data.results[] | {nome: .nome, estado: .estado}'
```

## Casos de Uso Comuns

### 1. Sincronização de dados
```bash
# Obter todos os artigos para sincronização
curl -s "http://machadomeyer.local/pt/api/articles/articles?nolimit=1" > backup_articles.json

# Obter apenas artigos modificados recentemente (se suportado)
curl -s "http://machadomeyer.local/pt/api/articles/articles?limit=100&listOrder=DESC"
```

### 2. Feed de notícias
```bash
# Últimos 20 artigos para feed
curl -s "http://machadomeyer.local/pt/api/articles/latest?limit=20"

# Artigos em destaque para homepage
curl -s "http://machadomeyer.local/pt/api/articles/articles?featured=1&limit=5"
```

### 3. Busca e filtros
```bash
# Buscar artigos por termo
curl -s "http://machadomeyer.local/pt/api/articles/articles?search=contrato&limit=10"

# Filtrar por múltiplos critérios
curl -s "http://machadomeyer.local/pt/api/articlesij/articlesij?featured=1&created_by=5&limit=5"
```

### 4. Listagem de profissionais
```bash
# Advogados por escritório e área
curl -s "http://machadomeyer.local/pt/api/advogados/advogados?escritorio=São Paulo&area_de_atuacao=Tributário&limit=20"

# Todos os advogados para diretório
curl -s "http://machadomeyer.local/pt/api/advogados/advogados?nolimit=1" > diretorio_completo.json
```

## Dicas de Debug

### Verificar logs
```bash
tail -f administrator/logs/apisef_debug.log
```

### Limpar cache
```bash
rm -rf administrator/cache/* cache/* tmp/*
```

### Testar conectividade
```bash
curl -I "http://machadomeyer.local/pt/api/articles/latest?limit=1"
```
