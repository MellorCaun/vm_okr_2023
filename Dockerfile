FROM php:8.3-fpm
# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql
# Set working directory
WORKDIR /var/www/html
# Copy existing application directory contents
COPY . /var/www/html
# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
#
#FROM php:8.3-cli
#
#RUN apt-get update && apt-get install -y \
#    git \
#    libzip-dev \
#    libicu-dev \
#    libfreetype6-dev \
#    libjpeg62-turbo-dev \
#    libpng-dev \
#    libbz2-dev \
#    libcurl4-openssl-dev \
#    libssl-dev \
#    pkg-config \
#    libxslt1-dev \
#    libedit-dev \
#    libxml2-dev \
#    libonig-dev \
#    g++ \
#    && rm -rf /var/lib/apt/lists/*
#
#RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
#    && docker-php-ext-install -j$(nproc) \
#    bcmath \
#    bz2 \
#    calendar \
#    curl \
#    exif \
#    gd \
#    gettext \
#    intl \
#    mbstring \
#    mysqli \
#    opcache \
#    pdo \
#    pdo_mysql \
#    shmop \
#    soap \
#    sockets \
#    sysvmsg \
#    sysvsem \
#    sysvshm \
#    xsl
#
#RUN pecl install pecl_http \
#    && docker-php-ext-enable http
#
#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
#
#WORKDIR /var/www/html
#
#COPY . .
#
#RUN composer install
#
#CMD php artisan serve --host=0.0.0.0 --port=8000
