FROM php:7.0-apache
RUN apt-get update && \
	apt-get install -y wget && \
    apt-get install -y libfreetype6-dev libjpeg62-turbo-dev && \
    docker-php-ext-install mysqli && \
    docker-php-ext-install mbstring && \
    docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/  &&  \
    docker-php-ext-install gd

RUN apt-get update \
  && apt-get install --no-install-recommends -y libpq-dev \
  && docker-php-ext-install pgsql \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/*

RUN apt-get update && apt-get install -y \
unzip \
libfreetype6-dev \
libjpeg62-turbo-dev \
libmcrypt-dev \
libpng-dev \
libaio1

RUN apt-get update && apt-get install -y libxml2-dev \
    && pear install -a SOAP-0.13.0 \
    && docker-php-ext-install soap;

RUN apt-get install -y \
    libzip-dev \
    zip \
    && docker-php-ext-configure zip --with-libzip \
    && docker-php-ext-install zip


# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# install Nano
RUN apt install nano
COPY instantclient-basiclite-linux.x64-19.3.0.0.0dbru.zip /tmp/
COPY instantclient-sdk-linux.x64-19.3.0.0.0dbru.zip /tmp/
COPY instantclient-sqlplus-linux.x64-19.3.0.0.0dbru.zip /tmp/
RUN unzip /tmp/instantclient-basiclite-linux.x64-19.3.0.0.0dbru.zip -d /usr/local/
RUN unzip /tmp/instantclient-sdk-linux.x64-19.3.0.0.0dbru.zip -d /usr/local/
RUN unzip /tmp/instantclient-sqlplus-linux.x64-19.3.0.0.0dbru.zip -d /usr/local/

RUN ln -s /usr/local/instantclient_19_3 /usr/local/instantclient
# fixes error "libnnz19.so: cannot open shared object file: No such file or directory"
RUN ln -s /usr/local/instantclient/lib* /usr/lib
RUN ln -s /usr/local/instantclient/sqlplus /usr/bin/sqlplus

RUN echo 'export LD_LIBRARY_PATH="/usr/local/instantclient"' >> /root/.bashrc
RUN echo 'umask 002' >> /root/.bashrc

RUN echo 'instantclient,/usr/local/instantclient' | pecl install oci8-2.2.0
RUN echo "extension=oci8.so" > /usr/local/etc/php/conf.d/php-oci8.ini

RUN a2enmod rewrite
RUN service apache2 restart

WORKDIR /var/www/html/
#RUN composer require econea/nusoap



COPY ./tpsonline /var/www/html/
RUN chmod -R 777 /var/www/html/assets/file_storage


ARG ENV
ARG USER
ARG PASS

RUN wget --http-user="$USER" --password="$PASS" http://10.8.3.93:2200/TPSOnline/"$ENV"/wsdl.zip -P /var/www/html/application/config/
RUN wget --http-user="$USER" --password="$PASS" http://10.8.3.93:2200/TPSOnline/"$ENV"/constants.php -P /var/www/html/application/config/
RUN wget --http-user="$USER" --password="$PASS" http://10.8.3.93:2200/TPSOnline/"$ENV"/config.php -P /var/www/html/application/config/
RUN wget --http-user="$USER" --password="$PASS" http://10.8.3.93:2200/TPSOnline/"$ENV"/database.php -P /var/www/html/application/config/
RUN unzip /var/www/html/application/config/wsdl.zip -d /var/www/html/application/config/

EXPOSE 80

