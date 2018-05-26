<?php

function Checks_Data_Update($Connection, $CheckName, $Columns, $Data) {
	$SQL = 'TRUNCATE TABLE `'.$CheckName.'`;';
	$SQL .= 'INSERT INTO `'.$CheckName.'` (';
	foreach ( $Columns AS $Column ) {
		$SQL .= '`'.$Column.'`,';
	}
	$SQL = rtrim($SQL, ',');
	$SQL .= ') VALUES ';
	foreach ( $Data as $Row ) {
		$SQL .= '(';
		foreach ( $Columns AS $Column ) {
			$SQL .= '\''.$Row[$Column].'\',';
		}
		$SQL = rtrim($SQL, ',');
		$SQL .= '),';
	}
	$SQL = rtrim($SQL, ',');
	$SQL .= ';';
	//var_dump($SQL);
	$Result = mysqli_multi_query($Connection, $SQL);
	return $Result;
}
