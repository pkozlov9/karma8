#!/usr/bin/env sh

# Install Additional Tools
apt-get update -y --fix-missing
apt-get install --no-install-recommends -y \
    wget git vim nano htop net-tools iproute2 tail head
apt update
apt -y install cron mc less gzip

# Install Mysql
docker-php-ext-install pdo pdo_mysql mysqli

# Cleanup to reduce image size
docker-php-source delete && \
    rm -rf /var/lib/apt/lists/* /tmp/*

# Set Timezone
ln -sf /usr/share/zoneinfo/Europe/Moscow /etc/localtime

# Generate DB dump
cp -R /var/www/docker/db/sql/db_dump_create.sql /var/www/docker/db/sql/db_dump.sql
php /var/www/app/generate_db_dump.php >> /var/www/docker/db/sql/db_dump.sql
gzip -f /var/www/docker/db/sql/db_dump.sql