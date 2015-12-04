<?php

/**
 * Web Application Config File
 *
 * The Web Application Configuration
 *
 * @category   WebApp.Config
 * @package    config.inc.php
 * @site       www.biggleswadesc.org
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 * @generated  Saturday 13th September 2014, 19:08
 */// Display Errors [Default: Disabled]
$config['core']['errors']      = 1;

// Debug Mode [Default: Disabled]
$config['core']['debug']       = 1;

// Maintenance  [Default: Disabled]
$config['core']['maintenance'] = 0;

// Database [Default: bwsc]
$config['mysql']['db']         = 'bwsc';

/*
* Read Settings
*/
// Username[Default: root]
$config['mysql']['r']['user']  = 'bwsc';

// Password [Default: ]
$config['mysql']['r']['pass']  = 'bwsc-website';

// Server [Default: localhost]
$config['mysql']['r']['serv']  = 'localhost';

// Port [Default: 3306]
$config['mysql']['r']['port']  = '3306';

/*
* Write Settings
*/
// Username[Default: root]
$config['mysql']['w']['user']  = 'bwsc';

// Password [Default: ]
$config['mysql']['w']['pass']  = 'bwsc-website';

// Server [Default: localhost]
$config['mysql']['w']['serv']  = 'localhost';

// mySQL_R_DBPort [Default: 3306]
$config['mysql']['w']['port']  = '3306';

/*
* reCAPTCHA Keys
* Used for reCAPTCHA Authentication
*/
$config['reCAPTCHA']['pub']    = '6LcbROYSAAAAAJeymhoaljIq2dTVhZonBRXPnA2l';
$config['reCAPTCHA']['priv']   = '6LcbROYSAAAAALMnAZ-MS2-rpar7LfjhjMKyssTk';?>
