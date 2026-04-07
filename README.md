# LG Electronics — Dashboard de Eficiência de Produção

Dashboard para acompanhamento da eficiência de produção da **Planta A**, exibindo métricas de produtividade das linhas: Geladeira, Máquina de Lavar, TV e Ar-Condicionado, referente a **janeiro de 2026**.

## Tecnologias

- **Backend:** Laravel 7 (PHP 7.4)
- **Banco de Dados:** MySQL 8.0
- **Frontend:** Blade + Bootstrap 4 + Chart.js
- **Infraestrutura:** Docker + Docker Compose

## Como Rodar o Projeto

### Pré-requisitos

- [Docker](https://www.docker.com/) e [Docker Compose](https://docs.docker.com/compose/) instalados

### Passo a passo

```bash
# 1. Clonar o repositório
git clone https://github.com/ThiagoDuarteC/teste-tecnico-lg.git
cd teste-tecnico-lg

# 2. Subir os containers
docker-compose up -d --build

# 3. Instalar dependências
docker exec -it teste-tecnico-lg-app-1 composer install

# 4. Configurar o ambiente
docker exec -it teste-tecnico-lg-app-1 cp .env.example .env
docker exec -it teste-tecnico-lg-app-1 php artisan key:generate

# 5. Executar migrations e seeders
docker exec -it teste-tecnico-lg-app-1 php artisan migrate --seed

# 6. Acessar a aplicação
# http://localhost:8000 ou http://localhost:8000/dashboard
```

## Estrutura da Tabela

```sql
CREATE TABLE produtividades (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    linha_produto VARCHAR(255) NOT NULL,
    quantidade_produzida INT NOT NULL,
    quantidade_defeitos INT NOT NULL,
    data_producao DATE NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### INSERTs de exemplo

```sql
INSERT INTO produtividades (linha_produto, quantidade_produzida, quantidade_defeitos, data_producao) VALUES
('Geladeira',         850, 12, '2026-01-01'),
('Geladeira',         920, 18, '2026-01-02'),
('Máquina de Lavar',  780, 25, '2026-01-01'),
('Máquina de Lavar',  690, 10, '2026-01-02'),
('TV',                950, 30, '2026-01-01'),
('TV',                870, 22, '2026-01-02'),
('Ar-Condicionado',   600, 8,  '2026-01-01'),
('Ar-Condicionado',   720, 15, '2026-01-02');
```

> Os dados também podem ser gerados automaticamente via `php artisan db:seed`, que cria 31 registros por linha (um por dia de janeiro/2026).

## Arquitetura

O projeto segue a estrutura **Controller → Service → Model**:

```
app/
├── Http/Controllers/
│   └── ProdutividadeController.php   # Recebe a request e delega ao Service
├── Services/
│   └── ProdutividadeService.php      # Lógica de negócio e cálculo de eficiência
└── Produtividade.php                 # Model Eloquent com $fillable e accessor
```

## Funcionalidades

- Cards de resumo: linhas ativas, total produzido, total de defeitos e eficiência geral
- Gráfico de barras: produção vs defeitos por linha
- Gráfico horizontal: eficiência por linha com cores condicionais
- Tabela de detalhamento com badges de eficiência
- Filtro por linha de produção via dropdown
