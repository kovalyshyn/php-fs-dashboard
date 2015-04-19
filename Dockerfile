FROM busybox
MAINTAINER "Vitaly Kovalyshyn" <vitaly@kovalyshyn.pp.ua>

RUN mkdir -p /data/logs
COPY sql /data/sql
COPY www /data/www
COPY docker-entrypoint.sh /docker-entrypoint.sh
RUN chmod -R 777 /data/www/app/storage 

VOLUME ["/data"]
ENTRYPOINT ["/docker-entrypoint.sh"]
