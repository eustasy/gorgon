<?php
require_once __DIR__.'/../_puff/sitewide.php';

////	List default repositories to check.
$Repositories = list_repositories();

foreach ( $Repositories as $Repository ) {
	$RepositoriesTotal++;

	//// GET /repos/:owner/:repo/contents/:path
	$ReadMe = github_fetch_once(
		'https://api.github.com/repos/'.
		$Repository['Organisation'].'/'.$Repository['Repository'].
		'/contents/README.md?');

	////	Populate Data
	$Slug = $Repository['Organisation'].'/'.$Repository['Repository'];
	$Data[$Repository['Repository']]['Organisation'] = $Repository['Organisation'];
	$Data[$Repository['Repository']]['Repository'] = $Repository['Repository'];
	$Data[$Repository['Repository']]['Affected'] = 0;

	////	Check ReadMe Existence
	$ItemsTotal++;
	if (
		!empty($ReadMe['type']) &&
		$ReadMe['type'] == 'file'
	) {
		$Content = base64_decode($ReadMe['content']);
		$Data[$Repository['Repository']]['ReadMe'] = true;
	} else {
		$Content = '';
		$Data[$Repository['Repository']]['ReadMe'] = false;
		$Data[$Repository['Repository']]['Affected']++;
		$ItemsAffected++;
	}

	////	Check Travis CI Badge
	$ItemsTotal++;
	$TravisCI['Old'] = '(https://travis-ci.org/'.$Slug.'.svg?branch=master)](https://travis-ci.org/'.$Slug.')';
	$TravisCI['New'] = '(https://api.travis-ci.org/'.$Slug.'.svg?branch=master)](https://travis-ci.org/'.$Slug.')';
	if (
		strpos($Content, $TravisCI['Old']) !== false ||
		strpos($Content, $TravisCI['New']) !== false
	) {
		$Data[$Repository['Repository']]['TravisCI'] = true;
	} else {
		$Data[$Repository['Repository']]['TravisCI'] = false;
		$Data[$Repository['Repository']]['Affected']++;
		$ItemsAffected++;
	}

	////	Check Codacy Badge
	// WARNING: Doesn't check that the Codacy Badge ID is corrent.
	$ItemsTotal++;
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
		$Data[$Repository['Repository']]['Codacy'] = true;
	} else {
		$Data[$Repository['Repository']]['Codacy'] = false;
		$Data[$Repository['Repository']]['Affected']++;
		$ItemsAffected++;
	}

	////	Check Code Climate Badge
	// WARNING: Doesn't check that the Code Climate tracker ID is correct.
	$ItemsTotal++;
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
		$Data[$Repository['Repository']]['CodeClimate'] = true;
	} else {
		$Data[$Repository['Repository']]['CodeClimate'] = false;
		$Data[$Repository['Repository']]['Affected']++;
		$ItemsAffected++;
	}

	////	Check BountySource Badge
	// WARNING: Doesn't check that the BountySource tracker ID is correct, or consistent.
	$ItemsTotal++;
	$BountySource['Start'] = '[![Bountysource](https://www.bountysource.com/badge/tracker?tracker_id=';
	$BountySource['End'] = ')](https://www.bountysource.com/teams/eustasy/issues?tracker_ids=';
	if (
		strpos($Content, $BountySource['Start']) !== false &&
		strpos($Content, $BountySource['End']) !== false
	) {
		$Data[$Repository['Repository']]['BountySource'] = true;
	} else {
		$Data[$Repository['Repository']]['BountySource'] = false;
		$Data[$Repository['Repository']]['Affected']++;
		$ItemsAffected++;
	}

	////	Check jsDelivr Badge [Optional]
	$jsDelivr = '[![jsDelivr](https://data.jsdelivr.com/v1/package/gh/'.$Slug.'/badge?style=rounded)](https://www.jsdelivr.com/package/gh/'.$Slug.')';
	if ( strpos($Content, $jsDelivr) !== false ) {
		$Data[$Repository['Repository']]['jsDelivr'] = true;
	} else {
		$Data[$Repository['Repository']]['jsDelivr'] = false;
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
	'Name' => 'repositories_badges',
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
	'TravisCI',
	'Codacy',
	'CodeClimate',
	'BountySource',
	'jsDelivr',
	'ReadMe',
);
$DataResult = Checks_Data_Update($Sitewide['Database']['Connection'], 'repositories_badges', $Columns, $Data);

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
