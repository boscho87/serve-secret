install:
	composer install --working-dir=tools/phpstan
	composer install --working-dir=tools/rector
	composer install --working-dir=tools/php-cs-fixer

phpstan:
	./tools/phpstan/vendor/bin/phpstan analyse src tests --level 7
rector:
	./tools/rector/vendor/bin/rector process . --config ../../vendor/craftcms/rector/sets/craft-cms-50.php
cs-fixer:
	./_ci/cs_fixer.sh
