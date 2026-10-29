# Etapa 1: Compilar assets frontend (Vite / TailwindCSS)
FROM node:22-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Etapa 2: Servidor PHP 8.3 con Apache
FROM php:8.3-apache

# Instalar dependencias del sistema, certificados SSL y extensiones PHP requeridas
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    ca-certificates \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql bcmath exif gd zip intl \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurar Apache para apuntar a la carpeta /public de Laravel y habilitar .htaccess
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf \
    && sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}/!g' /etc/apache2/apache2.conf \
    && sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf \
    && echo '<Directory /var/www/html/public>\n    AllowOverride All\n    Require all granted\n</Directory>' >> /etc/apache2/apache2.conf

WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Copiar los assets compilados por Vite desde la etapa de frontend
COPY --from=frontend /app/public/build ./public/build
RUN if [ -f ./public/build/.vite/manifest.json ]; then cp ./public/build/.vite/manifest.json ./public/build/manifest.json; fi

# Instalar dependencias PHP para producción
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Crear carpetas necesarias y asignar permisos al usuario www-data de Apache
RUN mkdir -p storage/framework/cache \
       storage/framework/sessions \
       storage/framework/views \
       storage/logs \
       bootstrap/cache \
       public/uploads/productos \
    && chown -R www-data:www-data storage bootstrap/cache public/uploads \
    && chmod -R 775 storage bootstrap/cache public/uploads

# Configurar Apache para escuchar en el puerto dinámico de Render ($PORT o 80)
CMD sh -c "sed -i 's/80/'\${PORT:-80}'/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf && apache2-foreground"
