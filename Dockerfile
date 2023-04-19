FROM php:7.0-fpm

RUN apt-get update && apt-get install -y \
    nginx \
    memcached \
    mariadb-server \
    libnss-winbind \
    samba \
    cifs-utils \
    phantomjs \
    numlockx \
    libcurl4-openssl-dev \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libmcrypt-dev \
    && apt-get remove --purge -y snapd

RUN docker-php-ext-configure gd --with-webp-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install mysqli pdo pdo_mysql curl mbstring json gd \
    && docker-php-ext-enable gd \
    && docker-php-ext-install mcrypt \
    && docker-php-ext-enable mcrypt

COPY infra/etc/nginx/sites-enabled/default /etc/nginx/sites-enabled/default
COPY infra/etc/php/7.0/fpm/pool.d/www.conf /etc/php/7.0/fpm/pool.d/www.conf
COPY infra/etc/php/7.0/fpm/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
COPY infra/fastcgi-php.conf /etc/nginx/snippets/fastcgi-php.conf

EXPOSE 80

CMD ["bash", "-c", "service nginx start && /usr/local/sbin/php-fpm -R -O -c /etc/php/7.0/fpm && tail -f /var/log/nginx/error.log"]

# CMD ["bash", "-c", "/usr/local/sbin/php-fpm -c /etc/php/7.0/fpm && service nginx start && tail -f /var/log/nginx/error.log"]
# CMD ["bash", "-c", "/usr/local/sbin/php-fpm -D && service nginx start && tail -f /var/log/nginx/error.log"]