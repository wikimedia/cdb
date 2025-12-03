<?php
/**
 * @license GPL-2.0-or-later
 */

namespace Cdb\Reader;

use Cdb\Exception;
use Cdb\Reader;

/**
 * Reader class which uses the DBA extension (php-dba)
 */
class DBA extends Reader {
	/**
	 * @var resource|false|null The file handle
	 */
	protected $handle;

	public function __construct( string $fileName ) {
		$this->handle = dba_open( $fileName, 'r-', 'cdb' );
		if ( !$this->handle ) {
			throw new Exception( 'Unable to open CDB file "' . $fileName . '"' );
		}
	}

	public function close(): void {
		if ( $this->handle ) {
			dba_close( $this->handle );
		}
		$this->handle = null;
	}

	public function get( $key ) {
		return dba_fetch( (string)$key, $this->handle );
	}

	public function exists( $key ): bool {
		return dba_exists( (string)$key, $this->handle );
	}

	public function firstkey() {
		return dba_firstkey( $this->handle );
	}

	public function nextkey() {
		return dba_nextkey( $this->handle );
	}
}
