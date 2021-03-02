Contributing
============

```bash
vendor/bin/phpcs --config-set installed_paths vendor/wp-coding-standards/wpcs
  
vendor/bin/phpcbf -s --standard=WordPress build.php clubcloud-video-plugin/
vendor/bin/phpcs -s --standard=WordPress build.php clubcloud-video-plugin/
vendor/bin/phpunit
```
