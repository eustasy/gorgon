<?php

require_once __DIR__.'/../../src/vierbergenlars/SemVer/expression.php';
require_once __DIR__.'/../../src/vierbergenlars/SemVer/version.php';
require_once __DIR__.'/../../src/vierbergenlars/SemVer/SemVerException.php';

use vierbergenlars\SemVer\version;
use vierbergenlars\SemVer\expression;
use vierbergenlars\SemVer\SemVerException;

function test_version($version) {
	try {
		$valid = new version($version);
		$valid = $valid->valid();
	} catch (Exception $e) {
		$valid = false;
	}
	return $valid;
}
