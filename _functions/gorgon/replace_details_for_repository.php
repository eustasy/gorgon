<?php

////	Query GitHub for the repo
// Merges a DB $Repository with given GitHub API $data
function replace_details_for_repository($Repository, $data) {
	global $Sitewide, $Time;

	$Repository['Description'] = htmlentities($data['description'], ENT_QUOTES, 'UTF-8');
	$Repository['Homepage'] = htmlentities($data['homepage'], ENT_QUOTES, 'UTF-8');
	if ( $data['updated_at'] > $data['pushed_at'] ) {
		$Repository['Modified At'] = strtotime($data['updated_at']);
	} else {
		$Repository['Modified At'] = strtotime($data['pushed_at']);
	}
	$Repository['Popularity'] = $data['stargazers_count'] + $data['forks_count'];
	$Repository['Size']     = intval($data['size']);
	$Repository['Fork']     = intval($data['fork']);
	$Repository['Archived'] = intval($data['archived']);

	$Repository['Updated At'] = $Time;
	$Repository['Outdated'] = 0;

	$SQL = '
	REPLACE INTO
		`Repositories`
	SET
		`Organisation`=\''.$Repository['Organisation'].'\',
		`Repository`=\''.$Repository['Repository'].'\',
		`Fork`=\''.$Repository['Fork'].'\',
		`Archived`=\''.$Repository['Archived'].'\',
		`Outdated`=\''.$Repository['Outdated'].'\',
		`Updated At`=\''.$Repository['Updated At'].'\',
		`Modified At`=\''.$Repository['Modified At'].'\',
		`Size`=\''.$Repository['Size'].'\',
		`Popularity`=\''.$Repository['Popularity'].'\',
		`Homepage`=\''.$Repository['Homepage'].'\',
		`Description`=\''.$Repository['Description'].'\';';
	$Result = mysqli_query($Sitewide['Database']['Connection'], $SQL);

	return $Repository;
}
