<?php
require_once __DIR__.'/../_puff/sitewide.php';

$Repositories = list_repositories();

$Results = [];
$RepositoriesAffected = 0;
$RepositoriesTotal = 0;
$ItemsAffected = 0;

foreach ( $Repositories as $Repository ) {

	//// GET /repos/:owner/:repo
	$MergeTypes = github_fetch_once('https://api.github.com/repos/'.
		$Repository['Organisation'].'/'.$Repository['Repository'].'?');

	$Results[$Repository['Repository']]['Organisation'] = $Repository['Organisation'];
	$Results[$Repository['Repository']]['Repository'] = $Repository['Repository'];
	$Results[$Repository['Repository']]['allow_rebase_merge'] = $MergeTypes['allow_rebase_merge'];
	$Results[$Repository['Repository']]['allow_squash_merge'] = $MergeTypes['allow_squash_merge'];
	$Results[$Repository['Repository']]['allow_merge_commit'] = $MergeTypes['allow_merge_commit'];
	$Results[$Repository['Repository']]['Affected'] = 0;

	if ( $MergeTypes['allow_rebase_merge'] ) {
		$Results[$Repository['Repository']]['Affected']++;
	}
	if ( !$MergeTypes['allow_squash_merge'] ) {
		$Results[$Repository['Repository']]['Affected']++;
	}
	if ( $MergeTypes['allow_merge_commit'] ) {
		$Results[$Repository['Repository']]['Affected']++;
	}

	if ( $Results[$Repository['Repository']]['Affected'] ) {
		$ItemsAffected += $Results[$Repository['Repository']]['Affected'];
		$RepositoriesAffected++;
	}
	$RepositoriesTotal++;
}

$ItemsTotal = $RepositoriesTotal * 3;
$Percentage = round(
	( 100 - ( ( $ItemsAffected / $ItemsTotal ) * 100 ) ),
	1
);

// Update MetaTable
$SQL = 'REPLACE INTO `Meta` (`Name`, `Updated`, `APIQueries`, `Affected`, `Total`, `Percentage`, `WorkItems`) ';
$SQL .= 'VALUES (\'repositories_allowed-merge-types\', \''.$Time.'\', \''.$APIQueries.'\', \''.$RepositoriesAffected.'\', \''.$RepositoriesTotal.'\', \''.$Percentage.'\', \''.$ItemsAffected.'\');';
$Result = mysqli_query($Sitewide['Database']['Connection'], $SQL);

// Empty & Update Table
$SQL = <<<SQL
	TRUNCATE TABLE `repositories_allowed-merge-types`;
	INSERT INTO `repositories_allowed-merge-types`
		(
			`Organisation`, `Repository`,
			`allow_rebase_merge`, `allow_squash_merge`,
			`allow_merge_commit`, `Affected`
		)
	VALUES
SQL;
foreach ( $Results as $Row ) {
	$SQL .= '(\''.$Row['Organisation'].'\', \''.$Row['Repository'].'\', ';
	$SQL .= '\''.$Row['allow_rebase_merge'].'\', \''.$Row['allow_squash_merge'].'\', ';
	$SQL .= '\''.$Row['allow_merge_commit'].'\', \''.$Row['Affected'].'\'),';
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
