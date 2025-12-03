<?php
/**
 * @license GPL-2.0-or-later
 */

namespace Cdb\Writer;

use Cdb\Exception;
use Cdb\Writer;

/**
 * Writer class which uses the DBA extension (php-dba)
 */
class DBA extends Writer {
	/**
	 * @var resource|false|null The file handle
	 */
	protected $handle;

	/**
	 * Create the object and open the file.
	 *
	 * @param string $fileName
	 */
	public function __construct( string $fileName ) {
		$this->realFileName = $fileName;
		$this->tmpFileName = $fileName . '.tmp.' . mt_rand( 0, 0x7fffffff );
		$this->handle = dba_open( $this->tmpFileName, 'n', 'cdb_make' );
		if ( !$this->handle ) {
			throw new Exception( 'Unable to open CDB file for write "' . $fileName . '"' );
		}
	}

	public function set( $key, $value ): void {
		dba_insert( $key, $value, $this->handle );
	}

	public function close(): void {
		if ( $this->handle ) {
			dba_close( $this->handle );
			if ( $this->isWindows() ) {
				unlink( $this->realFileName );
			}
			if ( !rename( $this->tmpFileName, $this->realFileName ) ) {
				throw new Exception( 'Unable to move the new CDB file into place.' );
			}
		}
		$this->handle = null;
	}
}
