<?php

require_once __DIR__.'/../_puff/sitewide.php';

//// GET /orgs/:org/repos
$URL = 'https://api.github.com/orgs/'.$Client['Organisation'].'/repos?visibility=public&per_page=100';
$data = github_fetch_once($URL);

foreach ( $data as $Repository ) {
	if ( !empty($Repository['name']) ) {
		$SQL = 'INSERT IGNORE `Repositories` (`Organisation`, `Repository`) VALUES (\''.$Client['Organisation'].'\', \''.$Repository['name'].'\');';
		$Result = mysqli_query($Sitewide['Database']['Connection'], $SQL);
		$json[$Repository['full_name']]['repo'] = array('result' => $Result);
		$SQL = 'INSERT IGNORE `RepositoriesStats` (`Organisation`, `Repository`) VALUES (\''.$Client['Organisation'].'\', \''.$Repository['name'].'\');';
		$Result = mysqli_query($Sitewide['Database']['Connection'], $SQL);
		$json[$Repository['full_name']]['data'] = array('result' => $Result);
	}
}
echo json_encode($json);
