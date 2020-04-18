<?php
require_once __DIR__ . '/../vendor/autoload.php';

$dbw = new Cdb\Writer\PHP( __DIR__ . '/fixture/example.cdb' );
$dbw->set( 'foo', 'bar' );
$dbw->set( 'answer', '42' );
$dbw->set( 'false', '0' );
$dbw->set( 'true', '1' );
$dbw->close();
