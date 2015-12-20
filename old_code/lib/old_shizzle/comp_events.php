<?php
/*
*************************************************************************
=====                          EVENTS.PHP                           =====
=====  PHP File containing an array that dictates the event details.=====
*************************************************************************
*/

// Variables
$stroke['fs'] = 'Freestyle';
$stroke['bs'] = 'Backstroke';
$stroke['br'] = 'Breaststroke';
$stroke['bf'] = 'Butterfly';
$stroke['im'] = 'Individual Medley';
$stroke['fr'] = 'Freestyle Relay';
$stroke['mr'] = 'Medley Relay';
$age[8] = '8/Over';
$age[9] = '9/Over';
$age[10] = '10/Over';
$age[11] = '11/Over';
$gender[0] = MALE;
$gender[1] = FEMALE;
$gender[2] = "Mixed";
$i = 1;

// MALE
// Event 1
$eventid[$i]['g'] = 0;
$eventid[$i]['d'] = 50;
$eventid[$i]['s'] = 'fs';
$eventid[$i]['a'] = $age[MINAGE];
$i++;

// Event 2
$eventid[$i]['g'] = 0;
$eventid[$i]['d'] = 100;
$eventid[$i]['s'] = 'fs';
$eventid[$i]['a'] = $age[MINAGEH];
$i++;

// Event 3
$eventid[$i]['g'] = 0;
$eventid[$i]['d'] = 200;
$eventid[$i]['s'] = 'fs';
$eventid[$i]['a'] = $age[MINAGE];
$i++;

// Event 4
$eventid[$i]['g'] = 0;
$eventid[$i]['d'] = 400;
$eventid[$i]['s'] = 'fs';
$eventid[$i]['a'] = $age[MINAGELD];
$i++;

// Event 5
$eventid[$i]['g'] = 0;
$eventid[$i]['d'] = 800;
$eventid[$i]['s'] = 'fs';
$eventid[$i]['a'] = $age[MINAGELD];
$i++;

// Event 6
$eventid[$i]['g'] = 0;
$eventid[$i]['d'] = 1500;
$eventid[$i]['s'] = 'fs';
$eventid[$i]['a'] = $age[MINAGELD];
$i++;

// Event 7
$eventid[$i]['g'] = 0;
$eventid[$i]['d'] = 50;
$eventid[$i]['s'] = 'bs';
$eventid[$i]['a'] = $age[MINAGE];
$i++;

// Event 8
$eventid[$i]['g'] = 0;
$eventid[$i]['d'] = 100;
$eventid[$i]['s'] = 'bs';
$eventid[$i]['a'] = $age[MINAGEH];
$i++;

// Event 9
$eventid[$i]['g'] = 0;
$eventid[$i]['d'] = 200;
$eventid[$i]['s'] = 'bs';
$eventid[$i]['a'] = $age[MINAGE];
$i++;

// Event 10
$eventid[$i]['g'] = 0;
$eventid[$i]['d'] = 50;
$eventid[$i]['s'] = 'br';
$eventid[$i]['a'] = $age[MINAGE];
$i++;

// Event 11
$eventid[$i]['g'] = 0;
$eventid[$i]['d'] = 100;
$eventid[$i]['s'] = 'br';
$eventid[$i]['a'] = $age[MINAGEH];
$i++;

// Event 12
$eventid[$i]['g'] = 0;
$eventid[$i]['d'] = 200;
$eventid[$i]['s'] = 'br';
$eventid[$i]['a'] = $age[MINAGE];
$i++;

// Event 13
$eventid[$i]['g'] = 0;
$eventid[$i]['d'] = 50;
$eventid[$i]['s'] = 'bf';
$eventid[$i]['a'] = $age[MINAGE];
$i++;

// Event 14
$eventid[$i]['g'] = 0;
$eventid[$i]['d'] = 100;
$eventid[$i]['s'] = 'bf';
$eventid[$i]['a'] = $age[MINAGEH];
$i++;

