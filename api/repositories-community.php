<?php
require_once __DIR__.'/../_puff/sitewide.php';

$Repositories = list_repositories();

$Communities = [];
$RepositoriesAffected = 0;
$RepositoriesTotal = 0;
$ItemsAffected = 0;

foreach ( $Repositories as $Repository ) {
	$Communities[$Repository['Repository']]['Organisation'] = $Repository['Organisation'];
	$Communities[$Repository['Repository']]['Repository'] = $Repository['Repository'];
	$Communities[$Repository['Repository']]['Affected'] = 0;

	//// GET /repos/:owner/:name/community/profile
	$Community = github_fetch_once(
		'https://api.github.com/repos/'.
		$Repository['Organisation'].'/'.$Repository['Repository'].
		'/community/profile?');
	//$Communities[$Repository['Repository']]['Community'] = $Community;

	//// License
	if ( !empty($Community['files']['license']) ) {
		$Communities[$Repository['Repository']]['License']['GitHub']['Name'] = $Community['files']['license']['name'];
		$Communities[$Repository['Repository']]['License']['GitHub']['Link'] = $Community['files']['license']['html_url'];
	} else {
		$Communities[$Repository['Repository']]['License']['GitHub']['Name'] = '';
		$Communities[$Repository['Repository']]['License']['GitHub']['Link'] = '';
		$Communities[$Repository['Repository']]['Affected']++;
	}

	//// GET /repos/:owner/:repo/contents/:path
	// WARNING: Only checks LICENSE.md
	$License = github_fetch_once(
		'https://api.github.com/repos/'.
		$Repository['Organisation'].'/'.$Repository['Repository'].
		'/contents/LICENSE.md?');
	if (
		!empty($License['type']) &&
		$License['type'] == 'file'
	) {
		$Content = base64_decode($License['content']);
		if ( strpos($Content, 'AGPL') !== false ) {
			if ( strpos($Content, '3.0') !== false ) {
				$Communities[$Repository['Repository']]['License']['Detected']['Name'] = 'AGPL 3.0';
				$Communities[$Repository['Repository']]['License']['Detected']['Color'] = 'flatui-pomegranate';
			} else {
				$Communities[$Repository['Repository']]['License']['Detected']['Name'] = 'AGPL';
				$Communities[$Repository['Repository']]['License']['Detected']['Color'] = 'flatui-pomegranate';
			}
		} else if ( strpos($Content, 'LGPL') !== false ) {
			if ( strpos($Content, '3.0') !== false ) {
				$Communities[$Repository['Repository']]['License']['Detected']['Name'] = 'LGPL 3.0';
				$Communities[$Repository['Repository']]['License']['Detected']['Color'] = 'flatui-pumpkin';
			} else if ( strpos($Content, '2.1') !== false ) {
				$Communities[$Repository['Repository']]['License']['Detected']['Name'] = 'LGPL 2.1';
				$Communities[$Repository['Repository']]['License']['Detected']['Color'] = 'flatui-pumpkin';
			} else {
				$Communities[$Repository['Repository']]['License']['Detected']['Name'] = 'LGPL';
				$Communities[$Repository['Repository']]['License']['Detected']['Color'] = 'flatui-pumpkin';
			}
		} else if ( strpos($Content, 'GPL') !== false ) {
			if ( strpos($Content, '3.0') !== false ) {
				$Communities[$Repository['Repository']]['License']['Detected']['Name'] = 'GPL 3.0';
				$Communities[$Repository['Repository']]['License']['Detected']['Color'] = 'flatui-pomegranate';
			} else if ( strpos($Content, '2.1') !== false ) {
				$Communities[$Repository['Repository']]['License']['Detected']['Name'] = 'GPL 2.1';
				$Communities[$Repository['Repository']]['License']['Detected']['Color'] = 'flatui-pomegranate';
			} else {
				$Communities[$Repository['Repository']]['License']['Detected']['Name'] = 'GPL';
				$Communities[$Repository['Repository']]['License']['Detected']['Color'] = 'flatui-pomegranate';
			}
		} else if ( strpos($Content, 'MPL License') !== false ) {
			if ( strpos($Content, '2.0') !== false ) {
				$Communities[$Repository['Repository']]['License']['Detected']['Name'] = 'MPL 2.0';
				$Communities[$Repository['Repository']]['License']['Detected']['Color'] = 'flatui-pomegranate';
			} else {
				$Communities[$Repository['Repository']]['License']['Detected']['Name'] = 'MPL';
				$Communities[$Repository['Repository']]['License']['Detected']['Color'] = 'flatui-pomegranate';
			}
		} else if ( strpos($Content, 'Apache') !== false ) {
			if ( strpos($Content, '2.0') !== false ) {
				$Communities[$Repository['Repository']]['License']['Detected']['Name'] = 'Apache 2.0';
				$Communities[$Repository['Repository']]['License']['Detected']['Color'] = 'flatui-pumpkin';
			} else {
				$Communities[$Repository['Repository']]['License']['Detected']['Name'] = 'Apache';
				$Communities[$Repository['Repository']]['License']['Detected']['Color'] = 'flatui-pumpkin';
			}
		} else if ( strpos($Content, 'Unlicense') !== false ) {
			$Communities[$Repository['Repository']]['License']['Detected']['Name'] = 'Unlicense';
			$Communities[$Repository['Repository']]['License']['Detected']['Color'] = 'flatui-pumpkin';
		} else if ( strpos($Content, 'MIT') !== false ) {
			if ( strpos($Content, 'CC BY NC SA') !== false ) {
				$Communities[$Repository['Repository']]['License']['Detected']['Name'] = 'MIT with CC BY NC SA';
				$Communities[$Repository['Repository']]['License']['Detected']['Color'] = 'flatui-nephritis';
			} else {
				$Communities[$Repository['Repository']]['License']['Detected']['Name'] = 'MIT';
				$Communities[$Repository['Repository']]['License']['Detected']['Color'] = 'flatui-nephritis';
			}
		} else {
			$Communities[$Repository['Repository']]['License']['Detected']['Name'] = 'Unknown';
			$Communities[$Repository['Repository']]['License']['Detected']['Color'] = 'flatui-pomegranate';
			$Communities[$Repository['Repository']]['Affected']++;
		}
		// WARNING: Hardcoded date limits.
		for ( $i = 1990; $i <= 2030; $i++ ) {
			if ( strpos($Content, (string)$i) !== false ) {
				$Communities[$Repository['Repository']]['License']['Detected']['Year'] = $i;
			}
		}
		if ( empty($Communities[$Repository['Repository']]['License']['Detected']['Year']) ) {
			$Communities[$Repository['Repository']]['License']['Detected']['Year'] = '';
		}
	} else {
		$Communities[$Repository['Repository']]['License']['Detected']['Name'] = '';
		$Communities[$Repository['Repository']]['License']['Detected']['Color'] = '';
		$Communities[$Repository['Repository']]['License']['Detected']['Year'] = '';
		$Communities[$Repository['Repository']]['Affected']++;
	}

	//// Code of Conduct
	if ( !empty($Community['files']['code_of_conduct']) ) {
		$Communities[$Repository['Repository']]['CoC']['GitHub']['Name'] = $Community['files']['code_of_conduct']['name'];
		$Communities[$Repository['Repository']]['CoC']['GitHub']['Link'] = $Community['files']['code_of_conduct']['html_url'];
	} else {
		$Communities[$Repository['Repository']]['CoC']['GitHub']['Name'] = '';
		$Communities[$Repository['Repository']]['CoC']['GitHub']['Link'] = '';
		$Communities[$Repository['Repository']]['Affected']++;
	}
	//// GET /repos/:owner/:repo/contents/:path
	// WARNING: Only checks .github/CODE_OF_CONDUCT.md
	$CoC = github_fetch_once(
		'https://api.github.com/repos/'.
		$Repository['Organisation'].'/'.$Repository['Repository'].
		'/contents/.github/CODE_OF_CONDUCT.md?');
	if (
		!empty($CoC['type']) &&
		$CoC['type'] == 'file'
	) {
		$Content = base64_decode($CoC['content']);
		if ( strpos($Content, 'http://contributor-covenant.org/version/1/4') !== false ) {
			$Communities[$Repository['Repository']]['CoC']['Detected']['Name'] = 'Contributor Covenant 1.4';
			$Communities[$Repository['Repository']]['CoC']['Detected']['Color'] = 'flatui-nephritis';
		} else if ( strpos($Content, 'contributor-covenant.org') !== false ) {
			$Communities[$Repository['Repository']]['CoC']['Detected']['Name'] = 'Contributor Covenant';
			$Communities[$Repository['Repository']]['CoC']['Detected']['Color'] = 'flatui-nephritis';
			$Communities[$Repository['Repository']]['Affected']++;
		} else if ( strpos($Content, 'citizencodeofconduct.org') !== false ) {
			$Communities[$Repository['Repository']]['CoC']['Detected']['Name'] = 'Citizen Code of Conduct';
			$Communities[$Repository['Repository']]['CoC']['Detected']['Color'] = 'flatui-nephritis';
			$Communities[$Repository['Repository']]['Affected']++;
		} else {
			$Communities[$Repository['Repository']]['CoC']['Detected']['Name'] = 'Unknown';
			$Communities[$Repository['Repository']]['CoC']['Detected']['Color'] = 'flatui-pumpkin';
			$Communities[$Repository['Repository']]['Affected']++;
		}
	} else {
		$Communities[$Repository['Repository']]['CoC']['Detected']['Name'] = '';
		$Communities[$Repository['Repository']]['CoC']['Detected']['Color'] = '';
		$Communities[$Repository['Repository']]['Affected']++;
	}

	if ( !empty($Community['files']['contributing']) ) {
		$Communities[$Repository['Repository']]['Contributing'] = $Community['files']['contributing']['html_url'];
	} else {
		$Communities[$Repository['Repository']]['Contributing'] = '';
		$Communities[$Repository['Repository']]['Affected']++;
	}

	if ( !empty($Community['files']['issue_template']) ) {
		$Communities[$Repository['Repository']]['IssueTemplate'] = $Community['files']['issue_template']['html_url'];
	} else {
		$Communities[$Repository['Repository']]['IssueTemplate'] = '';
		$Communities[$Repository['Repository']]['Affected']++;
	}

	if ( !empty($Community['files']['pull_request_template']) ) {
		$Communities[$Repository['Repository']]['PullTemplate'] = $Community['files']['pull_request_template']['html_url'];
	} else {
		$Communities[$Repository['Repository']]['PullTemplate'] = '';
		$Communities[$Repository['Repository']]['Affected']++;
	}

	if ( !empty($Community['files']['readme']) ) {
		$Communities[$Repository['Repository']]['ReadMe'] = $Community['files']['readme']['html_url'];
	} else {
		$Communities[$Repository['Repository']]['ReadMe'] = '';
		$Communities[$Repository['Repository']]['Affected']++;
	}

	if (
		$Communities[$Repository['Repository']]['Affected'] ||
		$Community['health_percentage'] < 100
	) {
		$RepositoriesAffected++;
		$ItemsAffected += $Communities[$Repository['Repository']]['Affected'];
	}
	$RepositoriesTotal++;

}

