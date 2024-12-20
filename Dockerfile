# ビルドステージ
FROM node:16 as build-stage

WORKDIR /app

# パッケージファイルをコピー
COPY package*.json ./

# 依存関係のインストール
RUN npm ci

# ソースコードをコピー
COPY . .

# Viteでビルド
RUN npm run build


FROM flyio/litefs:0.5 as litefs

FROM php:8.3-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    vim \
    sqlite3 \
    cron

RUN export EDITOR=vim
# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy LiteFS
COPY --from=litefs /usr/local/bin/litefs /usr/local/bin/litefs

# Copy application files
COPY . /var/www

# Install dependencies
RUN composer install

# Change ownership of our applications
RUN chown -R www-data:www-data /var/www

# Configure Apache to listen on port 8080
RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:8080>/' /etc/apache2/sites-available/000-default.conf

# Set the document root to public
RUN sed -i 's#/var/www/html#/var/www/public#g' /etc/apache2/sites-available/000-default.conf

# Enable mod_rewrite
RUN a2enmod rewrite

# Copy LiteFS config
COPY litefs.yml /etc/litefs.yml

RUN echo "EXTRA_OPTS='-L 5'" >> /etc/default/cron

# Set up cron job
RUN echo "* * * * * /usr/local/bin/php /var/www/artisan slack:notifyDevelopPoint >> /var/log/cron.log 2>&1" > /etc/cron.d/laravel

# Apply cron job
RUN crontab /etc/cron.d/laravel

# Create log file
RUN touch /var/log/cron.log

# restart cron
RUN service cron restart

# Start LiteFS and Apache
CMD litefs mount & apache2-foreground

RUN mkdir -p /var/lib/litefs/data && chown -R www-data:www-data /var/lib/litefs/data

RUN printenv | sed 's/^\(.*\)$/\1/g' > /etc/environment

COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh
CMD ["/usr/local/bin/start.sh"]
