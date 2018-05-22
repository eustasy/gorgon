<?php

function get_karma_for_issue($Sitewide, $Issue) {
	global $Time;

	// Existence
	$Karma['Existence'] = 100;
	// Cash, 1 karma per cent.
	$Karma['Cash'] = $Issue['Cash Total'] * 1;
	// Comments, 1 karma per.
	$Karma['Comments'] = $Issue['Comments'] * 1;
	$Karma['Reactions'] = $Issue['Reactions'] * 1;

	////	Karma for Time Spent Open
	// 1 Karma per Day
	if ( empty($Issue['Closed At']) ) {
		$Karma['Time'] = $Time - intval($Issue['Created At']);
	} else {
		$Karma['Time'] = intval($Issue['Closed At']) - intval($Issue['Created At']);
	}
	$Karma['Time'] = intval( floor( ( $Karma['Time'] / 86400 ) ) );

	////	Karma for Priority and Size
	// Weighted from 1000 to 1
	$Karma['Priority'] = 100;
	$Karma['Size']     = 100;
	foreach ( $Issue['labels'] as $Label ) {
		if ( substr($Label['name'], 0, 10) == 'Priority: ' ) {
			switch ($Label['name']) {
				case 'Priority: Critical':
					$Karma['Priority'] = 1000;
				break;
				case 'Priority: High':
					$Karma['Priority'] = 500;
				break;
				case 'Priority: Medium':
					$Karma['Priority'] = 200;
				break;
				case 'Priority: Low':
					$Karma['Priority'] = 100;
				break;
				case 'Priority: Wishlist':
					$Karma['Priority'] = 50;
				break;
			}
		}
		if ( substr($Label['name'], 0, 6) == 'Size: ' ) {
			switch ($Label['name']) {
				case 'Size: Goliath':
					$Karma['Size'] = 1000;
				break;
				case 'Size: Large':
					$Karma['Size'] = 500;
				break;
				case 'Size: Medium':
					$Karma['Size'] = 200;
				break;
				case 'Size: Small':
					$Karma['Size'] = 100;
				break;
				case 'Size: Bitesize':
				case 'Size: Bytesize':
					$Karma['Size'] = 50;
				break;
			}
		}
	}
	$Karma['Total'] = 0;
	$Karma['Total'] += $Karma['Existence'];
	$Karma['Total'] += $Karma['Cash'];
	$Karma['Total'] += $Karma['Comments'];
	$Karma['Total'] += $Karma['Reactions'];
	$Karma['Total'] += $Karma['Time'];
	$Karma['Total'] += $Karma['Priority'];
	$Karma['Total'] += $Karma['Size'];

	return $Karma;

}