$ItemsTotal = $RepositoriesTotal * 8;
$Percentage = round(
	( 100 - ( ( $ItemsAffected / $ItemsTotal ) * 100 ) ),
	1
);

// Update MetaTable
$SQL = 'REPLACE INTO `Meta` (`Name`, `Updated`, `APIQueries`, `Affected`, `Total`, `Percentage`, `WorkItems`) ';
$SQL .= 'VALUES (\'repositories-community\', \''.$Time.'\', \''.$APIQueries.'\', \''.$RepositoriesAffected.'\', \''.$RepositoriesTotal.'\', \''.$Percentage.'\', \''.$ItemsAffected.'\');';
$Result = mysqli_query($Sitewide['Database']['Connection'], $SQL);

// Empty & Update Table
$SQL = <<<SQL
	TRUNCATE TABLE `repositories-community`;
	INSERT INTO `repositories-community` (
		`Organisation`, `Repository`,
		`License_GitHub_Name`, `License_GitHub_Link`,
		`License_Detected_Name`, `License_Detected_Color`, `License_Detected_Year`,
		`CoC_GitHub_Name`, `CoC_GitHub_Link`,
		`CoC_Detected_Name`, `CoC_Detected_Color`,
		`Contributing`, `IssueTemplate`, `PullTemplate`,
		`ReadMe`, `Affected`
	)
	VALUES
SQL;
foreach ( $Communities as $Row ) {
	$SQL .= '(\''.$Row['Organisation'].'\', \''.$Row['Repository'].'\', ';
	$SQL .= '\''.$Row['License']['GitHub']['Name'].'\', \''.$Row['License']['GitHub']['Link'].'\', ';
	$SQL .= '\''.$Row['License']['Detected']['Name'].'\', \''.$Row['License']['Detected']['Color'].'\', \''.$Row['License']['Detected']['Year'].'\', ';
	$SQL .= '\''.$Row['CoC']['GitHub']['Name'].'\', \''.$Row['CoC']['GitHub']['Link'].'\', ';
	$SQL .= '\''.$Row['CoC']['Detected']['Name'].'\', \''.$Row['CoC']['Detected']['Color'].'\', ';
	$SQL .= '\''.$Row['Contributing'].'\', \''.$Row['IssueTemplate'].'\', \''.$Row['PullTemplate'].'\', ';
	$SQL .= '\''.$Row['ReadMe'].'\', \''.$Row['Affected'].'\'),';
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
