<?php

////	Query the current database for a given slug
function does_repository_exist($Slug) {
	global $Sitewide;

	$Slug = htmlentities($_GET['slug'], ENT_QUOTES, 'UTF-8');
	$Slug = explode('/', $Slug);

	$SQL = <<<SQL
		SELECT
			`Organisation`,
			`Repository`
		FROM
			`Repositories`
		WHERE
			`Organisation`='$Slug[0]' AND `Repository`='$Slug[1]';
SQL;
	$Result = mysqli_fetch_once($Sitewide['Database']['Connection'], $SQL);

	return $Result;
}
