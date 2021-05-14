<?php
/**
 * Tests the shortcode abstract class
 *
 * @package MyVideoRoomPluginTest\Library;
 */

declare(strict_types=1);

namespace MyVideoRoomPluginTest\Library\Class_HtmlTest;

use MyVideoRoomPlugin\Factory;
use MyVideoRoomPlugin\Library\HTML;
use PHPUnit\Framework\TestCase;

/**
 * Class HtmlTest/IDGeneratorTest
 */
class IDGeneratorTest extends TestCase {

	/**
	 * Reset the HTML after each test
	 */
	public function tearDown(): void {
		try {
			HTML::reset();
		} catch ( \Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			// allowed during testing.
		}
	}

	/**
	 * The HTML ID generator should create pseudo-random and unique strings.
	 *
	 * @test
	 * @covers \MyVideoRoomPlugin\Library\HTML
	 * @uses \MyVideoRoomPlugin\autoloader
	 * @uses \MyVideoRoomPlugin\Factory::get_instance()
	 */
	public function id_generator_creates_unique_strings() {
		$iterations_to_check = 100;
		$suffixes            = array();

		for ( $i = 0; $i < $iterations_to_check; ++$i ) {
			$suffix_id = $this->verify_id( $i );
			$this->assertArrayNotHasKey( $suffix_id, $suffixes );
			$suffixes[ $suffix_id ] = true;
		}

		$this->assertEquals( $iterations_to_check, count( $suffixes ) );
	}

	/**
	 * You can seed the id generator with a unique id
	 *
	 * @test
	 * @covers \MyVideoRoomPlugin\Library\HTML
	 * @uses \MyVideoRoomPlugin\autoloader
	 * @uses \MyVideoRoomPlugin\Factory::get_instance()
	 */
	public function id_generator_can_have_id_set() {
		$new_index = 500;

		HTML::set_id_index( $new_index );
		$this->verify_id( $new_index );
	}

	/**
	 * Resetting the generator should throw an exception to prevent it being used in normal code
	 *
	 * @test
	 * @covers \MyVideoRoomPlugin\Library\HTML
	 * @uses \MyVideoRoomPlugin\autoloader
	 */
	public function id_generator_reset() {
		$this->expectException( \Exception::class );
		$this->expectExceptionMessage( 'This is likely to lean to collision - probably only for tests!' );
		HTML::reset();
	}

	/**
	 * Test setting an ID too large will throw an exception
	 *
	 * @test
	 * @covers \MyVideoRoomPlugin\Library\HTML
	 * @uses \MyVideoRoomPlugin\autoloader
	 */
	public function id_generator_should_throw_exception_is_setting_big_id() {
		$this->expectException( \Exception::class );
		$this->expectExceptionMessage( 'Cannot exceed maximum ID count' );
		HTML::set_id_index( 10000 );
	}

	/**
	 * Test setting an ID smaller than the current setting will throw an error
	 *
	 * @test
	 * @covers \MyVideoRoomPlugin\Library\HTML
	 * @uses \MyVideoRoomPlugin\autoloader
	 */
	public function id_generator_should_throw_exception_is_setting_smaller_id() {
		$this->expectException( \Exception::class );
		$this->expectExceptionMessage( 'Cannot reduce the ID index as this will lead to collision' );
		HTML::set_id_index( 100 );
		HTML::set_id_index( 50 );
	}

	/**
	 * Verify a single id generation
	 *
	 * @param integer $index The expected index of the id.
	 *
	 * @return integer
	 */
	private function verify_id( int $index ): int {
		$id = Factory::get_instance( HTML::class, array( 'myvideoroom' ) )->get_id( 'test' );

		$suffix = explode( '_', $id )[2];

		$this->assertEquals( HTML::ID_LENGTH, strlen( $suffix ) );

		$suffix_parts = array();

		$look_up = array_combine(
			range( 'a', 'j' ),
			range( 0, 9 )
		);

		foreach ( str_split( $suffix ) as $item ) {
			$suffix_parts[] = $look_up[ $item ];
		}

		$suffix_id = (int) implode( '', $suffix_parts );

		$expected = $index * 99;

		while ( $expected > 10 ** HTML::ID_LENGTH ) {
			$expected -= 10 ** HTML::ID_LENGTH;
		}

		$this->assertEquals( $expected, $suffix_id );

		return $suffix_id;
	}
}
