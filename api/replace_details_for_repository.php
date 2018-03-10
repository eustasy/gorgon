<?php

////	Update Repositories
// Updates all repository information apart from:
// - Issues Cache
// - Cash Cache
// - Karma Cache

require_once __DIR__.'/../_puff/sitewide.php';

////	Fetch the next repo to process
if ( !empty($_GET['slug']) ) {
	$Slug = htmlentities($_GET['slug'], ENT_QUOTES, 'UTF-8');
	$Slug = explode('/', $Slug);
	// TODO Test if Slug in DB first
	$SQL = 'SELECT * FROM `Repositories` WHERE `Organisation`=\''.$Slug[0].'\' AND `Repository`=\''.$Slug[1].'\';';
} else {
	$SQL = 'SELECT * FROM `Repositories` ORDER BY `Outdated` DESC, `Updated At` ASC, `Organisation` ASC, `Repository` ASC LIMIT 1;';
}
$Repository = mysqli_fetch_once($Sitewide['Database']['Connection'], $SQL);

//// GET /repos/:owner/:repo
$URL = 'https://api.github.com/repos/'.$Repository['Organisation'].'/'.$Repository['Repository'].'?';
$data = github_fetch_once($URL);

if (
	!empty($data['message']) &&
	$data['message'] == 'Not Found'
) {
	echo json_encode(array('error' => 'Repository "'.$Repository['Organisation'].'/'.$Repository['Repository'].'" not found.'));

} else {
	$Repository = replace_details_for_repository($Repository, $data);
	echo json_encode($Repository);
}
