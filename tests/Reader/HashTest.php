<?php
namespace Cdb\Test\Reader;

use Cdb\Reader\Hash;

/**
 * @covers Cdb\Reader\Hash
 */
class HashTest extends \PHPUnit_Framework_TestCase {

	public function testConstructor_fail() {
		$this->setExpectedException( 'InvalidArgumentException' );
		new Hash( 'not an array' );
	}

	public function testClose() {
		$reader = new Hash( array( 'foo' => 'FOO' ) );
		$reader->close();

		$this->assertFalse( $reader->get( 'foo' ) );
	}

	public function testGet() {
		$reader = new Hash( array( 'foo' => 'FOO' ) );

		$this->assertSame( 'FOO', $reader->get( 'foo' ) );
		$this->assertFalse( $reader->get( 'xyz' ) );
	}

	public function testExists() {
		$reader = new Hash( array( 'foo' => 'FOO' ) );

		$this->assertTrue( $reader->exists( 'foo' ) );
		$this->assertFalse( $reader->exists( 'xyz' ) );
	}

	public function testFirstKey() {
		$reader = new Hash( array(
			'one' => 'ONE',
			'two' => 'TWO',
		) );

		$this->assertSame( 'one', $reader->firstkey() );
		$this->assertSame( 'one', $reader->firstkey() );

		$reader->nextkey();
		$this->assertSame( 'one', $reader->firstkey() );
	}

	public function testFirstKey_empty() {
		$reader = new Hash( array() );

		$this->assertFalse( $reader->firstkey() );
	}

	public function testNextKey() {
		$reader = new Hash( array(
			'one' => 'ONE',
			'two' => 'TWO',
		) );

		$this->assertSame( 'one', $reader->nextkey() );
		$this->assertSame( 'two', $reader->nextkey() );

		$reader->firstkey();
		$this->assertSame( 'two', $reader->nextkey() );
		$this->assertFalse( $reader->nextkey() );

		$reader->firstkey();
		$this->assertSame( 'two', $reader->nextkey() );
	}

}
