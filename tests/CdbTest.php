<?php

namespace Cdb\Test;

use Cdb\Reader;
use Cdb\Writer;

/**
 * Test the CDB reader/writer
 */
class CdbTest extends \PHPUnit\Framework\TestCase {
	/** @var string */
	private $phpCdbFile, $dbaCdbFile;

	protected function setUp() : void {
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

	protected function tearDown() : void {
		unlink( $this->phpCdbFile );
		unlink( $this->dbaCdbFile );
		parent::tearDown();
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

	private function cdbAssert( $msg, $key, $expected, $actual ) {
		$this->assertSame(
			$expected,
			$actual,
			$msg . ', k=' . bin2hex( $key )
		);
	}

	/**
	 * @covers Cdb\Reader::open
	 */
	public function testReaderOpen() {
		$this->assertInstanceOf(
			Reader::class,
			Reader::open( $this->phpCdbFile )
		);
	}

	/**
	 * @covers Cdb\Writer::open
	 */
	public function testWriterOpen() {
		$this->assertInstanceOf(
			Writer::class,
			Writer::open( $this->phpCdbFile )
		);
	}

	/**
	 * @covers Cdb\Util
	 * @covers Cdb\Writer
	 * @covers Cdb\Writer\PHP
	 * @covers Cdb\Writer\DBA
	 * @covers Cdb\Reader\PHP
	 * @covers Cdb\Reader\DBA
	 */
	public function testReadWrite() {
		$w1 = new Writer\PHP( $this->phpCdbFile );
		$w2 = new Writer\DBA( $this->dbaCdbFile );

		$data = [];
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

			$this->cdbAssert( 'PHP error', $key, $value, $v1 );
			$this->cdbAssert( 'DBA error', $key, $value, $v2 );
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
		$this->assertFalse( $r1->get( -1 ), 'PHP entry not found' );
		$this->assertFalse( $r2->get( -1 ), 'DBA entry not found' );

		$firstKey1 = $r1->firstkey();
		$firstKey2 = $r2->firstkey();

		$this->assertEquals( $firstKey1, $firstKey, 'PHP Match first key' );
		$this->assertEquals( $firstKey2, $firstKey, 'DBA Match first key' );

		unset( $data[$firstKey] );
		for ( $j = 0, $max = count( $data ); $j < $max; $j++ ) {
			$this->assertEquals( $r2->nextkey(), $r1->nextkey(), 'nextkey match' );
		}

		$this->assertFalse( $r1->nextkey() );
		$this->assertFalse( $r2->nextkey() );
	}

	/**
	 * @covers Cdb\Writer\PHP::finish
	 */
	public function testEmpty() {
		$w = new Writer\PHP( $this->phpCdbFile );
		$this->assertSame( null, $w->close() );
	}

	/**
	 * @covers Cdb\Writer::__destruct
	 */
	public function testDestruct() {
		$w = new Writer\PHP( $this->phpCdbFile );
		$this->assertInstanceOf(
			Writer\PHP::class,
			$w
		);
		$w = null;
	}
}
