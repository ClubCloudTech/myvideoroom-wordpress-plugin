<?php
/**
 * Helper functions for HTML elements - i.e. forms
 *
 * @package MyVideoRoomPlugin\Library
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Library;

use Exception;
use MyVideoRoomPlugin\Plugin;

/**
 * Class HTML
 */
class HTML {

	const ID_LENGTH = 3;

	/**
	 * The current index of the ID generator
	 *
	 * @var ?int
	 */
	private static ?int $id_index = null;

	/**
	 * The current cached ID suffix
	 *
	 * @var string
	 */
	private string $id_suffix;

	/**
	 * The unique identifier for this element's parent
	 *
	 * @var string
	 */
	private string $identifier;

	/**
	 * Get the current index, if null then create it
	 *
	 * @return integer
	 */
	public static function get_or_create_current_index(): int {
		if ( ! self::$id_index ) {
			self::$id_index = (int) wp_rand( 1, 10 ** self::ID_LENGTH - 1 );
		}

		return self::$id_index;
	}

	/**
	 * HTML constructor.
	 *
	 * @param string $identifier The unique identifier for this element's parent.
	 *
	 * @throws Exception If we've tried to generate too many IDs.
	 */
	public function __construct( string $identifier ) {
		$current_index = self::get_or_create_current_index();

		$this->identifier = Plugin::PLUGIN_NAMESPACE . '_' . $this->sanitize_name( $identifier );

		if ( $current_index > 10 ** self::ID_LENGTH ) {
			throw new Exception( 'Cannot exceed maximum ID count' );
		}

		$seed = ( $current_index * 99 );

		while ( $seed > 10 ** self::ID_LENGTH ) {
			$seed -= 10 ** self::ID_LENGTH;
		}

		$seed_string = str_pad( (string) $seed, self::ID_LENGTH, '0', STR_PAD_LEFT );

		//phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$this->id_suffix = array_reduce(
			str_split( $seed_string ),
			function ( $carry, $item ) {
				return $carry . chr( $item + 97 );
			},
			''
		);

		++self::$id_index;
	}

	/**
	 * Explicitly set the starting id index - used for testing.
	 *
	 * @param int $id_index The new id index.
	 *
	 * @throws Exception If the new id is less than the current one.
	 * @throws Exception If the new id is greater than the maximum one.
	 */
	public static function set_id_index( int $id_index ) {
		if ( $id_index < self::$id_index ) {
			throw new Exception( 'Cannot reduce the ID index as this will lead to collision' );
		}

		if ( $id_index > 10 ** self::ID_LENGTH ) {
			throw new Exception( 'Cannot exceed maximum ID count' );
		}

		self::$id_index = $id_index;
	}

	/**
	 * Reset the id index - ONLY TO BE USED IN TESTS.
	 *
	 * @throws Exception Always throws an exception, if you are using a test you can handle this.
	 */
	public static function reset() {
		self::$id_index = 0;

		throw new Exception( 'This is likely to lean to collision - probably only for tests!' );
	}

	/**
	 * Get a namespaced field name
	 *
	 * @param string $field_name The field name.
	 *
	 * @return string
	 */
	public function get_field_name( string $field_name ): string {
		return $this->identifier . '_' . $this->sanitize_name( $field_name );
	}

	/**
	 * Get a unique and namespaced id
	 *
	 * @param string $field_name The field name.
	 *
	 * @return string
	 */
	public function get_id( string $field_name ): string {
		return $this->get_field_name( $field_name ) . '_' . $this->id_suffix;
	}

	/**
	 * Get a unique and namespaced id for a aria-describedby
	 *
	 * @param string $field_name The field name.
	 *
	 * @return string
	 */
	public function get_description_id( string $field_name ): string {
		return $this->get_id( $field_name ) . '_description';
	}

	/**
	 * Strips leading tabs from code, and renders in a HTML code block
	 *
	 * @param string $code The code to render.
	 */
	public function render_code_block( string $code ): string {
		$code_lines = explode( "\n", $code );

		preg_match_all( '/\t/', $code_lines[1], $matches );

		$indent_size = count( $matches[0] );

		$output_code = array_map(
			function ( $line ) use ( $indent_size ) {
				return preg_replace( '/^\t{' . $indent_size . '}/', '', $line );
			},
			$code_lines
		);

		return '<code>' . esc_html( trim( implode( "\n", $output_code ) ) ) . '</code>';
	}

	/**
	 * Ensure a string has a correct format for a html attribute
	 *
	 * @param string $name The input name.
	 *
	 * @return string
	 */
	private function sanitize_name( string $name ): string {
		return preg_replace(
			'/[^a-z0-9_]/',
			'',
			strtolower(
				str_replace( '-', '_', $name )
			)
		);
	}
}
