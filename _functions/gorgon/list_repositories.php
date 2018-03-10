<?php

////	List all repositories
function list_repositories($condition = false) {
	global $Sitewide;

	$SQL = '
		SELECT
			`Organisation`,
			`Repository`
		FROM
			`Repositories`';
	if ( $condition ) {
		$SQL .= ' WHERE '.$condition.';';
	} else {
		$SQL .= ';';
	}
	$Repositories = mysqli_query($Sitewide['Database']['Connection'], $SQL);
	while ( $Repository = mysqli_fetch_assoc($Repositories) ) {
		$Result[$Repository['Organisation'].'/'.$Repository['Repository']] = $Repository;
	}

	return $Result;
}
