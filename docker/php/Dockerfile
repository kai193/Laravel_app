FROM php:8.2-fpm
EXPOSE 5173

# 作成した設定ファイルをコンテナ内にコピー
COPY ./docker/php/php.ini /usr/local/etc/php/php.ini

# パッケージのインストール
RUN apt-get update \
    && apt-get -y install git zip unzip vim \
    wget xz-utils

RUN docker-php-ext-install pdo_mysql mysqli

RUN apt-get -y install libzip-dev \
    && docker-php-ext-install zip

RUN curl -fsSL https://deb.nodesource.com/setup_lts.x | bash -
RUN apt-get install -y nodejs

# composerのインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# node使いたいバージョン指定
ENV NODE_VERSION=20.9.0
RUN curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.34.0/install.sh | bash
ENV NVM_DIR=/root/.nvm
RUN . "$NVM_DIR/nvm.sh" && nvm install ${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm use v${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm alias default v${NODE_VERSION}
ENV PATH="/root/.nvm/versions/node/v${NODE_VERSION}/bin/:${PATH}"
RUN node --version
RUN npm --version

# コンテナ内で作業する際のディレクトリの指定
WORKDIR /var/www/html