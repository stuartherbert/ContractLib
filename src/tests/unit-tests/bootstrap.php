<?php

// =========================================================================
//
// tests/bootstrap.php
//              A helping hand for running our unit tests
//
// Author       Stuart Herbert
//              (stuart@stuartherbert.com)
//
// Copyright    (c) 2012 Stuart Herbert
//              Released under the New BSD license
//
// =========================================================================

// our imports
use Phix_Project\Autoloader4\PSR0_Autoloader;
use Phix_Project\Autoloader4\Autoloader_Path;

// step 1: create the APP_TOPDIR constant that all components require
define('PHIX_SRCDIR', realpath(__DIR__ . '/../../php'));
define('PHIX_COMPOSER_VENDOR_DIR', realpath(__DIR__ . '/../../../vendor'));
define('PHIX_PEAR_VENDOR_DIR', realpath(__DIR__ . '/../../../vendor/php'));
define('PHIX_TESTDIR', realpath(__DIR__ . '/php'));

// step 2: find the autoloader, and install it
//
// we support both Composer and PEAR-style packages
//
// * Composer-style packages use both autoloaders for unit-testing
// * PEAR-style packages only use Phix's autoloader for unit testing
//
// a) Composer's autoloader
// b) Phix's autoloader, in the (Composer) vendor folder
// c) Phix's autoloader, in the (PEAR) vendor folder
// d) Phix's autoloader, installed globally by PEAR
//
// PLEASE NOTE: we deliberately avoid creating any variables in this
// bootstrap file, to avoid contaminating the global scope
if (file_exists(PHIX_COMPOSER_VENDOR_DIR . '/autoload.php')) {
	// we can use Composer's autoloader
	require_once(PHIX_COMPOSER_VENDOR_DIR . '/autoload.php');
	define('PHIX_USING_COMPOSER', true);
}
else {
	// we're not using Composer, we'll have to autoload everything ourselves
	define('PHIX_USING_COMPOSER', false);
}

if (file_exists(PHIX_COMPOSER_VENDOR_DIR . '/phix/autoloader4/src/php/Phix_Project/Autoloader4/PSR0/Autoloader.php')) {
	// we are using Phix's autoloader in the (Composer) vendor/ folder
	require_once(PHIX_COMPOSER_VENDOR_DIR . '/phix/autoloader4/src/php/Phix_Project/Autoloader4/PSR0/Autoloader.php');
}
else if (file_exists(PHIX_PEAR_VENDOR_DIR . '/Phix_Project/Autoloader4/PSR0/Autoloader.php')) {
	// we are using Phix's autoloader in the (PEAR) vendor/ folder
	require_once(PHIX_PEAR_VENDOR_DIR . '/Phix_Project/Autoloader4/PSR0/Autoloader.php');
}
else {
	// assume there's a copy of Phix's autoloader installed globally
	require_once('Phix_Project/Autoloader4/PSR0/Autoloader.php');
}

// step 3: initalise the autoloader
//
// Composer's autoloader cannot see our unit tests, so we use our own
// PSR0-compliant autoloader to work around that

// enable the Phix autoloader
PSR0_Autoloader::startAutoloading();

// we want to start with an empty search path, to catch any missing
// dependencies
Autoloader_Path::emptySearchList();

// search inside the vendor/ folder
PHIX_USING_COMPOSER || Autoloader_Path::searchFirst(PHIX_PEAR_VENDOR_DIR);

// search inside our src/tests/unit-tests/ folder
Autoloader_Path::searchFirst(PHIX_TESTDIR);

// search inside our src/php/ folder
PHIX_USING_COMPOSER || Autoloader_Path::searchFirst(PHIX_SRCDIR);