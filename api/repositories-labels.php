<?php
require_once __DIR__.'/../_puff/sitewide.php';

$Repositories = list_repositories('`Repository` NOT LIKE \'copyof-%\' AND `Description` NOT LIKE \'EOL: %\'');

$RepositoriesAffected = 0;
$RepositoriesTotal = 0;

//// GET data/github-labels.json
$Comparison = file_get_contents(__DIR__.'/../data/github-labels.json');
$Comparison = json_decode($Comparison, true);
$ComparisonCount = count($Comparison);

foreach ( $Repositories as $Repository ) {

	$Results[$Repository['Repository']]['Organisation'] = $Repository['Organisation'];
	$Results[$Repository['Repository']]['Repository'] = $Repository['Repository'];
	$Results[$Repository['Repository']]['Affected'] = 0;

	//// GET /repos/:owner/:repo/labels
	$Labels = github_fetch_once('https://api.github.com/repos/'.
		$Repository['Organisation'].'/'.$Repository['Repository']
		.'/labels?');
	$ValidCount = 0;
	$InvalidCount = 0;
	foreach ( $Labels as $Index => $Label ) {
		if ( is_int($Index) ) {
			$Valid = false;
			foreach ( $Comparison as $Compare ) {
				if (
					$Label['name'] == $Compare['name'] &&
					$Label['color'] == trim($Compare['color'], '#')
				) {
					$Valid = true;
				}
			}
			if ( $Valid ) {
				$ValidCount++;
			} else {
				$InvalidCount++;
			}
		}
	}

	$Results[$Repository['Repository']]['Valid'] = $ValidCount;
	$Results[$Repository['Repository']]['Invalid'] = $InvalidCount;
	$Results[$Repository['Repository']]['Missing'] = $ComparisonCount - $ValidCount;

	if ( ( $ComparisonCount - $ValidCount ) > 0 ) {
		$Results[$Repository['Repository']]['Affected'] = 1;
		$RepositoriesAffected++;
	}
	$RepositoriesTotal++;
}

// Update MetaTable
$Percentage = round( 100 - ( ( $RepositoriesAffected / $RepositoriesTotal ) * 100 ) );
$SQL = 'REPLACE INTO `Meta` (`Name`, `Updated`, `APIQueries`, `Affected`, `Total`, `Percentage`) ';
$SQL .= 'VALUES (\'repositories-labels\', \''.$Time.'\', \''.$APIQueries.'\', \''.$RepositoriesAffected.'\', \''.$RepositoriesTotal.'\', \''.$Percentage.'\');';
$Result = mysqli_query($Sitewide['Database']['Connection'], $SQL);

// Empty & Update Table
$SQL = <<<SQL
	TRUNCATE TABLE `repositories-labels`;
	INSERT INTO `repositories-labels`
		(`Organisation`, `Repository`, `Valid`, `Invalid`, `Missing`, `Affected`)
	VALUES
SQL;
foreach ( $Results as $Row ) {
	$SQL .= '(\''.$Row['Organisation'].'\', \''.$Row['Repository'].'\', ';
	$SQL .= '\''.$Row['Valid'].'\', \''.$Row['Invalid'].'\', ';
	$SQL .= '\''.$Row['Missing'].'\', \''.$Row['Affected'].'\'),';
}
$SQL = rtrim($SQL, ',');
$SQL .= ';';
$Result = mysqli_multi_query($Sitewide['Database']['Connection'], $SQL);

$JSON = array(
	'success' => $Result,
	'affected' => $RepositoriesAffected,
	'total' => $RepositoriesTotal,
	'api_queries' => $APIQueries,
	'time' => $Time,
);
echo json_encode($JSON, JSON_PRETTY_PRINT);
