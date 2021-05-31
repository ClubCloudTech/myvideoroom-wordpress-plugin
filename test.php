<?php

class Test
{
	private const OUTPUT_BASE = 36;

	public function __construct() {
		$nonce = \base_convert(
			(string) \rand( 100000, 1000000 - 1 ),
			10,
			16
		);

		$this->seed = round( \base_convert( $nonce, 16, 10 ) / 5000 )  - 100;
	}

	private function bring_into_range( int $value ): int {
		while ( $value < 100 ) {
			$value += 900;
		}

		while ( $value >= 1000 ) {
			$value -= 900;
		}

		return $value;
	}

	public function get_hash_from_id(int $id): string {
		$seed = $this->seed;

		$randomisation_factor = ( ( ( $id + $seed ) * 13 ) % self::OUTPUT_BASE );

		$number = $this->bring_into_range( $id + $randomisation_factor + $seed );

		return base_convert( $randomisation_factor, 10, self::OUTPUT_BASE ) . base_convert( $number, 10, self::OUTPUT_BASE );
	}

	public function get_id_from_hash( string $hash ): int {
		$factor = base_convert( $hash[0], self::OUTPUT_BASE, 10 );
		$number = base_convert( substr( $hash, 1 ), self::OUTPUT_BASE, 10 );

		$value = $number - $factor - $this->seed;

		return $this->bring_into_range( $value );
	}
}

$hashes = array();

$test = new Test();

for ( $i = 100; $i < 1000; ++$i ) {

	$hash   = $test->get_hash_from_id( $i );
	$actual = $test->get_id_from_hash( $hash );

	echo $i . ' ' . $hash . ' ' . $actual . "\n";
}
