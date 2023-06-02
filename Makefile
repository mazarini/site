
#*************************************************************************#
#                            G E N E R I Q U E                            #
#*************************************************************************#

install: composer-install yarn-install

validate: php-cs-fixer phpstan twig-validate phpmd test composer-validate yaml-validate twig-validate

update: composer-update yarn-update

#*************************************************************************#
#                                 Y A M L                                 #
#*************************************************************************#

yaml-validate: yaml-lint

yaml-lint:
	bin/console lint:yaml config
	bin/console lint:yaml .ide/phpstan.neon .ide/phpstan-baseline.neon

#*************************************************************************#
#                                 T W I G                                 #
#*************************************************************************#

twig-validate: twig-lint twigcs

twigcs:
	twigcs --config=.ide/twig_cs

twig-lint:
	bin/console lint:twig templates

#*************************************************************************#
#                         P H P - C S - F I X E R                         #
#*************************************************************************#

cs: php-cs-fixer

phpcs: php-cs-fixer

php-cs-fixer:
	~/.config/composer/vendor/bin/php-cs-fixer fix --config=.ide/php-cs-fixer.php

#*************************************************************************#
#                              P H P S T A N                              #
#*************************************************************************#

stan: phpstan

phpstan:
	~/.config/composer/vendor/bin/phpstan -vvv --configuration=.ide/phpstan.neon

#*************************************************************************#
#                                P H P M D                                #
#*************************************************************************#

md: phpmd

phpmd:
	~/.config/composer/vendor/bin/phpmd src,tests text .ide/rulesets.xml --baseline-file .ide/phpmd.baseline.xml

#*************************************************************************#
#                              P H P U N I T                              #
#*************************************************************************#

cover: cover-text
test: phpunit

phpunit: phpunit-init
	bin/phpunit --configuration .ide/phpunit.xml --cache-result-file var/cache/phpunit.result.cache

cover-html: phpunit-init
	XDEBUG_MODE=coverage bin/phpunit --configuration .ide/phpunit.xml --cache-result-file var/cache/phpunit.result.cache --coverage-html var/cover

cover-text: phpunit-init
	XDEBUG_MODE=coverage bin/phpunit --configuration .ide/phpunit.xml --cache-result-file var/cache/phpunit.result.cache --coverage-text

phpunit-init:
	cp var/data/empty.db var/data/test.db


#*************************************************************************#
#                               P H P D O C                               #
#*************************************************************************#

doc: phpDocumentor
phpdoc: phpDocumentor

phpDocumentor:
	phpDocumentor --config=.ide/phpdoc.xml

#*************************************************************************#
#                             C O M P O S E R                             #
#*************************************************************************#

composer-validate:
	composer validate --strict

composer-update:
	composer global update
	composer update

composer-install:
	composer self-update
	composer install

#*************************************************************************#
#                                 Y A R N                                 #
#*************************************************************************#

yarn-install:
	yarnpkg install
	yarnpkg dev

yarn-update:
	yarnpkg upgrade
