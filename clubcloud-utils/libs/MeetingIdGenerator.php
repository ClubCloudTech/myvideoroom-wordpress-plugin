<?php

class ClubCloud_MeetingIdGenerator {
	public static function getMeetingHashFromUserId( $userId ) {
		$input = $userId ^ CC_MEETINGID_HASH;

		$items = str_split( (string) $input );
		$seed  = array_pop( $items );

		$items = self::seededShuffle( $items, $seed + substr( HASH, 3 ) );

		$items[] = $seed;

		$number = implode( '', $items );

		$number = substr( $number, 0, 3 ) . '-' .
		          substr( $number, 3, 4 ) . '-' .
		          substr( $number, 7 );

		return $number;
	}

	private static function seededShuffle( $items, $seed ) {
		mt_srand( $seed );
		for ( $i = count( $items ) - 1; $i > 0; $i -- ) {
			$j = mt_rand( 0, $i );
			list( $items[ $i ], $items[ $j ] ) = array( $items[ $j ], $items[ $i ] );
		}

		return $items;
	}

	public static function getUserIdFromMeetingHash( $hash ) {
		$items = str_split( (string) str_replace( '-', '', $hash ) );
		$seed  = array_pop( $items );

		$items = self::seededUnshuffle( $items, $seed + substr( HASH, 3 ) );

		$items[] = $seed;

		$number = implode( '', $items );

		return $number ^ CC_MEETINGID_HASH;
	}

	private static function seededUnshuffle( array $items, $seed ) {
		mt_srand( $seed );
		$indices = [];
		for ( $i = count( $items ) - 1; $i > 0; $i -- ) {
			$indices[ $i ] = mt_rand( 0, $i );
		}

		foreach ( array_reverse( $indices, true ) as $i => $j ) {
			list( $items[ $i ], $items[ $j ] ) = [ $items[ $j ], $items[ $i ] ];
		}

		return $items;
	}
}

