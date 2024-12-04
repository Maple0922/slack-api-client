# PHP 8.3 Apache ベースイメージを使用
FROM php:8.3-apache

# システムの依存関係をインストール
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl

# PHP 拡張機能をインストール
RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd

# Composer のインストール
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# アプリケーションファイルをコピー
COPY . /var/www/html

# Apache の設定
RUN a2enmod rewrite

# 作業ディレクトリを設定
WORKDIR /var/www/html

# Composer の依存関係をインストール（composer.json がある場合）
RUN if [ -f "composer.json" ]; then composer install --no-interaction --no-dev --prefer-dist; fi

# パーミッションを設定
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Apache がフォアグラウンドで実行されるようにする
CMD ["apache2-foreground"]

# Composer の実行前に環境変数を設定
ENV COMPOSER_ALLOW_SUPERUSER=1

# composer install コマンドを修正
RUN if [ -f "composer.json" ]; then \
    composer install --no-interaction --no-dev --prefer-dist --ignore-platform-reqs; \
    fi
