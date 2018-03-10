<?php

function get_karma_for_issue($Sitewide, $Issue) {
	global $Time;

	// Existence
	$Karma['Existence'] = 100;
	// Cash, 1 karma per cent.
	$Karma['Cash'] = $Issue['Cash Total'] * 1;
	// Comments, 1 karma per.
	$Karma['Comments'] = $Issue['Comments'] * 1;
	// TODO Reactions are comments too
	// https://developer.github.com/changes/2016-05-12-reactions-api-preview/
	// https://developer.github.com/v3/reactions/#list-reactions-for-an-issue

	////	Karma for Time Spent Open
	// 1 Karma per Day
	if ( empty($Issue['Closed At']) ) {
		$Karma['Time'] = $Time - intval($Issue['Created At']);
	} else {
		$Karma['Time'] = intval($Issue['Closed At']) - intval($Issue['Created At']);
	}
	$Karma['Time'] = intval( floor( ( $Karma['Time'] / 86400 ) ) );

	////	Karma for Priority and Size
	// Weighted from 100 to 5
	$Karma['Priority'] = 20;
	$Karma['Size']     = 20;
	foreach ( $Issue['labels'] as $Label ) {
		if ( substr($Label['name'], 0, 10) == 'Priority: ' ) {
			switch ($Label['name']) {
				case 'Priority: Critical':
					$Karma['Priority'] = 100;
				break;
				case 'Priority: High':
					$Karma['Priority'] = 50;
				break;
				case 'Priority: Medium':
					$Karma['Priority'] = 20;
				break;
				case 'Priority: Low':
					$Karma['Priority'] = 10;
				break;
				case 'Priority: Wishlist':
					$Karma['Priority'] = 5;
				break;
			}
		}
		if ( substr($Label['name'], 0, 6) == 'Size: ' ) {
			switch ($Label['name']) {
				case 'Size: Goliath':
					$Karma['Size'] = 100;
				break;
				case 'Size: Large':
					$Karma['Size'] = 50;
				break;
				case 'Size: Medium':
					$Karma['Size'] = 20;
				break;
				case 'Size: Small':
					$Karma['Size'] = 10;
				break;
				case 'Size: Bitesize':
				case 'Size: Bytesize':
					$Karma['Size'] = 5;
				break;
			}
		}
	}
	$Karma['Total'] = 0;
	$Karma['Total'] += $Karma['Existence'];
	$Karma['Total'] += $Karma['Cash'];
	$Karma['Total'] += $Karma['Comments'];
	$Karma['Total'] += $Karma['Time'];
	$Karma['Total'] += $Karma['Priority'];
	$Karma['Total'] += $Karma['Size'];

	// TODO Update Karma Cache on Issue here

	return $Karma;

}
