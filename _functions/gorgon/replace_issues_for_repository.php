<?php

function replace_issues_for_repository($Issue, $connection) {
	global $Sitewide, $Repository, $Time, $APIQueries, $Clients;

	$Issue['Organisation'] = $Repository['Organisation'];
	$Issue['Repository'] = $Repository['Repository'];

	$Issue['Number'] = intval($Issue['number']);
	$Issue['Title'] = htmlentities($Issue['title'], ENT_QUOTES, 'UTF-8');
	$Issue['Assignee'] = htmlentities($Issue['assignee']['login'], ENT_QUOTES, 'UTF-8');
	$Issue['Comments'] = intval($Issue['comments']);
	$Issue['Created At'] = strtotime($Issue['created_at']);
	$Issue['Updated At'] = strtotime($Issue['updated_at']);
	$Issue['Closed At'] = strtotime($Issue['closed_at']);
	$Issue['State'] = $Issue['state'];
	$Issue['Body'] = htmlentities($Issue['body'], ENT_QUOTES, 'UTF-8');
	$Issue['Milestone'] = htmlentities($Issue['milestone']['title'], ENT_QUOTES, 'UTF-8');

	////	GET /repos/:owner/:repo/issues/:number/reactions
	$URL = 'https://api.github.com/repos/'.$Repository['Organisation'].'/'.$Repository['Repository'].
		'/issues/'.$Issue['Number'].'/reactions?per_page=100';
	$Issue['Reactions'] = github_fetch_once($URL, false);
	$Issue['Reactions'] = count($Issue['Reactions']);

	////	Calculate Cash Bounty
	// WARNING: Must be before Karma calculations.
	$Issue['Cash Total'] = 0;
	foreach ( $Issue['labels'] as $label ) {
		if ( strtolower($label['name']) == 'bounty' ) {
			$Issue['Cash Total'] = explode('[', $Issue['Title']);
			$Issue['Cash Total'] = array_pop($Issue['Cash Total']);
			// WARNING: This expects the amount to always be in the format $1,234.56
			// $1.234,56 will result in errors, but bountysource only seems to deal in USD.
			$Issue['Cash Total'] = html_entity_decode($Issue['Cash Total'], ENT_QUOTES, 'UTF-8');
			$Issue['Cash Total'] = preg_replace("/[^0-9.]/", '', $Issue['Cash Total']);
			$Issue['Cash Total'] = intval($Issue['Cash Total']);
		}
	}
	if ( $Issue['State'] == 'open' ) {
		$Issue['Cash Open'] = $Issue['Cash Total'];
	} else {
		$Issue['Cash Open'] = 0;
	}

	////	Calculate Karma
	$Issue['Karma Total'] = get_karma_for_issue($Sitewide, $Issue)['Total'];
	if ( $Issue['State'] == 'open' ) {
		$Issue['Karma Open'] = $Issue['Karma Total'];
	} else {
		$Issue['Karma Open'] = 0;
	}

	////	Update Global Repository Variable for Caches and Counts
	$Repository['Karma Total'] = $Repository['Karma Total'] + $Issue['Karma Total'];
	$Repository['Karma Open']  = $Repository['Karma Open']  + $Issue['Karma Open'];
	$Repository['Cash Total']  = $Repository['Cash Total']  + $Issue['Cash Total'];
	$Repository['Cash Open']   = $Repository['Cash Open']   + $Issue['Cash Open'];
	$Repository['Issues Updated']++;
	$Repository['Issues Total']++;
	if ( $Issue['State'] == 'open' ) {
		$Repository['Issues Open']++;
	}

	if ( !empty($Issue['number']) ) {
		$SQL = 'REPLACE INTO `Issues` SET
		`Organisation`=\''.$Repository['Organisation'].'\',
		`Repository`=\''.$Repository['Repository'].'\',
		`Number`=\''.$Issue['Number'].'\',
		`Created At`=\''.$Issue['Created At'].'\',
		`Updated At`=\''.$Time.'\',
		`Modified At`=\''.$Issue['Updated At'].'\',
		`Closed At`=\''.$Issue['Closed At'].'\',
		`State`=\''.$Issue['State'].'\',
		`Assignee`=\''.$Issue['Assignee'].'\',
		`Karma Total`=\''.$Issue['Karma Total'].'\',
		`Karma Open`=\''.$Issue['Karma Open'].'\',
		`Cash Total`=\''.$Issue['Cash Total'].'\',
		`Cash Open`=\''.$Issue['Cash Open'].'\',
		`Title`=\''.$Issue['Title'].'\',
		`Comments`=\''.$Issue['Comments'].'\',
		`Reactions`=\''.$Issue['Reactions'].'\',
		`Description`=\''.$Issue['Body'].'\',
		`Milestone`=\''.$Issue['Milestone'].'\',
		`Labels`=\''.json_encode($Issue['labels']).'\';';
		$result = mysqli_query($connection, $SQL);
		return $result;
	} else {
		return false;
	}
}
