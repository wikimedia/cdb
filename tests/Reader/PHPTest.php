<?php
namespace Cdb\Test;

use Cdb\Exception;
use Cdb\Reader\PHP;

/**
 * @covers Cdb\Reader\PHP
 */
class PHPTest extends \PHPUnit\Framework\TestCase {
	/** @var string */
	private $cdbFile;

	protected function setUp() : void {
		parent::setUp();
		$temp = sys_get_temp_dir();
		if ( !is_writable( $temp ) ) {
			$this->markTestSkipped( "Temp dir [$temp] isn't writable." );
		}
		$this->cdbFile = tempnam( $temp, get_class( $this ) . '_' );
	}

	protected function tearDown() : void {
		unlink( $this->cdbFile );
		parent::tearDown();
	}

	// File can't be opened
	public function testConstructorOpen() {
		if ( is_callable( [ $this, 'setExpectedException' ] ) ) {
			// PHPUnit 4.8
			$this->setExpectedException( Exception::class );
		} else {
			// PHPUnit 6+
			$this->expectException( Exception::class );
		}
		// Ignore native error from fopen()
		// @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors
		@new PHP( '/tmp/non-exist' );
	}

	// File contains fewer than 2048 bytes
	public function testConstructorRead() {
		if ( is_callable( [ $this, 'setExpectedException' ] ) ) {
			// PHPUnit 4.8
			$this->setExpectedException( Exception::class );
		} else {
			// PHPUnit 6+
			$this->expectException( Exception::class );
		}
		new PHP( $this->cdbFile );
	}
}
