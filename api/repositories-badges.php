<?php
require_once __DIR__.'/../_puff/sitewide.php';

$Repositories = list_repositories();

$Results = [];
$RepositoriesAffected = 0;
$RepositoriesTotal = 0;
$ItemsAffected = 0;

foreach ( $Repositories as $Repository ) {
	$Results[$Repository['Repository']]['Organisation'] = $Repository['Organisation'];
	$Results[$Repository['Repository']]['Repository'] = $Repository['Repository'];
	$Results[$Repository['Repository']]['Affected'] = 0;

	//// GET /repos/:owner/:repo/contents/:path
	$ReadMe = github_fetch_once(
		'https://api.github.com/repos/'.
		$Repository['Organisation'].'/'.$Repository['Repository'].
		'/contents/README.md?');
	if (
		!empty($ReadMe['type']) &&
		$ReadMe['type'] == 'file'
	) {
		$Results[$Repository['Repository']]['ReadMe'] = true;
		$Content = base64_decode($ReadMe['content']);
		$Slug = $Repository['Organisation'].'/'.$Repository['Repository'];

		$TravisCI['Old'] = '(https://travis-ci.org/'.$Slug.'.svg?branch=master)](https://travis-ci.org/'.$Slug.')';
		$TravisCI['New'] = '(https://api.travis-ci.org/'.$Slug.'.svg?branch=master)](https://travis-ci.org/'.$Slug.')';
		if (
			strpos($Content, $TravisCI['Old']) !== false ||
			strpos($Content, $TravisCI['New']) !== false
		) {
			$Results[$Repository['Repository']]['Badges']['Travis CI'] = true;
		} else {
			$Results[$Repository['Repository']]['Badges']['Travis CI'] = false;
			$Results[$Repository['Repository']]['Affected']++;
		}

		// WARNING: Doesn't check that the Codacy Badge ID is corrent.
		$Codacy['Start'] = '[![Codacy Badge](https://api.codacy.com/project/badge/Grade/';
		$Codacy['End'] = ')](https://www.codacy.com/app/'.$Slug;
		$Codacy['OldEnd'] = ')](https://www.codacy.com/app/';
		if (
			strpos($Content, $Codacy['Start']) !== false &&
			(
				strpos($Content, $Codacy['End']) !== false ||
				strpos($Content, $Codacy['OldEnd']) !== false
			)
		) {
			$Results[$Repository['Repository']]['Badges']['Codacy'] = true;
		} else {
			$Results[$Repository['Repository']]['Badges']['Codacy'] = false;
			$Results[$Repository['Repository']]['Affected']++;
		}

		// WARNING: Doesn't check that the Code Climate tracker ID is correct.
		$CodeClimate['Old'] = '[![Code Climate](https://codeclimate.com/github/'.strtolower($Slug).'/badges/gpa.svg)](https://codeclimate.com/github/'.strtolower($Slug).')';
		$CodeClimate['Start'] = '[![Maintainability](https://api.codeclimate.com/v1/';
		$CodeClimate['End'] = '/maintainability)](https://codeclimate.com/github/'.$Slug.'/maintainability)';
		$CodeClimate['EndLow'] = '/maintainability)](https://codeclimate.com/github/'.strtolower($Slug).'/maintainability)';
		if (
			strpos($Content, $CodeClimate['Old']) !== false ||
			(
				strpos($Content, $CodeClimate['Start']) !== false &&
				(
					strpos($Content, $CodeClimate['End']) !== false ||
					strpos($Content, $CodeClimate['EndLow']) !== false
				)
			)
		) {
			$Results[$Repository['Repository']]['Badges']['Code Climate'] = true;
		} else {
			$Results[$Repository['Repository']]['Badges']['Code Climate'] = false;
			$Results[$Repository['Repository']]['Affected']++;
		}

		// WARNING: Doesn't check that the BountySource tracker ID is correct, or consistent.
		$BountySource['Start'] = '[![Bountysource](https://www.bountysource.com/badge/tracker?tracker_id=';
		$BountySource['End'] = ')](https://www.bountysource.com/teams/eustasy/issues?tracker_ids=';
		if (
			strpos($Content, $BountySource['Start']) !== false &&
			strpos($Content, $BountySource['End']) !== false
		) {
			$Results[$Repository['Repository']]['Badges']['BountySource'] = true;
		} else {
			$Results[$Repository['Repository']]['Badges']['BountySource'] = false;
			$Results[$Repository['Repository']]['Affected']++;
		}

		$jsDelivr = '[![jsDelivr](https://data.jsdelivr.com/v1/package/gh/'.$Slug.'/badge?style=rounded)](https://www.jsdelivr.com/package/gh/'.$Slug.')';
		if ( strpos($Content, $jsDelivr) !== false ) {
			$Results[$Repository['Repository']]['Badges']['jsDelivr'] = true;
		} else {
			$Results[$Repository['Repository']]['Badges']['jsDelivr'] = false;
		}

	} else {
		$Results[$Repository['Repository']]['ReadMe'] = false;
		$Results[$Repository['Repository']]['Affected']++;
	}

	if ( $Results[$Repository['Repository']]['Affected'] ) {
		$RepositoriesAffected++;
	}
	$ItemsAffected += $Results[$Repository['Repository']]['Affected'];
	$RepositoriesTotal++;

}

$ItemsTotal = $RepositoriesTotal * 4;
$Percentage = round(
	( 100 - ( ( $ItemsAffected / $ItemsTotal ) * 100 ) ),
	1
);

// Update MetaTable
$SQL = 'REPLACE INTO `Meta` (`Name`, `Updated`, `APIQueries`, `Affected`, `Total`, `Percentage`, `WorkItems`) ';
$SQL .= 'VALUES (\'repositories-badges\', \''.$Time.'\', \''.$APIQueries.'\', \''.$RepositoriesAffected.'\', \''.$RepositoriesTotal.'\', \''.$Percentage.'\', \''.$ItemsAffected.'\');';
$Result = mysqli_query($Sitewide['Database']['Connection'], $SQL);

// Empty & Update Table
$SQL = <<<SQL
	TRUNCATE TABLE `repositories-badges`;
	INSERT INTO `repositories-badges` (
		`Organisation`, `Repository`,
		`TravisCI`, `Codacy`,
		`CodeClimate`, `BountySource`,
		`jsDelivr`, `ReadMe`, `Affected`
	)
	VALUES
SQL;
foreach ( $Results as $Row ) {
	$SQL .= '(\''.$Row['Organisation'].'\', \''.$Row['Repository'].'\', ';
	$SQL .= '\''.$Row['Badges']['Travis CI'].'\', \''.$Row['Badges']['Codacy'].'\', ';
	$SQL .= '\''.$Row['Badges']['Code Climate'].'\', \''.$Row['Badges']['BountySource'].'\', ';
	$SQL .= '\''.$Row['Badges']['jsDelivr'].'\', \''.$Row['ReadMe'].'\', \''.$Row['Affected'].'\'),';
}
$SQL = rtrim($SQL, ',');
$SQL .= ';';
$Result = mysqli_multi_query($Sitewide['Database']['Connection'], $SQL);

$JSON = array(
	'success' => $Result,
	'affected' => $RepositoriesAffected,
	'total' => $RepositoriesTotal,
	'iteamsAffected' => $ItemsAffected,
	'itemsTotal' => $ItemsTotal,
	'api_queries' => $APIQueries,
	'time' => $Time,
);
echo json_encode($JSON, JSON_PRETTY_PRINT);
