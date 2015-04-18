FROM busybox
MAINTAINER "Vitaly Kovalyshyn" <vitaly@kovalyshyn.pp.ua>

RUN mkdir -p /data/logs
COPY www /data/www
VOLUME ["/data"]

ENTRYPOINT ["tail", "-f", "/data/www/app/storage/logs/laravel.log"]
