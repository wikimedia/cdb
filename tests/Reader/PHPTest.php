<?php
namespace Cdb\Test;

use Cdb\Reader\PHP;

/**
 * @covers Cdb\Reader\PHP
 */
class PHPTest extends \PHPUnit\Framework\TestCase {
	/** @var string */
	private $cdbFile;

	protected function setUp() {
		parent::setUp();
		$temp = sys_get_temp_dir();
		if ( !is_writable( $temp ) ) {
			$this->markTestSkipped( "Temp dir [$temp] isn't writable." );
		}
		$this->cdbFile = tempnam( $temp, get_class( $this ) . '_' );
	}

	protected function tearDown() {
		unlink( $this->cdbFile );
		parent::tearDown();
	}

	// File can't be opened
	public function testConstructorOpen() {
		$this->setExpectedException( 'Cdb\Exception' );
		// Ignore native error from fopen()
		// @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors
		@new PHP( '/tmp/non-exist' );
	}

	// File contains fewer than 2048 bytes
	public function testConstructorRead() {
		$this->setExpectedException( 'Cdb\Exception' );
		new PHP( $this->cdbFile );
	}
}
