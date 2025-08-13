FROM php:8.3-fpm
ARG TZ

RUN apt-get update && apt-get install -y \
    gnupg \
    g++ \
    procps \
    openssl \
    git \
    unzip \
    zlib1g-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg-dev \
    libicu-dev  \
    libxslt1-dev \
    acl \
    && echo 'alias sf="php bin/console"' >> ~/.bashrc


RUN apt-get update \
        &&  apt-get install -y libmcrypt-dev \
        &&  apt-get install -y libpng-dev \
        && apt-get install -y libpq-dev \
        && apt-get install -y libonig-dev \
        && apt-get install -y libcurl4-openssl-dev \
        && apt-get install -y libssl-dev \
        && pecl install mcrypt \
        && docker-php-ext-enable mcrypt \
        && pecl install mongodb \
        && docker-php-ext-enable mongodb \
        && docker-php-ext-install gd \
        && apt-get install -y iputils-ping \
        && docker-php-ext-install pdo mysqli pdo_pgsql pdo_mysql zip xsl intl opcache exif mbstring \
        && docker-php-ext-enable pdo_mysql pdo_pgsql mysqli


RUN apt-get update && \
    apt-get install -y \
    supervisor

RUN apt-get install git -y

RUN apt-get update \
    && apt-get install -y \
        librabbitmq-dev \
        libssh-dev \
    && docker-php-ext-install \
        bcmath \
        sockets \
    && pecl install amqp \
    && docker-php-ext-enable amqp

# Install Composer
#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
#        && composer --version

#SOAP
RUN apt-get update && \
    apt-get install -y libxml2-dev

RUN docker-php-ext-install soap && docker-php-ext-enable soap

#Composer
RUN echo "$(curl -sS https://composer.github.io/installer.sig) -" > composer-setup.php.sig \
        && curl -sS https://getcomposer.org/installer | tee composer-setup.php | sha384sum -c composer-setup.php.sig \
        && php composer-setup.php && rm composer-setup.php* \
        && chmod +x composer.phar && mv composer.phar /usr/bin/composer

## install xdebug
RUN pecl install xdebug \
        && docker-php-ext-enable xdebug

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
COPY docker-entrypoint.sh /opt/docker-entrypoint.sh

WORKDIR  /project-market/public

ENTRYPOINT ["bash", "/opt/docker-entrypoint.sh"]

#CMD ["php-fpm"]
