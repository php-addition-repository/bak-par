ARG TARGET_PHP_VERSION=8.1
FROM php:${TARGET_PHP_VERSION}-cli-alpine

ARG SERVICE_DIR="./.docker/workspace"
ARG SHARED_DIR="./.docker/shared"
ARG BUILD_DIR="/opt/build"
COPY ${SHARED_DIR} ${BUILD_DIR}

###########################################################################
# Setup
###########################################################################

RUN apk update && apk add --no-cache \
    bash \
    && rm -rf /tmp/*

###########################################################################
# Set Timezone
###########################################################################

ARG TZ=UTC
ENV TZ ${TZ}

RUN ${BUILD_DIR}/scripts/set-timezone.sh "${TZ}"

###########################################################################
# non-root user + access
###########################################################################

ARG APP_USER=app
ARG APP_GROUP=app
ARG APP_USER_ID=1000
ARG APP_GROUP_ID=1000
ARG APP_CODE_PATH="/app"

RUN addgroup -g ${APP_GROUP_ID} -S ${APP_GROUP} \
	&& adduser -u ${APP_USER_ID} -S ${APP_USER} -G ${APP_GROUP} \
    && ${BUILD_DIR}/scripts/create-user-dir.sh ${APP_USER_ID} ${APP_GROUP_ID} "${APP_CODE_PATH}"

WORKDIR ${APP_CODE_PATH}

###########################################################################
# PHP extras
###########################################################################

# install php extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/

RUN install-php-extensions xdebug zip intl mbstring

RUN apk add --no-cache make g++ autoconf \
    && docker-php-source extract \
    && pecl install ds \
    && docker-php-ext-enable ds \
    && docker-php-source delete \
    && apk del make g++ autoconf && rm -rf /tmp/*

# php config
#COPY ${SERVICE_DIR}/config/php/conf.d/*  /usr/local/etc/php/conf.d/

###########################################################################
# Composer
###########################################################################

# install composer
RUN apk add --no-cache git git-subtree curl openssh-client \
	&& curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && rm -rf /tmp/*

###########################################################################
# Project
###########################################################################

# execute below as non-root user
USER ${APP_USER}

# make sure PATH contains all global composer scripts
ENV PATH="/home/${APP_USER}/.composer/vendor/bin:${PATH}"

# make sure PATH contains all composer scripts
ENV PATH="./vendor/bin:${PATH}"
