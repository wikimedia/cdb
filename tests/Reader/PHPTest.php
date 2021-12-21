<?php
namespace Cdb\Test;

use Cdb\Exception;
use Cdb\Reader\PHP;

/**
 * @covers Cdb\Reader\PHP
 */
class PHPTest extends \PHPUnit\Framework\TestCase {
	private $cdbFile;

	protected function setUp(): void {
		parent::setUp();
		$temp = sys_get_temp_dir();
		if ( !is_writable( $temp ) ) {
			$this->markTestSkipped( "Temp dir [$temp] isn't writable." );
		}
		$this->cdbFile = tempnam( $temp, get_class( $this ) . '_' );
	}

	protected function tearDown(): void {
		unlink( $this->cdbFile );
		parent::tearDown();
	}

	public function testConstructorOpen() {
		$this->expectException( Exception::class );
		// File can't be opened
		// Ignore native error from fopen()
		@new PHP( '/tmp/non-exist' );
	}

	public function testConstructorRead() {
		$this->expectException( Exception::class );
		// File contains fewer than 2048 bytes
		new PHP( $this->cdbFile );
	}
}
