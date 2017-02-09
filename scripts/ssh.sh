sudo apt-get -y install mysql-server
sudo apt-get install php7-mysql
mysql_secure_installation
mysql -u root -ptoor -e "CREATE DATABASE domainleads";
cd /var/www/html
php artisan migrate
php artisan db:seed
