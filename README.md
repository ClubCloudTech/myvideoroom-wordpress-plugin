Contributing
============

```bash
vendor/bin/phpcs --config-set installed_paths vendor/wp-coding-standards/wpcs
  
vendor/bin/phpcbf -s --standard=WordPress build.php myvideoroom-plugin/
vendor/bin/phpcs -s --standard=WordPress build.php myvideoroom-plugin/
vendor/bin/phpunit
```
