<?php
/**
 * A HTML checkbox input field
 *
 * @package MyVideoRoomPlugin/Module/Security/Settings
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\Security\Settings\Field;

use MyVideoRoomPlugin\Library\HTML;
use MyVideoRoomPlugin\Module\Security\Settings\Field;

/**
 * Class Field
 */
class Checkbox extends Field {

	/**
	 * If the checkbox is checked
	 *
	 * @var bool
	 */
	private bool $checked;

	/**
	 * Checkbox constructor.
	 *
	 * @param string  $key         The key/slug of the field.
	 * @param string  $label       The translated label.
	 * @param ?string $description The optional description.
	 * @param bool    $checked     If the checkbox is on or off.
	 */
	public function __construct( string $key, string $label, string $description, bool $checked = false ) {
		parent::__construct( $key, $label, $description );
		$this->checked = $checked;
	}

	/**
	 * Return the input as an HTML element string
	 *
	 * @param \MyVideoRoomPlugin\Library\HTML $html_library The html library, used for generating unique ids.
	 *
	 * @return string
	 */
	public function input_to_string( HTML $html_library ): string {
		ob_start();
		?>
			<input
				type="checkbox"
				name="<?php echo esc_attr( $html_library->get_field_name( $this->get_key() ) ); ?>"
				id="<?php echo esc_attr( $html_library->get_id( $this->get_key() ) ); ?>"

				<?php if ( $this->checked ) { ?>
				checked
				<?php } ?>

				<?php if ( $this->get_description() ) { ?>
				aria-describedby="<?php esc_attr( $html_library->get_description_id( $this->get_key() ) ); ?>"
				<?php } ?>
			/>
		<?php
		return ob_get_clean();
	}
}
