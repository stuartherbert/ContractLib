<?php

// =========================================================================
//
// tests/bootstrap.php
//              A helping hand for running our unit tests
//
// Author       Stuart Herbert
//              (stuart@stuartherbert.com)
//
// Copyright    (c) 2012-present Stuart Herbert
//              Released under the New BSD license
//
// =========================================================================

// our imports
use Phix_Project\Autoloader4\PSR0_Autoloader;
use Phix_Project\Autoloader4\Autoloader_Path;

// step 1: create the APP_TOPDIR constant that all components require
define('APP_TOPDIR', realpath(__DIR__ . '/../../php'));
define('APP_LIBDIR', realpath(__DIR__ . '/../../../vendor/php'));
define('APP_TESTDIR', realpath(__DIR__ . '/php'));

// step 2: find the autoloader, and install it
//
// we prefer (in this order):
//
// a) Composer's autoloader
// b) Phix's autoloader, in the vendor folder
// c) Phix's autoloader, installed globally
//
// PLEASE NOTE: we deliberately avoid creating any variables in this
// bootstrap file, to avoid contaminating the global scope
if (file_exists(__DIR__ . '/../../../vendor/autoload.php')) {
	// we can use Composer's autoloader
	require_once(__DIR__ . '/../../../vendor/autoload.php');
}
else if (file_exists(APP_LIBDIR . '/Phix_Project/Autoloader4/PSR0/Autoloader.php')) {
	// we are using Phix's autoloader in the vendor/ folder
	require_once(APP_LIBDIR . '/Phix_Project/Autoloader4/PSR0/Autoloader.php');
}
else {
	// assume there's a copy of Phix's autoloader installed globally
	require_once('Phix_Project/Autoloader4/PSR0/Autoloader.php');
}

// step 3: initalise the autoloader
//
// Phix's autoloader uses PHP's built-in include_path, and needs to be
// told where this component's code can be found
if (class_exists('Phix_Project\Autoloader4\PSR0_Autoloader')) {
	// enable the Phix autoloader
	PSR0_Autoloader::startAutoloading();

	// we want to start with an empty search path, to catch any missing
	// dependencies
	Autoloader_Path::emptySearchList();

	// search inside the vendor/ folder
	Autoloader_Path::searchFirst(APP_LIBDIR);

	// search inside our src/tests/unit-tests/ folder
	Autoloader_Path::searchFirst(APP_TESTDIR);

	// search inside our src/php/ folder
	Autoloader_Path::searchFirst(APP_TOPDIR);
}