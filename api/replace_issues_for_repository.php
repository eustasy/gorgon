<?php

////	Issues Repositories
// Updates all issue information
// Updates all RepositoryData

require_once __DIR__.'/../_puff/sitewide.php';

////	Fetch the next repo to process
if ( !empty($_GET['slug']) ) {
	$Slug = htmlentities($_GET['slug'], ENT_QUOTES, 'UTF-8');
	$Slug = explode('/', $Slug);
	// TODO Test if Slug in DB first
	$SQL = 'SELECT * FROM `RepositoriesStats` WHERE `Organisation`=\''.$Slug[0].'\' AND `Repository`=\''.$Slug[1].'\';';
} else {
	$SQL = 'SELECT * FROM `RepositoriesStats` ORDER BY `Outdated` DESC, `Updated At` ASC, `Organisation` ASC, `Repository` ASC LIMIT 1;';
}
$Repository = mysqli_fetch_once($Sitewide['Database']['Connection'], $SQL);

//// GET /repos/:owner/:repo/issues
$URL = 'https://api.github.com/repos/'.$Repository['Organisation'].'/'.$Repository['Repository'].'/issues';
$Parameters = '?per_page=100&state=all&sort=updated';
$data = github_fetch_once($URL.$Parameters);

if (
	!empty($data['message']) &&
	$data['message'] == 'Not Found'
) {
	echo json_encode(array('error' => 'Repository "'.$Repository['Organisation'].'/'.$Repository['Repository'].'" not found.'));
} else {

	$Repository['Karma Total'] = 0;
	$Repository['Karma Open'] = 0;
	$Repository['Cash Total'] = 0;
	$Repository['Cash Open'] = 0;

	$Repository['Issues Total'] = 0;
	$Repository['Issues Updated'] = 0;
	$Repository['Issues Open'] = 0;
	// OPTION: Alternative count, but this includes pull requests.
	//$Repository['Issues Open'] = $data['open_issues'];

	// OPTION: Re-enable if issues requests are taking to long or we are running out of requests.
	// WARNING: Will break issue counts if enabled.
	//if ( !$Repository['Changed'] ) {
	//	$Parameters .= '&since='.date('c', $Repository['Updated At']);
	//}

	$data = github_fetch_once($URL.$Parameters);
	$Repository['X-RateLimit-Remaining'] = $data['X-RateLimit-Remaining'];

	foreach ( $data as $reference => $Issue ) {
		if (
			$reference !== 'X-RateLimit-Remaining' &&
			$reference !== 'API Pagination End' &&
			empty($Issue['pull_request'])
		) {
			replace_issues_for_repository($Issue, $Sitewide['Database']['Connection']);
		}
	}
	// Now that we know how many pages there are, start paginating.
	// Start on Page 2
	$Page = 2;
	while ( $Page <= $data['API Pagination End'] ) {
		$data = github_fetch_once($URL.$Parameters.'&page='.$Page);
		$Repository['X-RateLimit-Remaining'] = $data['X-RateLimit-Remaining'];
		$Page = $Page + 1;
		foreach ( $data as $reference => $Issue ) {
			if (
				$reference !== 'X-RateLimit-Remaining' &&
				$reference !== 'API Pagination End' &&
				empty($Issue['pull_request'])
			) {
				replace_issues_for_repository($Issue, $Sitewide['Database']['Connection']);
			}
		}
	}

	$Repository['Updated At'] = $Time;
	$Repository['Outdated'] = 0;
	$SQL = '
	REPLACE INTO
		`RepositoriesStats`
	SET
		`Organisation`=\''.$Repository['Organisation'].'\',
		`Repository`=\''.$Repository['Repository'].'\',
		`Outdated`=\''.$Repository['Outdated'].'\',
		`Updated At`=\''.$Repository['Updated At'].'\',
		`Karma Total`=\''.$Repository['Karma Total'].'\',
		`Karma Open`=\''.$Repository['Karma Open'].'\',
		`Cash Total`=\''.$Repository['Cash Total'].'\',
		`Cash Open`=\''.$Repository['Cash Open'].'\',
		`Issues Total`=\''.$Repository['Issues Total'].'\',
		`Issues Open`=\''.$Repository['Issues Open'].'\';';
	$Result = mysqli_query($Sitewide['Database']['Connection'], $SQL);
	echo json_encode($Repository);
}
