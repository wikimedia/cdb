<?php
namespace Cdb\Test;

use Cdb\Exception;
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
		if ( is_callable( [ $this, 'setExpectedException' ] ) ) {
			// PHPUnit 4.8
			$this->setExpectedException( Exception::class );
		} else {
			// PHPUnit 6+
			$this->expectException( Exception::class );
		}
		// Silence native error from dba_open()
		// @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors
		@new DBA( '/tmp/non-exist' );
	}
}
