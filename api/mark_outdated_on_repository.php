<?php

require_once __DIR__.'/../_puff/sitewide.php';

if ( isset($_REQUEST['payload']) ) {
	$data = json_decode($_REQUEST['payload'], true); // The true is important
	$Slug = str_replace('https://github.com/', '', $data['repository']['url']);
}

if ( !empty($_GET['slug']) ) {
	$Slug = htmlentities($_GET['slug'], ENT_QUOTES, 'UTF-8');
}

if ( !empty($Slug) ) {

	$Exists = does_repository_exist($Slug);

	if ( $Exists ) {
		$Slug = htmlentities($_GET['slug'], ENT_QUOTES, 'UTF-8');
		$Slug = explode('/', $Slug);

		$SQL = 'UPDATE `Repositories` SET `Outdated`=\'1\' WHERE `Organisation`=\''.$Slug[0].'\' AND `Repository`=\''.$Slug[1].'\';';
		$Result = mysqli_query($Sitewide['Database']['Connection'], $SQL);

		echo json_encode(array('success' => 'The repository "'.$Slug[0].'/'.$Slug[1].'" has been marked as changed.'));

	} else {
		echo json_encode(array('error' => 'The requested repository "'.$Slug[0].'/'.$Slug[1].'" is not currently tracked.'));
	}

} else {
	echo json_encode(array('error' => 'Expected a "slug" variable but none was recieved.'));
}
