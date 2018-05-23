<?php
require_once __DIR__.'/../_puff/sitewide.php';

$Repositories = list_repositories();

$Travis = [];
$RepositoriesAffected = 0;
$RepositoriesTotal = 0;
$ItemsAffected = 0;

foreach ( $Repositories as $Repository ) {

	//// GET /repos/:owner/:repo/branches/:branch
	$Travis[$Repository['Repository']] = travis_fetch($Repository['Organisation'].'/'.$Repository['Repository']);

	$Travis[$Repository['Repository']]['Organisation'] = $Repository['Organisation'];
	$Travis[$Repository['Repository']]['Repository'] = $Repository['Repository'];
	$Travis[$Repository['Repository']]['state'] = $Travis[$Repository['Repository']]['branch']['state'];

	foreach ( $Travis[$Repository['Repository']]['branch']['config']['before_script'] as $Line ) {
		if ( strpos($Line, 'b92da74ddf4b05b698e2d12ebd56e965d6749397') !== false ) {
			$Travis[$Repository['Repository']]['VersionString'] = 'Normal Checks 1.10.1';
			$Travis[$Repository['Repository']]['VersionColor'] = 'flatui-nephritis';
			$Travis[$Repository['Repository']]['Affected'] = 0;
		} else if ( strpos($Line, '2b23ee3dbb274409ae51a620ae9d6fef6516781a') !== false ) {
			$Travis[$Repository['Repository']]['VersionString'] = 'Normal Checks 1.10.0';
			$Travis[$Repository['Repository']]['VersionColor'] = 'flatui-nephritis';
			$Travis[$Repository['Repository']]['Affected'] = 0;
		} else if ( strpos($Line, '649a7e0907c0ab4b342688e7d068b574a0945b3e') !== false ) {
			$Travis[$Repository['Repository']]['VersionString'] = 'Normal Checks 1.9';
			$Travis[$Repository['Repository']]['VersionColor'] = 'flatui-nephritis';
			$Travis[$Repository['Repository']]['Affected'] = 0;
		} else if ( strpos($Line, '4256f55ef631900df06ca5c6167e21e6ed4cf55b') !== false ) {
			$Travis[$Repository['Repository']]['VersionString'] = 'Normal Checks 1.7';
			$Travis[$Repository['Repository']]['VersionColor'] = 'flatui-nephritis';
			$Travis[$Repository['Repository']]['Affected'] = 0;
		} else if ( strpos($Line, 'd5f1a5d9e3fbac391b905f2bdfcdcdbfe465eabf') !== false ) {
			$Travis[$Repository['Repository']]['VersionString'] = 'Normal Checks 1.4';
			$Travis[$Repository['Repository']]['VersionColor'] = 'flatui-pumpkin';
			$Travis[$Repository['Repository']]['Affected'] = 1;
		} else if ( empty($Travis[$Repository['Repository']]['VersionString']) ) {
			$Travis[$Repository['Repository']]['VersionString'] = 'Unknown';
			$Travis[$Repository['Repository']]['VersionColor'] = 'flatui-pumpkin';
			$Travis[$Repository['Repository']]['Affected'] = 1;
		}
	}

	if (
		$Travis[$Repository['Repository']]['state'] != 'passed' ||
		$Travis[$Repository['Repository']]['Affected']
	) {
		$Travis[$Repository['Repository']]['Affected'] = 1;
		$RepositoriesAffected++;
		if ( $Travis[$Repository['Repository']]['state'] != 'passed' ) {
			$ItemsAffected += 1;
		}
		if ( $Travis[$Repository['Repository']]['Affected'] ) {
			$ItemsAffected += 1;
		}
	}
	$RepositoriesTotal++;
}

$ItemsTotal = $RepositoriesTotal * 2;
$Percentage = round(
	( 100 - ( ( $ItemsAffected / $ItemsTotal ) * 100 ) ),
	1
);

// Update MetaTable
$SQL = 'REPLACE INTO `Meta` (`Name`, `Updated`, `APIQueries`, `Affected`, `Total`, `Percentage`, `WorkItems`) ';
$SQL .= 'VALUES (\'repositories_without-normalized-builds\', \''.$Time.'\', \''.$APIQueries.'\', \''.$RepositoriesAffected.'\', \''.$RepositoriesTotal.'\', \''.$Percentage.'\', \''.$ItemsAffected.'\');';
$Result = mysqli_query($Sitewide['Database']['Connection'], $SQL);

// Empty & Update Table
$SQL = <<<SQL
	TRUNCATE TABLE `repositories_without-normalized-builds`;
	INSERT INTO `repositories_without-normalized-builds`
		(`Organisation`, `Repository`, `VersionString`, `VersionColor`, `state`, `Affected`)
	VALUES
SQL;
foreach ( $Travis as $Row ) {
	$SQL .= '(\''.$Row['Organisation'].'\', \''.$Row['Repository'].'\', ';
	$SQL .= '\''.$Row['VersionString'].'\', \''.$Row['VersionColor'].'\', ';
	$SQL .= '\''.$Row['state'].'\', \''.$Row['Affected'].'\'),';
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
