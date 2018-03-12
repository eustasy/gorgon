<?php
require_once __DIR__.'/../_puff/sitewide.php';

$Repositories = list_repositories('`Repository` NOT LIKE \'copyof-%\' AND `Description` NOT LIKE \'EOL: %\'');

$CSRs = [];
$RepositoriesAffected = 0;
$RepositoriesTotal = 0;
$TotalCommitsSince = 0;

foreach ( $Repositories as $Repository ) {

	$CSRs[$Repository['Repository']]['Organisation'] = $Repository['Organisation'];
	$CSRs[$Repository['Repository']]['Repository'] = $Repository['Repository'];

	//// GET /repos/:owner/:repo/releases/latest
	$LatestRelease = github_fetch_once(
		'https://api.github.com/repos/'.
		$Repository['Organisation'].'/'.$Repository['Repository'].
		'/releases/latest?');
	$CSRs[$Repository['Repository']]['LatestRelease'] = $LatestRelease;

	if ( !empty($CSRs[$Repository['Repository']]['LatestRelease']['tag_name']) ) {
		$CSRs[$Repository['Repository']]['ReleaseTime'] = strtotime($CSRs[$Repository['Repository']]['LatestRelease']['created_at']);
		$CSRs[$Repository['Repository']]['ReleaseString'] = $CSRs[$Repository['Repository']]['LatestRelease']['tag_name'];
		if ( !empty($CSRs[$Repository['Repository']]['LatestRelease']['name']) ) {
			$CSRs[$Repository['Repository']]['ReleaseString'] .=  ' ('.$CSRs[$Repository['Repository']]['LatestRelease']['name'].')';
		}
	} else {
		$CSRs[$Repository['Repository']]['ReleaseTime'] = 0;
		$CSRs[$Repository['Repository']]['ReleaseString'] = '';
	}

	//// GET /repos/:owner/:repo/commits
	// ?since=$Release['created_at']
	$CommitsSinceQuery = 'https://api.github.com/repos/'.$Repository['Organisation'].'/'.$Repository['Repository'].'/commits?per_page=100';
	if ( !empty($CSRs[$Repository['Repository']]['LatestRelease']['tag_name']) ) {
		$CommitsSinceQuery .= '&since='.$CSRs[$Repository['Repository']]['LatestRelease']['created_at'];
	}
	$CommitsSince = github_fetch_once($CommitsSinceQuery);
	$CommitsSince = count($CommitsSince);

	// TODO What? Why? Document better.
	if ( !empty($CSRs[$Repository['Repository']]['LatestRelease']['tag_name']) ) {
		$CommitsSince = $CommitsSince - 3;
	} else {
		$CommitsSince = $CommitsSince - 2;
	}

	$CSRs[$Repository['Repository']]['CommitsSince'] = $CommitsSince;
	if ( $CommitsSince >= 90 ) {
		$CSRs[$Repository['Repository']]['CommitsColor'] = 'flatui-pomegranate';
	} else if (
		$CommitsSince > $Gorgon['CommitsSinceBoundary'] &&
		$CSRs[$Repository['Repository']]['ReleaseTime'] < ( $Time - 2419200 )
	) {
		$CSRs[$Repository['Repository']]['CommitsColor'] = 'flatui-pumpkin';
	} else {
		$CSRs[$Repository['Repository']]['CommitsColor'] = 'flatui-nephritis';
	}

	if (
		$CommitsSince > $Gorgon['CommitsSinceBoundary'] &&
		$CSRs[$Repository['Repository']]['ReleaseTime'] < ( $Time - 2419200 )
	) {
		$CSRs[$Repository['Repository']]['Affected'] = 1;
		$RepositoriesAffected++;
	} else {
		$CSRs[$Repository['Repository']]['Affected'] = 0;
	}

	unset($CSRs[$Repository['Repository']]['LatestRelease']);
	$TotalCommitsSince += $CommitsSince;
	$RepositoriesTotal++;
}

// Update MetaTable
$Percentage = round( 100 - ( ( $RepositoriesAffected / $RepositoriesTotal ) * 100 ) );
$SQL = 'REPLACE INTO `Meta` (`Name`, `Updated`, `APIQueries`, `Affected`, `Total`, `Percentage`, `Data1`) ';
$SQL .= 'VALUES (\'repositories-with-unreleased-commits\', \''.$Time.'\', \''.$APIQueries.'\', \''.$RepositoriesAffected.'\', \''.$RepositoriesTotal.'\', \''.$Percentage.'\', \''.$TotalCommitsSince.'\');';
$Result = mysqli_query($Sitewide['Database']['Connection'], $SQL);

// Empty & Update Table
$SQL = <<<SQL
	TRUNCATE TABLE `repositories-with-unreleased-commits`;
	INSERT INTO `repositories-with-unreleased-commits`
		(`Organisation`, `Repository`, `ReleaseString`, `ReleaseTime`, `CommitsSince`, `CommitsColor`, `Affected`)
	VALUES
SQL;
foreach ( $CSRs as $Row ) {
	$SQL .= '(\''.$Row['Organisation'].'\', \''.$Row['Repository'].'\', ';
	$SQL .= '\''.$Row['ReleaseString'].'\', \''.$Row['ReleaseTime'].'\', ';
	$SQL .= '\''.$Row['CommitsSince'].'\', \''.$Row['CommitsColor'].'\', ';
	$SQL .= '\''.$Row['Affected'].'\'),';
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
