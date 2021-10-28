FROM php:7.4-cli
COPY analyzer.php .
WORKDIR .
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer 
RUN composer init --require=kassner/log-parser:~2.0
RUN apt-get -y update && apt-get -y install git && composer update

