<?php
/**
 * @license GPL-2.0-or-later
 */

namespace Cdb;

/**
 * Write to a CDB file.
 * Native C and pure PHP implementations are provided.
 *
 * @see http://cr.yp.to/cdb.html
 */
abstract class Writer {
	/**
	 * File we'll be writing to when we're done
	 * @var string
	 */
	protected $realFileName;

	/**
	 * File we write to temporarily until we're done
	 * @var string
	 */
	protected $tmpFileName;

	/**
	 * Open a writer and return a subclass instance.
	 * The user must have write access to the directory, for temporary file creation.
	 *
	 * @param string $fileName
	 * @return Writer
	 */
	public static function open( $fileName ) {
		return Reader::haveExtension() ?
			new Writer\DBA( $fileName ) :
			new Writer\PHP( $fileName );
	}

	/**
	 * Set a key to a given value. The value will be converted to string.
	 *
	 * @param string $key
	 * @param string $value
	 */
	abstract public function set( $key, $value ): void;

	/**
	 * Close the writer object. You should call this function before the object
	 * goes out of scope, to write out the final hashtables.
	 */
	abstract public function close(): void;

	/**
	 * If the object goes out of scope, close it
	 */
	public function __destruct() {
		$this->close();
	}

	/**
	 * Are we running on Windows?
	 * @return bool
	 */
	protected function isWindows() {
		return strtoupper( substr( PHP_OS, 0, 3 ) ) === 'WIN';
	}
}
