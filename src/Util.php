<?php
/**
 * @license GPL-2.0-or-later
 */

namespace Cdb;

/**
 * Common functions for readers and writers
 *
 * This is a port of D.J. Bernstein's CDB to PHP. It's based on the copy that
 * appears in PHP 5.3.
 */
class Util {
	/**
	 * Take a modulo of a signed integer as if it were an unsigned integer.
	 * $b must be less than 0x40000000 and greater than 0
	 *
	 * @param int $a
	 * @param int $b
	 * @return int
	 */
	public static function unsignedMod( $a, $b ): int {
		if ( $a & 0x80000000 ) {
			$m = ( $a & 0x7fffffff ) % $b + 2 * ( 0x40000000 % $b );

			return $m % $b;
		} else {
			return $a % $b;
		}
	}

	/**
	 * Shift a signed integer right as if it were unsigned
	 *
	 * @param int $a
	 * @param int $b
	 * @return int
	 */
	public static function unsignedShiftRight( $a, $b ): int {
		if ( $b == 0 ) {
			return $a;
		}
		if ( $a & 0x80000000 ) {
			return ( ( $a & 0x7fffffff ) >> $b ) | ( 0x40000000 >> ( $b - 1 ) );
		} else {
			return $a >> $b;
		}
	}

	/**
	 * The CDB hash function.
	 *
	 * @param string $s
	 * @return int
	 */
	public static function hash( $s ): int {
		$h = 5381;
		$len = strlen( $s );
		for ( $i = 0; $i < $len; $i++ ) {
			$h5 = ( $h << 5 ) & 0xffffffff;
			// Do a 32-bit sum
			// Inlined here for speed
			$sum = ( $h & 0x3fffffff ) + ( $h5 & 0x3fffffff );
			$h = (
				( $sum & 0x40000000 ? 1 : 0 )
				+ ( $h & 0x80000000 ? 2 : 0 )
				+ ( $h & 0x40000000 ? 1 : 0 )
				+ ( $h5 & 0x80000000 ? 2 : 0 )
				+ ( $h5 & 0x40000000 ? 1 : 0 )
			) << 30 | ( $sum & 0x3fffffff );
			$h ^= ord( $s[$i] );
			$h &= 0xffffffff;
		}

		return $h;
	}
}