// Event 15
$eventid[$i]['g'] = 0;
$eventid[$i]['d'] = 200;
$eventid[$i]['s'] = 'bf';
$eventid[$i]['a'] = $age[MINAGE];
$i++;

// Event 16
$eventid[$i]['g'] = 0;
$eventid[$i]['d'] = 100;
$eventid[$i]['s'] = 'im';
$eventid[$i]['a'] = $age[MINAGE];
$i++;

// Event 17
$eventid[$i]['g'] = 0;
$eventid[$i]['d'] = 200;
$eventid[$i]['s'] = 'im';
$eventid[$i]['a'] = $age[MINAGE];
$i++;

// Event 18
$eventid[$i]['g'] = 0;
$eventid[$i]['d'] = 400;
$eventid[$i]['s'] = 'im';
$eventid[$i]['a'] = $age[MINAGELD];
$i++;

// Event 19
$eventid[$i]['g'] = 0;
$eventid[$i]['d'] = "4x50";
$eventid[$i]['s'] = 'fr';
$eventid[$i]['a'] = "";
$i++;

// Event 20
$eventid[$i]['g'] = 0;
$eventid[$i]['d'] = "4x100";
$eventid[$i]['s'] = 'fr';
$eventid[$i]['a'] = "";
$i++;

// Event 21
$eventid[$i]['g'] = 0;
$eventid[$i]['d'] = "4x200";
$eventid[$i]['s'] = 'fr';
$eventid[$i]['a'] = "";
$i++;

// Event 22
$eventid[$i]['g'] = 0;
$eventid[$i]['d'] = "4x50";
$eventid[$i]['s'] = 'mr';
$eventid[$i]['a'] = "";
$i++;

// Event 23
$eventid[$i]['g'] = 0;
$eventid[$i]['d'] = "4x100";
$eventid[$i]['s'] = 'mr';
$eventid[$i]['a'] = "";
$i++;

// FEMALE
// Event 24
$eventid[$i]['g'] = 1;
$eventid[$i]['d'] = 50;
$eventid[$i]['s'] = 'fs';
$eventid[$i]['a'] = $age[MINAGE];
$i++;

// Event 25
$eventid[$i]['g'] = 1;
$eventid[$i]['d'] = 100;
$eventid[$i]['s'] = 'fs';
$eventid[$i]['a'] = $age[MINAGEH];
$i++;

// Event 26
$eventid[$i]['g'] = 1;
$eventid[$i]['d'] = 200;
$eventid[$i]['s'] = 'fs';
$eventid[$i]['a'] = $age[MINAGE];
$i++;

// Event 27
$eventid[$i]['g'] = 1;
$eventid[$i]['d'] = 400;
$eventid[$i]['s'] = 'fs';
$eventid[$i]['a'] = $age[MINAGELD];
$i++;

// Event 28
$eventid[$i]['g'] = 1;
$eventid[$i]['d'] = 800;
$eventid[$i]['s'] = 'fs';
$eventid[$i]['a'] = $age[MINAGELD];
$i++;

// Event 29
$eventid[$i]['g'] = 1;
$eventid[$i]['d'] = 1500;
$eventid[$i]['s'] = 'fs';
$eventid[$i]['a'] = $age[MINAGELD];
$i++;

// Event 30
$eventid[$i]['g'] = 1;
$eventid[$i]['d'] = 50;
$eventid[$i]['s'] = 'bs';
$eventid[$i]['a'] = $age[MINAGE];
$i++;

// Event 31
$eventid[$i]['g'] = 1;
$eventid[$i]['d'] = 100;
$eventid[$i]['s'] = 'bs';
$eventid[$i]['a'] = $age[MINAGEH];
$i++;

// Event 32
$eventid[$i]['g'] = 1;
$eventid[$i]['d'] = 200;
$eventid[$i]['s'] = 'bs';
$eventid[$i]['a'] = $age[MINAGE];
$i++;

