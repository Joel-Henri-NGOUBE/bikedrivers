sleep 90
php bin/console doctrine:database:create --if-not-exists
php bin/console lexik:jwt:generate-keypair --skip-if-exists
while true; do php bin/console make:migration && break; done
while true; do php bin/console doctrine:migrations:migrate -n && break; done
php -S 0.0.0.0:8000 -t public