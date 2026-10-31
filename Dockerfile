FROM php:8.2-apache

# Instala as dependências necessárias do PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Instala as extensões PDO do PHP
RUN docker-php-ext-install pdo pdo_pgsql

# Copia os arquivos do projeto para o diretório web
COPY . /var/www/html/

# Aplica as permissões de forma segura na pasta inteira
RUN chown -R www-data:www-data /var/www/html