// Event 33
$eventid[$i]['g'] = 1;
$eventid[$i]['d'] = 50;
$eventid[$i]['s'] = 'br';
$eventid[$i]['a'] = $age[MINAGE];
$i++;

// Event 34
$eventid[$i]['g'] = 1;
$eventid[$i]['d'] = 100;
$eventid[$i]['s'] = 'br';
$eventid[$i]['a'] = $age[MINAGEH];
$i++;

// Event 35
$eventid[$i]['g'] = 1;
$eventid[$i]['d'] = 200;
$eventid[$i]['s'] = 'br';
$eventid[$i]['a'] = $age[MINAGE];
$i++;

// Event 36
$eventid[$i]['g'] = 1;
$eventid[$i]['d'] = 50;
$eventid[$i]['s'] = 'bf';
$eventid[$i]['a'] = $age[MINAGE];
$i++;

// Event 37
$eventid[$i]['g'] = 1;
$eventid[$i]['d'] = 100;
$eventid[$i]['s'] = 'bf';
$eventid[$i]['a'] = $age[MINAGEH];
$i++;

// Event 38
$eventid[$i]['g'] = 1;
$eventid[$i]['d'] = 200;
$eventid[$i]['s'] = 'bf';
$eventid[$i]['a'] = $age[MINAGE];
$i++;

// Event 39
$eventid[$i]['g'] = 1;
$eventid[$i]['d'] = 100;
$eventid[$i]['s'] = 'im';
$eventid[$i]['a'] = $age[MINAGEH];
$i++;

// Event 40
$eventid[$i]['g'] = 1;
$eventid[$i]['d'] = 200;
$eventid[$i]['s'] = 'im';
$eventid[$i]['a'] = $age[MINAGE];
$i++;

// Event 41
$eventid[$i]['g'] = 1;
$eventid[$i]['d'] = 400;
$eventid[$i]['s'] = 'im';
$eventid[$i]['a'] = $age[MINAGELD];
$i++;

// Event 42
$eventid[$i]['g'] = 1;
$eventid[$i]['d'] = "4x50";
$eventid[$i]['s'] = 'fr';
$eventid[$i]['a'] = "";
$i++;

// Event 43
$eventid[$i]['g'] = 1;
$eventid[$i]['d'] = "4x100";
$eventid[$i]['s'] = 'fr';
$eventid[$i]['a'] = "";
$i++;

// Event 44
$eventid[$i]['g'] = 1;
$eventid[$i]['d'] = "4x200";
$eventid[$i]['s'] = 'fr';
$eventid[$i]['a'] = "";
$i++;

// Event 45
$eventid[$i]['g'] = 1;
$eventid[$i]['d'] = "4x50";
$eventid[$i]['s'] = 'mr';
$eventid[$i]['a'] = "";
$i++;

// Event 46
$eventid[$i]['g'] = 1;
$eventid[$i]['d'] = "4x100";
$eventid[$i]['s'] = 'mr';
$eventid[$i]['a'] = "";
$i++;

// MIXED
// Event 1
$eventid[$i]['g'] = 2;
$eventid[$i]['d'] = 50;
$eventid[$i]['s'] = 'fs';
$eventid[$i]['a'] = $age[MINAGE];
$i++;

// Event 2
$eventid[$i]['g'] = 2;
$eventid[$i]['d'] = 100;
$eventid[$i]['s'] = 'fs';
$eventid[$i]['a'] = $age[MINAGEH];
$i++;

// Event 3
$eventid[$i]['g'] = 2;
$eventid[$i]['d'] = 200;
$eventid[$i]['s'] = 'fs';
$eventid[$i]['a'] = $age[MINAGE];
$i++;

// Event 4
$eventid[$i]['g'] = 2;
$eventid[$i]['d'] = 400;
$eventid[$i]['s'] = 'fs';
$eventid[$i]['a'] = $age[MINAGELD];
$i++;

// Event 5
$eventid[$i]['g'] = 2;
$eventid[$i]['d'] = 800;
$eventid[$i]['s'] = 'fs';
$eventid[$i]['a'] = $age[MINAGELD];
$i++;

