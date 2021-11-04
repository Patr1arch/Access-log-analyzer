FROM php:7.4-cli
COPY analyzer.php .
COPY AnalyzerTest.php .
COPY access_logs access_logs
WORKDIR .
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer 
RUN composer init --require=kassner/log-parser:~2.0
RUN docker-php-ext-install bcmath
RUN apt-get -y update
RUN apt-get install -y libzip-dev
RUN docker-php-ext-install zip
RUN apt-get -y install zip unzip
RUN apt-get -y install git && composer require phpunit/phpunit && composer update
RUN echo "alias phpunit='./vendor/bin/phpunit'" >> ~/.bashrc
