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
use MyVideoRoomPlugin\Module\Security\Settings\Field\SelectOption;

/**
 * Class Field
 */
class Select extends Field {

	/**
	 * The options
	 *
	 * @var SelectOption[]
	 */
	private array $options;

	/**
	 * The currently selected value
	 *
	 * @var ?string
	 */
	private ?string $selected_value;

	/**
	 * Checkbox constructor.
	 *
	 * @param string         $key               The key/slug of the field.
	 * @param string         $label             The translated label.
	 * @param ?string        $description       The optional description.
	 * @param SelectOption[] $options           If the checkbox is on or off.
	 * @param ?string        $selected_value    The currently selected value.
	 */
	public function __construct( string $key, string $label, string $description, array $options = array(), string $selected_value = null ) {
		parent::__construct( $key, $label, $description );
		$this->options = $options;
		$this->selected_value = $selected_value;
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
			<select
				type="checkbox"
				name="<?php echo esc_attr( $html_library->get_field_name( $this->get_key() ) ); ?>"
				id="<?php echo esc_attr( $html_library->get_id( $this->get_key() ) ); ?>"

				<?php if ( $this->get_description() ) { ?>
				aria-describedby="<?php esc_attr( $html_library->get_description_id( $this->get_key() ) ); ?>"
				<?php } ?>
			>
				<?php
				foreach ( $this->options as $option ) {
					$selected = '';

					if ( $option->get_value() === $this->selected_value ) {
						$selected = ' selected';
					}

					echo '<option value="' . esc_attr( $option->get_value() ) . '"' . esc_attr( $selected ) . '>' . esc_html( $option->get_name() ) . '</option>';
				}
				?>
			</select>
		<?php
		return ob_get_clean();
	}
}