// Event 6
$eventid[$i]['g'] = 2;
$eventid[$i]['d'] = 1500;
$eventid[$i]['s'] = 'fs';
$eventid[$i]['a'] = $age[MINAGELD];
$i++;

// Event 7
$eventid[$i]['g'] = 2;
$eventid[$i]['d'] = 50;
$eventid[$i]['s'] = 'bs';
$eventid[$i]['a'] = $age[MINAGE];
$i++;

// Event 8
$eventid[$i]['g'] = 2;
$eventid[$i]['d'] = 100;
$eventid[$i]['s'] = 'bs';
$eventid[$i]['a'] = $age[MINAGEH];
$i++;

// Event 9
$eventid[$i]['g'] = 2;
$eventid[$i]['d'] = 200;
$eventid[$i]['s'] = 'bs';
$eventid[$i]['a'] = $age[MINAGE];
$i++;

// Event 10
$eventid[$i]['g'] = 2;
$eventid[$i]['d'] = 50;
$eventid[$i]['s'] = 'br';
$eventid[$i]['a'] = $age[MINAGE];
$i++;

// Event 11
$eventid[$i]['g'] = 2;
$eventid[$i]['d'] = 100;
$eventid[$i]['s'] = 'br';
$eventid[$i]['a'] = $age[MINAGEH];
$i++;

// Event 12
$eventid[$i]['g'] = 2;
$eventid[$i]['d'] = 200;
$eventid[$i]['s'] = 'br';
$eventid[$i]['a'] = $age[MINAGE];
$i++;

// Event 13
$eventid[$i]['g'] = 2;
$eventid[$i]['d'] = 50;
$eventid[$i]['s'] = 'bf';
$eventid[$i]['a'] = $age[MINAGE];
$i++;

// Event 14
$eventid[$i]['g'] = 2;
$eventid[$i]['d'] = 100;
$eventid[$i]['s'] = 'bf';
$eventid[$i]['a'] = $age[MINAGEH];
$i++;

// Event 15
$eventid[$i]['g'] = 2;
$eventid[$i]['d'] = 200;
$eventid[$i]['s'] = 'bf';
$eventid[$i]['a'] = $age[MINAGE];
$i++;

// Event 16
$eventid[$i]['g'] = 2;
$eventid[$i]['d'] = 100;
$eventid[$i]['s'] = 'im';
$eventid[$i]['a'] = $age[MINAGE];
$i++;

// Event 17
$eventid[$i]['g'] = 2;
$eventid[$i]['d'] = 200;
$eventid[$i]['s'] = 'im';
$eventid[$i]['a'] = $age[MINAGE];
$i++;

// Event 18
$eventid[$i]['g'] = 2;
$eventid[$i]['d'] = 400;
$eventid[$i]['s'] = 'im';
$eventid[$i]['a'] = $age[MINAGELD];
$i++;

// Event 19
$eventid[$i]['g'] = 2;
$eventid[$i]['d'] = "4x50";
$eventid[$i]['s'] = 'fr';
$eventid[$i]['a'] = "";
$i++;

// Event 20
$eventid[$i]['g'] = 2;
$eventid[$i]['d'] = "4x100";
$eventid[$i]['s'] = 'fr';
$eventid[$i]['a'] = "";
$i++;

// Event 21
$eventid[$i]['g'] = 2;
$eventid[$i]['d'] = "4x200";
$eventid[$i]['s'] = 'fr';
$eventid[$i]['a'] = "";
$i++;

// Event 22
$eventid[$i]['g'] = 2;
$eventid[$i]['d'] = "4x50";
$eventid[$i]['s'] = 'mr';
$eventid[$i]['a'] = "";
$i++;

// Event 23
$eventid[$i]['g'] = 2;
$eventid[$i]['d'] = "4x100";
$eventid[$i]['s'] = 'mr';
$eventid[$i]['a'] = "";
$i++;
?>