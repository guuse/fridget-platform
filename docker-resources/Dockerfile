FROM node:10 as front-end

WORKDIR /app/
ADD docker-resources/application.tgz /app
RUN mkdir -p public/build
RUN if [ -f package.json ]; then \
    npm install && \
    npm run build; fi

FROM connectholland/baseimages:php73-apache

WORKDIR /app/
ADD docker-resources/application.tgz /app
COPY --from=front-end /app/public/build /app/public/build
COPY docker-resources/composer-auth.json /root/.composer/auth.json
COPY docker-resources/apache.conf /etc/apache2/sites-enabled/000-default.conf

RUN if [ -f composer.json ]; then \
    composer install -o --no-dev --no-scripts; fi

ENV WRITEABLE_WEB_DIRECTORIES "var/log:var/cache"
