<?php
use BitsTheater\Scene as MyScene;
/* @var $recite MyScene */
/* @var $v MyScene */
use com\blackmoonit\Widgets;
$recite->includeMyHeader();
$w = '';

$w .= '<h2>You don\'t have to turn on the red light<h2>'."<br />\n";
$w .= '...but you must login to use this site.'."<br /><br />\n";

print($w);
print(str_repeat('<br />',3));
$recite->includeMyFooter();
