<?php
/**
 * Helper Functions for Visualiser
 *
 * @package MyVideoRoomPlugin\VisualiserHelpers
 */

namespace MyVideoRoomPlugin\Visualiser;

/**
 * Class VisualiserHelpers
 * Provides Helper Functions to Visualiser
 */
class VisualiserHelpers {

	/**
	 * Correctly Render Names for apostrophes to avoid s's
	 *
	 * @param string $name - the Text String name you want to format.
	 *
	 * @return string - the formatted user name.
	 */
	public function name_format( string $name ): string {

		$pieces    = explode( ' ', $name );
		$last_word = array_pop( $pieces );

		$last = substr( $last_word, - 1 );
		if ( 's' === $last || 'S' === $last ) {
			return $name . ' ';
		} else {
			return $name . '\'s';
		}
	}
}
