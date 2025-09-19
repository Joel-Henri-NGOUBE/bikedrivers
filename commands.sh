echo "$MYSQL_USER"
php bin/console doctrine:database:create --if-not-exists
php bin/console lexik:jwt:generate-keypair --skip-if-exists
php bin/console make:migration
php bin/console doctrine:migrations:migrate -n
php -S 0.0.0.0:8000 -t public