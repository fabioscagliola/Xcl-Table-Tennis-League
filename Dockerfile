FROM ubuntu:latest

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && \
    apt-get install -y ca-certificates curl git gnupg2 lsb-release software-properties-common unzip wget zip && \
    add-apt-repository ppa:ondrej/php && \
    curl -sL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get update && \
    apt-get install -y nginx nodejs php8.3 php8.3-cli php8.3-fpm php8.3-mbstring php8.3-mysql php8.3-xml

RUN wget https://get.symfony.com/cli/installer -O - | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    mv composer.phar /usr/local/bin/composer

WORKDIR /var/www/html/bend
COPY ./bend /var/www/html/bend
COPY ./.env.prod /var/www/html/bend/.env
RUN composer install

WORKDIR /var/www/html/fend
COPY ./fend /var/www/html/fend
RUN npm install
RUN npm run build

COPY ./nginx.conf /etc/nginx/nginx.conf

COPY xttl.sh /xttl.sh
RUN chmod +x /xttl.sh

CMD ["/xttl.sh"]

