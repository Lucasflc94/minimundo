FROM php:8.2-apache

# Instala as dependências necessárias para o PostgreSQL no PHP
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Instala as extensões PDO do PHP
RUN docker-php-ext-install pdo pdo_pgsql

# Copia os arquivos do projeto para o servidor web
COPY . /var/www/html/
RUN chown -www-data:www-data /var/www/html/* -R
