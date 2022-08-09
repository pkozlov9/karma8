SET GLOBAL time_zone = 'Europe/Moscow';
SET GLOBAL wait_timeout = 30000;
SET GLOBAL net_read_timeout = 30000;
SET GLOBAL net_write_timeout = 30000;
SET GLOBAL interactive_timeout = 30000;
SET GLOBAL connect_timeout = 30000;
DROP DATABASE IF EXISTS karma8_db;
DROP USER IF EXISTS karma8_db_user;
CREATE DATABASE karma8_db;
CREATE USER 'karma8_db_user'@'%' IDENTIFIED BY 'secret';
GRANT ALL PRIVILEGES ON karma8_db.* TO 'karma8_db_user'@'%';
FLUSH PRIVILEGES;