#!/usr/bin/env php
<?php
/**
 * Build the WordPress plugin as a zip
 *
 * @package ClubCloudVideoPlugin
 */

/* Get real path for our folder */
$root_path = realpath( 'clubcloud-video-plugin' );

require_once __DIR__ . '/vendor/squizlabs/php_codesniffer/autoload.php';

$runner = new \PHP_CodeSniffer\Runner();

$_SERVER['argv'] = array( 'vendor/bin/phpcs', '-s', '--standard=WordPress', 'build.php', 'clubcloud-video-plugin/' );
$exit_code       = $runner->runPHPCS();

if ( $exit_code ) {
	throw new \Exception( 'PHP Checkstyle failed - cannot build' );
}

/* Initialize archive object */
$zip = new ZipArchive();
$zip->open( 'clubcloud-video.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE );

/**
 * Recurse over the root directory and get all the required files
 *
 * @var SplFileInfo[] $files
 */
$files = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator( $root_path ),
	RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ( $files as $name => $file ) {
	$file_path     = $file->getRealPath();
	$relative_path = substr( $file_path, strlen( $root_path ) + 1 );

	if ( strpos( $relative_path, 'Test/' ) === 0 ) {
		continue;
	}

	if ( ! $file->isDir() ) {
		$zip->addFile( $file_path, $relative_path );
	}
}

/* Zip archive will be created only after closing object */
$zip->close();

echo "Done!\n";
