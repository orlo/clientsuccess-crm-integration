FROM debian:stretch
MAINTAINER me
ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update && \
    apt-get install -y php7.0-cli php7.0-curl php7.0-json php7.0-xml php7.0-mysql php7.0-sqlite php7.0-mbstring \
        libapache2-mod-php7.0 curl lsb-release ca-certificates unzip apache2 && \
    rm -rf /var/lib/apt/lists/* && \
    rm /etc/apache2/sites-enabled/* && \
    a2enmod rewrite deflate 

RUN curl -so /usr/local/bin/composer https://getcomposer.org/composer.phar  && chmod 755 /usr/local/bin/composer && \
    echo GMT > /etc/timezone && dpkg-reconfigure --frontend noninteractive tzdata && \
    ln -sf /dev/stdout /var/log/apache2/access.log \
    && ln -sf /dev/stderr /var/log/apache2/error.log

COPY ./provisioning/apache-host /etc/apache2/sites-enabled/default.conf
COPY . /srv/site
WORKDIR /srv/site
RUN /usr/local/bin/composer -n install 

CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
EXPOSE 80
