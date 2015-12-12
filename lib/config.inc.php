<?php
\s*/**
 * Web Application Config File
 *
 * The Web Application Configuration
 *
 * @category   WebApp.Config
 * @package    config.inc.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 * @generated  Thursday 25th September 2014, 21:58
 */
\s*// Display Errors [Default: Disabled]
$config['core']['errors']      = 1;
\s*// Debug Mode [Default: Disabled]
$config['core']['debug']       = 1;
\s*// Maintenance  [Default: Disabled]
$config['core']['maintenance'] = 0;
\s*// Database [Default: bwsc]
$config['mysql']['db']         = 'bwsc';
\s*/*
* Read Settings
*/
// Username[Default: root]
$config['mysql']['r']['user']  = 'bwsc';
\s*// Password [Default: ]
$config['mysql']['r']['pass']  = 'bwsc-website';
\s*// Server [Default: localhost]
$config['mysql']['r']['serv']  = '10.1.1.100';
\s*// Port [Default: 3306]
$config['mysql']['r']['port']  = '3306';
\s*/*
* Write Settings
*/
// Username[Default: root]
$config['mysql']['w']['user']  = 'bwsc';
\s*// Password [Default: ]
$config['mysql']['w']['pass']  = 'bwsc-website';
\s*// Server [Default: localhost]
$config['mysql']['w']['serv']  = '10.1.1.99';
\s*// mySQL_R_DBPort [Default: 3306]
$config['mysql']['w']['port']  = '3306';
\s*/*
* reCAPTCHA Keys
* Used for reCAPTCHA Authentication
*/
$config['reCAPTCHA']['pub']    = '6LcbROYSAAAAAJeymhoaljIq2dTVhZonBRXPnA2l';
$config['reCAPTCHA']['priv']   = '6LcbROYSAAAAALMnAZ-MS2-rpar7LfjhjMKyssTk';?>
