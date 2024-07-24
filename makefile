install:
	composer install --working-dir=tools/phpstan
	composer install --working-dir=tools/php-cs-fixer

phpstan:
	./tools/phpstan/vendor/bin/phpstan analyse src tests --level 7
cs-fixer:
	./_ci/cs_fixer.sh
