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

// namespace support
use Phix_Project\Autoloader4\PSR0_Autoloader;
use Phix_Project\Autoloader4\Autoloader_Path;

// step 1: create the APP_TOPDIR constant that all components require
define('APP_TOPDIR', realpath(__DIR__ . '/../../php'));
define('APP_LIBDIR', realpath(__DIR__ . '/../../../vendor/php'));
define('APP_TESTDIR', realpath(__DIR__ . '/php'));

// step 2: find the autoloader, and install it
//
// special case: this component provides the autoloader that all other
// Phix_Project components rely on, so we include our own copy
require_once(APP_LIBDIR . '/Phix_Project/Autoloader4/PSR0/Autoloader.php');
PSR0_Autoloader::startAutoloading();

// step 3: add the additional paths to the include path
Autoloader_Path::emptySearchList();
Autoloader_Path::searchFirst(APP_LIBDIR);
Autoloader_Path::searchFirst(APP_TESTDIR);
Autoloader_Path::searchFirst(APP_TOPDIR);
