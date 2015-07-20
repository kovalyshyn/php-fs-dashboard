FROM debian:jessie
MAINTAINER "Vitaly Kovalyshyn" <vitaly@kovalyshyn.pp.ua>

RUN mkdir -p /data/logs
COPY docker-entrypoint.sh /
COPY sql /data/sql
COPY www /data/www
RUN chmod -R 777 /data/www/app/storage 

VOLUME ["/data"]
ENTRYPOINT ["/docker-entrypoint.sh"]
CMD ["laravel"]
