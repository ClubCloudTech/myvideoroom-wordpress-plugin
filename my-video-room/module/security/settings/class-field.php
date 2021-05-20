<?php
/**
 * A HTML input field
 *
 * @package MyVideoRoomPlugin/Module/Security/Settings
 */

declare( strict_types=1 );

namespace MyVideoRoomPlugin\Module\Security\Settings;

use MyVideoRoomPlugin\Library\HTML;

/**
 * Class Field
 */
abstract class Field {

	/**
	 * The key of the settings field
	 *
	 * @var string
	 */
	private string $key;

	/**
	 * The translated label of the field
	 *
	 * @var string
	 */
	private string $label;

	/**
	 * The optional description of the field
	 *
	 * @var ?string
	 */
	private ?string $description;

	/**
	 * Field constructor.
	 *
	 * @param string  $key         The key/slug of the field.
	 * @param string  $label       The translated label.
	 * @param ?string $description The optional description.
	 */
	public function __construct( string $key, string $label, string $description = null ) {
		$this->key         = $key;
		$this->label       = $label;
		$this->description = $description;
	}

	/**
	 * Get the slug/key of the field
	 *
	 * @return string
	 */
	protected function get_key(): string {
		return $this->key;
	}

	/**
	 * Get the translated label of the field
	 *
	 * @return string
	 */
	protected function get_label(): string {
		return $this->label;
	}

	/**
	 * Get the description of the field
	 *
	 * @return string
	 */
	protected function get_description(): string {
		return $this->description;
	}

	/**
	 * Return the input as a HTML element string
	 *
	 * @param \MyVideoRoomPlugin\Library\HTML $html_library The html library, used for generating unique ids.
	 *
	 * @return string
	 */
	abstract protected function input_to_string( HTML $html_library ): string;

	/**
	 * Return the input, label and description as a string
	 *
	 * @param \MyVideoRoomPlugin\Library\HTML $html_library The html library, used for generating unique ids.
	 *
	 * @return string
	 */
	public function to_string( HTML $html_library ): string {
		ob_start();
		?>
		<label for="<?php echo esc_attr( $html_library->get_id( $this->get_key() ) ); ?>">
			<?php echo esc_html( $this->get_label() ); ?>
		</label>

		<?php
		//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $this->input_to_string( $html_library );
		?>

		<?php if ( $this->get_description() ) { ?>
			<p id="<?php esc_attr( $html_library->get_description_id( $this->get_key() ) ); ?>">
				<?php echo esc_html( $this->get_description() ); ?>
			</p>
		<?php } ?>

		<?php
		return ob_get_clean();
	}
}
