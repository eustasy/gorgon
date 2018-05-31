<?php
require_once __DIR__.'/../_puff/sitewide.php';

////	List default repositories to check.
$Repositories = list_repositories();

foreach ( $Repositories as $Repository ) {
	$RepositoriesTotal++;

	//// GET /repos/:owner/:repo
	$MergeTypes = github_fetch_once('https://api.github.com/repos/'.
		$Repository['Organisation'].'/'.$Repository['Repository'].'?');

	////	Populate Data
	$Data[$Repository['Repository']]['Organisation'] = $Repository['Organisation'];
	$Data[$Repository['Repository']]['Repository'] = $Repository['Repository'];
	$Data[$Repository['Repository']]['allow_rebase_merge'] = $MergeTypes['allow_rebase_merge'];
	$Data[$Repository['Repository']]['allow_squash_merge'] = $MergeTypes['allow_squash_merge'];
	$Data[$Repository['Repository']]['allow_merge_commit'] = $MergeTypes['allow_merge_commit'];
	$Data[$Repository['Repository']]['Affected'] = 0;

	////	Check allow_rebase_merge
	$ItemsTotal++;
	if ( $MergeTypes['allow_rebase_merge'] ) {
		$ItemsAffected++;
		$Data[$Repository['Repository']]['Affected']++;
	}

	////	Check allow_squash_merge
	$ItemsTotal++;
	if ( !$MergeTypes['allow_squash_merge'] ) {
		$ItemsAffected++;
		$Data[$Repository['Repository']]['Affected']++;
	}

	////	Check allow_merge_commit
	$ItemsTotal++;
	if ( $MergeTypes['allow_merge_commit'] ) {
		$ItemsAffected++;
		$Data[$Repository['Repository']]['Affected']++;
	}

	if ( $Data[$Repository['Repository']]['Affected'] ) {
		$RepositoriesAffected++;
	}
}

////	Calculate Percentage
$Percentage = round(
	( 100 - ( ( $ItemsAffected / $ItemsTotal ) * 100 ) ),
	1
);

////	Update Meta
$Meta = array(
	'Name' => 'repositories_allowed-merge-types',
	'Updated' => $Time,
	'APIQueries' => $APIQueries,
	'Affected' => $RepositoriesAffected,
	'Total' => $RepositoriesTotal,
	'Percentage' => $Percentage,
	'WorkItems' => $ItemsAffected,
);
$MetaResult = Checks_Meta_Update($Sitewide['Database']['Connection'], $Meta);

////	Update Data
$Columns = array(
	'Organisation',
	'Repository',
	'Affected',
	'allow_rebase_merge',
	'allow_squash_merge',
	'allow_merge_commit',
);
$DataResult = Checks_Data_Update($Sitewide['Database']['Connection'], 'repositories_allowed-merge-types', $Columns, $Data);

////	Return Result
if ( $MetaResult && $DataResult ) {
	$Result = true;
} else {
	$Result = false;
}
$JSON = array(
	'success' => $Result,
	'affected' => $RepositoriesAffected,
	'total' => $RepositoriesTotal,
	'api_queries' => $APIQueries,
	'time' => $Time,
);
echo json_encode($JSON, JSON_PRETTY_PRINT);
