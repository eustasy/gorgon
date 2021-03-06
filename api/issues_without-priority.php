<?php
require_once __DIR__.'/../_puff/sitewide.php';

$Repositories = list_repositories();
$RepositoriesTotal = count($Repositories);
Unset($Repositories);

$SQL = <<<SQL
SELECT
	*
FROM
	`Issues`
LEFT JOIN
	`Repositories`
		ON
			`Issues`.`Organisation` = `Repositories`.`Organisation`
		AND
			`Issues`.`Repository` = `Repositories`.`Repository`
WHERE
	`Issues`.`Repository` NOT LIKE 'copyof-%'
	AND `Repositories`.`Description` NOT LIKE 'EOL: %'
	AND `Labels` NOT LIKE '%Priority%'
	AND `Labels` NOT LIKE '%Status: Invalid%'
ORDER BY
	`State` DESC,
	`Karma Total` DESC;
SQL;
$Issues = mysqli_query($Sitewide['Database']['Connection'], $SQL);
$IssuesAffected = mysqli_num_rows($Issues);

foreach ($Issues as $Issue) {
	$Repositories[$Issue['Organisation']][$Issue['Repository']]++;
}
$RepositoriesAffected = count($Repositories, COUNT_RECURSIVE) - count($Repositories);

$Issues = count_issues('`Issues`.`Repository` NOT LIKE \'copyof-%\' AND `Repositories`.`Description` NOT LIKE \'EOL: %\' AND `Labels` NOT LIKE \'%Status: Invalid%\'');

// Update MetaTable
$Percentage = round( 100 - ( ( $IssuesAffected / $Issues ) * 100 ) );
$SQL = 'REPLACE INTO `Meta` (`Name`, `Updated`, `APIQueries`, `Affected`, `Total`, `Percentage`, `WorkItems`) ';
$SQL .= 'VALUES (\'issues_without-priority\', \''.$Time.'\', \''.$APIQueries.'\', \''.$RepositoriesAffected.'\', \''.$RepositoriesTotal.'\', \''.$Percentage.'\', \''.$IssuesAffected.'\');';
$Result = mysqli_query($Sitewide['Database']['Connection'], $SQL);

$JSON = array(
	'success' => $Result,
	'repositories' => array(
		'affected' => $RepositoriesAffected,
		'total' => $RepositoriesTotal,
	),
	'issues' => array(
		'affected' => $IssuesAffected,
		'total' => $Issues,
	),
	'api_queries' => $APIQueries,
	'time' => $Time,
);
echo json_encode($JSON, JSON_PRETTY_PRINT);
