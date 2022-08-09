FROM mysql:5.7.18

# Configure Mysql
COPY config/my.cnf /etc/alternatives/my.cnf

# DB scripts
COPY script/install.sh /docker-entrypoint-initdb.d/install.sh
COPY sql/db_dump.sql.gz /docker-entrypoint-initdb.d/db_dump.sql.gz
COPY sql/db_update.sql /docker-entrypoint-initdb.d/db_update.sql

WORKDIR /var/lib/mysql

# Mounts
VOLUME [ "/var/lib/mysql" ]