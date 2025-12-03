<?php
/**
 * @license GPL-2.0-or-later
 */

namespace Cdb;

/**
 * Read data from a CDB file.
 * Native C and pure PHP implementations are provided.
 *
 * @see http://cr.yp.to/cdb.html
 */
abstract class Reader {
	/**
	 * Open a file and return a subclass instance
	 *
	 * @param string $fileName
	 * @return Reader
	 */
	public static function open( $fileName ): Reader {
		return self::haveExtension() ?
			new Reader\DBA( $fileName ) :
			new Reader\PHP( $fileName );
	}

	/**
	 * Returns true if the native extension is available
	 *
	 * @return bool
	 * @codeCoverageIgnore
	 */
	public static function haveExtension(): bool {
		if ( !function_exists( 'dba_handlers' ) ) {
			return false;
		}
		$handlers = dba_handlers();

		return in_array( 'cdb', $handlers ) && in_array( 'cdb_make', $handlers );
	}

	/**
	 * Close the file. Optional, you can just let the variable go out of scope.
	 */
	abstract public function close(): void;

	/**
	 * Get a value with a given key. Only string values are supported.
	 *
	 * @param string|int $key
	 * @return string|false
	 */
	abstract public function get( $key );

	/**
	 * Check whether key exists
	 *
	 * @param string $key
	 * @return bool
	 */
	abstract public function exists( $key ): bool;

	/**
	 * Fetch first key
	 *
	 * @return string|false
	 */
	abstract public function firstkey();

	/**
	 * Fetch next key
	 *
	 * @return string|false
	 */
	abstract public function nextkey();
}
