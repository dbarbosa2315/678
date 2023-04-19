DIR=$(dirname $0)

cd $DIR

git checkout ubuntu

git pull

/usr/bin/php -f /home/pdv/pdv_local/index.php tasks/updateDatabase