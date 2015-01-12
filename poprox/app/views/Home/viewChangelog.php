<?php
use BitsTheater\Scene as MyScene;
/* @var $recite MyScene */
/* @var $v MyScene */
use \com\blackmoonit\Widgets;
$recite->includeMyHeader();
$w = '';

if (!empty($v->results)) {
	$w .= '<br>';
	$w .= '<div class="panel panel-info">';

	$w .= '<div class="panel-heading"><p class="panel-title">Changelog</p></div>';
	
	$w .= '<div class="panel-body">';
	foreach ($v->results as $theChangelogEntry) {
		$w .= '<h4>'.$theChangelogEntry['title'].'</h4><ul>';
		foreach ($theChangelogEntry['log'] as $theLogEntry) {
			$w .= '<li>'.$theLogEntry.'</li>';
		}
		$w .= '</ul><br>';
	}
	$w .= '</div>'; //panel-body
	
	$w .= '</div>'; //panel-info
}

print($w);
print(str_repeat('<br />',8));
$recite->includeMyFooter();
