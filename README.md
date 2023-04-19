### Setup mariadb ###

mysql_secure_installation

service mariadb restart

UPDATE mysql.user SET plugin = 'mysql_native_password', authentication_string = PASSWORD('GCsDEWPTXdCskabv') WHERE User = 'root';
FLUSH PRIVILEGES;
