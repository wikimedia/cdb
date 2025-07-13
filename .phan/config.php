<?php
$cfg = require __DIR__ . '/../vendor/mediawiki/mediawiki-phan-config/src/config-library.php';

$cfg['exception_classes_with_optional_throws_phpdoc'][] = \Cdb\Exception::class;

return $cfg;
