<?php
namespace Cdb\Test;

use Cdb\Reader;
use Cdb\Reader\DBA;

/**
 * @covers Cdb\Reader\DBA
 */
class DBATest extends \PHPUnit\Framework\TestCase {

	protected function setUp() {
		parent::setUp();
		if ( !Reader::haveExtension() ) {
			$this->markTestSkipped( 'Native CDB support is not available.' );
		}
	}

	public function testConstructor() {
		$this->setExpectedException( 'Cdb\Exception' );
		// Silence native error from dba_open()
		// @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors
		@new DBA( '/tmp/non-exist' );
	}
}
