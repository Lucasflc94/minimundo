# RevisaTI — Sistema de Revisão Espaçada (PHP)

[![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=flat&logo=php&logoColor=white)](https://www.php.net/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![Status](https://img.shields.io/badge/status-em%20desenvolvimento-blue)](https://github.com/)

Sistema web básico em PHP para auxiliar no ciclo de revisão dos assuntos do
cronograma de estudos de TI. A cada assunto estudado, o sistema agenda revisões
automáticas para **7, 15 e 30 dias**, alertando quando estiverem pendentes ou
atrasadas.

---

## ✨ Funcionalidades

- Importação do cronograma extraído de PDF (`data/cronograma.json`).
- Cadastro/listagem de assuntos com filtros (mês, status, tipo, busca).
- Marcar assunto como estudado → gera 3 revisões (7/15/30 dias).
- Dashboard com revisões atrasadas, de hoje e próximas.
- Listagem de revisões com ações "Revisado" / "Pular".
- Alertas via CLI (`cron.php`) gravando em `data/alerts.log` (e e-mail opcional).
- Configurações de intervalos e e-mail de alerta.

---

## 🚀 Como executar localmente

Requisitos: **PHP 8.x** (extensão PDO opcional — o sistema usa apenas JSON em arquivo).
Nenhum banco de dados externo é necessário.

```bash
# 1. Clone este repositório
git clone https://github.com/SEU_USUARIO/SEU_REPOSITORIO.git
cd SEU_REPOSITORIO

# 2. Rode o servidor embutido do PHP
php -S 127.0.0.1:8080
```

Acesse **http://127.0.0.1:8080**. Na primeira execução, o cronograma é
importado automaticamente a partir de `data/cronograma.json`.

### Alertas diários (cron)

```bash
php cron.php
```

Exemplo de crontab, diariamente às 08:00:

```bash
0 8 * * * php /caminho/para/o/repositorio/cron.php >> /caminho/para/o/repositorio/data/alerts.log 2>&1
```

---

## 📁 Estrutura do projeto

```
revisati/
├── .github/                # Workflows de CI/CD (opcional)
├── assets/
│   └── style.css           # Estilos
├── data/
│   ├── cronograma.json     # Cronograma base importado (template)
│   ├── subjects.json       # Assuntos (gerado em runtime — não versionar)
│   ├── reviews.json        # Revisões (gerado em runtime — não versionar)
│   ├── settings.json       # Configurações (gerado em runtime — não versionar)
│   └── alerts.log          # Log de alertas (gerado em runtime — não versionar)
├── includes/
│   ├── header.php
│   └── footer.php
├── lib/
│   ├── bootstrap.php       # Inicialização da aplicação
│   ├── functions.php       # Funções auxiliares
│   ├── reviews.php         # Lógica de assuntos e revisões
│   └── storage.php         # Leitura/escrita JSON
├── index.php               # Dashboard
├── subjects.php            # Assuntos
├── reviews.php             # Revisões
├── import.php              # Importar cronograma
├── settings.php            # Configurações
├── cron.php                # Alertas (CLI)
├── review_action.php       # Ação de revisão
├── subject_action.php      # Ação de assunto
├── .gitignore              # Arquivos ignorados pelo git
├── LICENSE                 # Licença MIT
└── README.md               # Este arquivo
```

---

## 🛠️ Deploy no GitHub

1. Crie um repositório vazio no GitHub (não marque "Add a README file").
2. Na sua máquina, dentro da pasta do projeto:

```bash
git init
git add .
git commit -m "feat: primeira versão do RevisaTI"
git branch -M main
git remote add origin https://github.com/SEU_USUARIO/SEU_REPOSITORIO.git
git push -u origin main
```

> ⚠️ Os arquivos em `data/` que contêm dados do usuário (`subjects.json`, `reviews.json`, `settings.json`, `alerts.log`) estão no `.gitignore` e **não** serão enviados para o GitHub. Eles são criados automaticamente na primeira execução.

---

## 📋 Requisitos

- PHP 8.x
- Nenhum banco de dados externo (armazenamento em arquivos JSON)
- Extensão PDO opcional

---

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhos.
