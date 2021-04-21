Contributing
============

Before submitting any pull request please ensure that your code matches the wordpress coding styles.

Setup:
```bash
composer install
vendor/bin/phpcs --config-set installed_paths vendor/wp-coding-standards/wpcs
```

Before submitting pull request:
```bash
vendor/bin/phpcbf 
vendor/bin/phpcs
```
