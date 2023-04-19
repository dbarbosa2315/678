apt update

apt remove --purge snapd

systemctl disable systemd-resolved

systemctl disable bluetooth

systemctl --user mask evolution-addressbook-factory.service evolution-calendar-factory.service evolution-source-registry.service

apt install nginx memcached php7.0-fpm php7.0-cli php7.0-mysql php7.0-curl php7.0-mbstring php7.0-mcrypt php7.0-json php7.0-gd php7.0-memcached

apt install mariadb-server

apt install libnss-winbind samba cifs-utils

apt install phantomjs

chown www-data:www-data app/logs

ln -s /home/pdv/pdv_local /var/www/html/pdv_local

apt install numlockx

cp infra/etc/nginx/sites-enabled/default /etc/nginx/sites-enabled/default

cp infra/etc/php/7.0/fpm/pool.d/www.conf /etc/php/7.0/fpm/pool.d/www.conf

service php7.0-fpm restart

service nginx restart


