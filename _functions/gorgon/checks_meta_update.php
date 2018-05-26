<?php

function Checks_Meta_Update($Connection, $Meta) {
	$SQL = 'REPLACE INTO `Meta` (';
	foreach ( $Meta AS $Key => $Value ) {
		$SQL .= '`'.$Key.'`,';
	}
	$SQL = rtrim($SQL, ',');
	$SQL .= ') VALUES (';
	foreach ( $Meta AS $Key => $Value ) {
		$SQL .= '\''.$Value.'\',';
	}
	$SQL = rtrim($SQL, ',');
	$SQL .= ');';
	//var_dump($SQL);
	$Result = mysqli_query($Connection, $SQL);
	return $Result;
}
