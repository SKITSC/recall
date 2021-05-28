
-- date: 26-05-2021
-- author: Iyad Al-Kassab @ SKITSC
-- description: create's db on deployement

CREATE DATABASE IF NOT EXISTS `plivo_backer`;

GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost'
    IDENTIFIED BY 'sa' WITH GRANT OPTION;