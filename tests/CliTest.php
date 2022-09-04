<?php
namespace Cdb\Test;

use Cdb\Cli;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Cdb\Cli
 */
class CliTest extends TestCase {
	private const FIXTURE = __DIR__ . '/fixture/example.cdb';
	private $out;

	protected function setUp(): void {
		$this->out = fopen( 'php://memory', 'rw' );
	}

	private function getOutput() {
		fseek( $this->out, 0 );
		return fread( $this->out, 2048 );
	}

	public function testContructor() {
		$cli = new Cli( $this->out, [] );
		$this->assertInstanceOf( Cli::class, $cli );
		$this->assertSame( '', $this->getOutput(), 'output' );
		$this->assertSame( 0, $cli->getExitCode(), 'exit code' );
	}

	public function testRunNoargs() {
		$cli = new Cli( $this->out, [ 'cdb' ] );
		$cli->run();

		$this->assertStringContainsString( 'usage: cdb', $this->getOutput(), 'output' );
		$this->assertSame( 1, $cli->getExitCode(), 'exit code' );
	}

	public function testRunGet() {
		$cli = new Cli( $this->out, [ 'cdb', self::FIXTURE, 'get', 'answer' ] );
		$cli->run();

		$this->assertSame( "42\n", $this->getOutput(), 'output' );
		$this->assertSame( 0, $cli->getExitCode(), 'exit code' );
	}

	public function testRunList() {
		$cli = new Cli( $this->out, [ 'cdb', self::FIXTURE, 'list' ] );
		$cli->run();

		$this->assertSame( "foo\nanswer\nfalse\ntrue\n", $this->getOutput(), 'output' );
		$this->assertSame( 0, $cli->getExitCode(), 'exit code' );
	}

	public function testRunListMax() {
		$cli = new Cli( $this->out, [ 'cdb', self::FIXTURE, 'list', '3' ] );
		$cli->run();

		$this->assertSame(
			"foo\nanswer\nfalse\n\n(more keys exist…)\n",
			$this->getOutput(),
			'output'
		);
		$this->assertSame( 0, $cli->getExitCode(), 'exit code' );
	}

	public function testRunMatch() {
		$cli = new Cli( $this->out, [ 'cdb', self::FIXTURE, 'match', '/a.+e/' ] );
		$cli->run();

		$this->assertSame( "answer\nfalse\n", $this->getOutput(), 'output' );
		$this->assertSame( 0, $cli->getExitCode(), 'exit code' );
	}
}
