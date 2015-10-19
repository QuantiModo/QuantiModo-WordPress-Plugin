#!/usr/bin/env bash

if [ -z "$1" ] || [ -z "$2" ] || [ -z "$3" ] || [ -z "$4" ]; then
    echo -e "All arguments should be specified\n"
    echo -e "Call script like this:\n"
    echo -e "$0 {{SITE_PREFIX}} {{CLIENT_ID}} {{CLIENT_SECRET}} {{DB_PASSWORD}}"
    exit
fi

SITE_PREFIX=$1
CLIENT_ID=$2
CLIENT_SECRET=$3
DB_PASSWORD=$4

NGINX_CONF_FILENAME="/etc/nginx/sites-available/${SITE_PREFIX}.quantimo.do"
DB_HOST="127.0.0.1"
DB_USER="root"

echo "Creating site ${SITE_PREFIX}.quantimo.do"
sudo ee site create ${SITE_PREFIX}.quantimo.do --wp

echo "Removing plugins and creating symlinks to quantipress.quantimo.do plugin directory"
#sudo rm -rf /var/www/${SITE_PREFIX}.quantimo.do/htdocs/wp-content/plugins
#sudo ln -s /var/www/quantipress.quantimo.do/htdocs/wp-content/plugins /var/www/${SITE_PREFIX}.quantimo.do/htdocs/wp-content/plugins
#sudo cp -Rf /var/www/quantipress.quantimo.do/htdocs/wp-content/uploads /var/www/${SITE_PREFIX}.quantimo.do/htdocs/wp-content/
sudo rsync -av /var/www/quantipress.quantimo.do/htdocs/wp-content/plugins/ /var/www/${SITE_PREFIX}.quantimo.do/htdocs/wp-content/plugins

echo "Removing themes and creating symlinks to quantipress.quantimo.do theme directory"
#sudo rm -rf /var/www/${SITE_PREFIX}.quantimo.do/htdocs/wp-content/themes/
#sudo ln -s /var/www/quantipress.quantimo.do/htdocs/wp-content/themes /var/www/${SITE_PREFIX}.quantimo.do/htdocs/wp-content/themes
#sudo cp -Rf /var/www/quantipress.quantimo.do/htdocs/wp-content/uploads /var/www/${SITE_PREFIX}.quantimo.do/htdocs/wp-content/
sudo rsync -av /var/www/quantipress.quantimo.do/htdocs/wp-content/themes/ /var/www/${SITE_PREFIX}.quantimo.do/htdocs/wp-content/themes

echo "Copying contents of quantipress.quantimo.do uploads"
sudo cp -Rf /var/www/quantipress.quantimo.do/htdocs/wp-content/uploads /var/www/${SITE_PREFIX}.quantimo.do/htdocs/wp-content/
#sudo cp -Rf /var/www/quantipress.quantimo.do/htdocs/wp-content/uploads /var/www/samuel.quantimo.do/htdocs/wp-content/


echo "Adding cron job to syncronize quantipress themes and plugins directories with newly created WP instance"
#write out current crontab
crontab -l > mycron
#echo new cron into cron file
echo "05 * * * * sudo rsync -av /var/www/quantipress.quantimo.do/htdocs/wp-content/plugins/ /var/www/${SITE_PREFIX}.quantimo.do/htdocs/wp-content/plugins" >> mycron
echo "05 * * * * sudo rsync -av /var/www/quantipress.quantimo.do/htdocs/wp-content/themes/ /var/www/${SITE_PREFIX}.quantimo.do/htdocs/wp-content/themes" >> mycron
#install new cron file
crontab mycron
rm mycron

echo "Setting permissions"
sudo chown -R www-data:www-data /var/www/
sudo chown -R root:root /var/www/quantipress.quantimo.do/wp-content/

echo "Copying Nginx server configuration file"
sudo cp /etc/nginx/sites-available/quantipress.quantimo.do /etc/nginx/sites-available/${SITE_PREFIX}.quantimo.do

echo "Updating Nginx server configuration file"	
sudo sed -i "s/quantipress.quantimo.do/${SITE_PREFIX}.quantimo.do/g" /etc/nginx/sites-available/${SITE_PREFIX}.quantimo.do
sudo service nginx reload

echo "Dumping quantipress database"
mysqldump --host=${DB_HOST} --user=${DB_USER} --password=${DB_PASSWORD} quantipress_quantimo_do > quantipress_quantimo_do.sql

echo "Importing quantipress database to new database"
mysql --host=${DB_HOST} --user=${DB_USER} --password=${DB_PASSWORD} ${SITE_PREFIX}_quantimo_do < quantipress_quantimo_do.sql

echo "Deleting dump"
rm quantipress_quantimo_do.sql

echo "Setting WP options"
mysql --host=${DB_HOST} --user=${DB_USER} --password=${DB_PASSWORD} ${SITE_PREFIX}_quantimo_do << EOF
UPDATE wp_options
SET option_value = 'https://${SITE_PREFIX}.quantimo.do/'
WHERE option_name IN ('siteurl', 'home');

UPDATE wp_options
SET option_value = '${CLIENT_SECRET}'
WHERE option_name IN ('qmwp_quantimodo_api_secret');

UPDATE wp_options
SET option_value = '${CLIENT_ID}'
WHERE option_name IN ('qmwp_quantimodo_api_id', 'blogname');
EOF

echo "Script executed succesfully"