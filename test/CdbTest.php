<?php

namespace Cdb\Test;

use Cdb\Reader;
use Cdb\Writer;

/**
 * Test the CDB reader/writer
 * @covers Cdb\Writer\PHP
 * @covers Cdb\Writer\DBA
 */
class CdbTest extends \PHPUnit_Framework_TestCase {
	/** @var string */
	private $phpCdbFile, $dbaCdbFile;

	protected function setUp() {
		parent::setUp();
		if ( !Reader::haveExtension() ) {
			$this->markTestSkipped( 'Native CDB support is not available.' );
		}
		$temp = sys_get_temp_dir();
		if ( !is_writable( $temp ) ) {
			$this->markTestSkipped( "Temp dir [$temp] isn't writable." );
		}
		$this->phpCdbFile = tempnam( $temp, get_class( $this ) . '_' );
		$this->dbaCdbFile = tempnam( $temp, get_class( $this ) . '_' );
	}

	protected function tearDown() {
		parent::tearDown();
		unlink( $this->phpCdbFile );
		unlink( $this->dbaCdbFile );
	}

	/**
	 * Make a random-ish string
	 * @return string
	 */
	private static function randomString() {
		$len = mt_rand( 1, 10 );
		$s = '';
		for ( $j = 0; $j < $len; $j++ ) {
			$s .= chr( mt_rand( 0, 255 ) );
		}

		return $s;
	}

	/**
	 * @covers Cdb\Reader\PHP
	 * @covers Cdb\Reader\DBA
	 */
	public function testCdbWrite() {
		$w1 = new Writer\PHP( $this->phpCdbFile );
		$w2 = new Writer\DBA( $this->dbaCdbFile );

		$data = array();
		for ( $i = 0; $i < 1000; $i++ ) {
			$key = self::randomString();
			$value = self::randomString();

			if ( !isset( $data[$key] ) ) {
				$w1->set( $key, $value );
				$w2->set( $key, $value );
				$data[$key] = $value;
			}
		}

		$w1->close();
		$w2->close();

		$this->assertEquals(
			md5_file( $this->phpCdbFile ),
			md5_file( $this->dbaCdbFile ),
			'same hash'
		);

		$r1 = new Reader\PHP( $this->phpCdbFile );
		$r2 = new Reader\DBA( $this->dbaCdbFile );

		foreach ( $data as $key => $value ) {
			$v1 = $r1->get( $key );
			$v2 = $r2->get( $key );

			$v1 = $v1 === false ? '(not found)' : $v1;
			$v2 = $v2 === false ? '(not found)' : $v2;

			# cdbAssert( 'Mismatch', $key, $v1, $v2 );
			$this->cdbAssert( "PHP error", $key, $v1, $value );
			$this->cdbAssert( "DBA error", $key, $v2, $value );
		}

		$r1->close();
		$r2->close();

		$r1 = new Reader\PHP( $this->phpCdbFile );
		$r2 = new Reader\DBA( $this->dbaCdbFile );

		$keys = array_keys( $data );
		$firstKey = array_shift( $keys );

		$this->assertTrue( $r1->exists( $firstKey ), 'PHP entry exists' );
		$this->assertTrue( $r2->exists( $firstKey ), 'DBA entry exists' );
		$this->assertFalse( $r1->exists( -1 ), 'PHP entry doesn\'t exists' );
		$this->assertFalse( $r2->exists( -1 ), 'DBA entry doesn\'t exists' );

		$firstKey1 = $r1->firstkey();
		$firstKey2 = $r2->firstkey();

		$this->assertEquals( $firstKey1, $firstKey, 'PHP Match first key' );
		$this->assertEquals( $firstKey2, $firstKey, 'DBA Match first key' );

		unset( $data[$firstKey] );
		for ( $j = 0, $max = count( $data ); $j < $max; $j++ ) {
			$this->assertEquals( $r2->nextkey(), $r1->nextkey(), 'nextkey match' );
		}
	}

	private function cdbAssert( $msg, $key, $v1, $v2 ) {
		$this->assertEquals(
			$v2,
			$v1,
			$msg . ', k=' . bin2hex( $key )
		);
	}
}
