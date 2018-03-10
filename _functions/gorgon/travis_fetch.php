<?php

function travis_fetch($Slug, $Branch = 'master') {
	global $APIQueries;
	$URL = 'https://api.travis-ci.org/repos/'.$Slug.'/branches/'.$Branch;
	$APIQueries++;
	$Headers = array(
		'User-Agent: Gorgon/0.1.0',
		'Accept: application/json',
		'Accept: application/vnd.travis-ci.2+json'
	);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Gorgon Automated Bot');
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	$data = curl_exec($ch);
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	curl_close($ch);

	$header_array = array();
	$header_data = substr($data, 0, strpos($data, "\r\n\r\n"));
	foreach(explode("\r\n", $header_data) as $i => $line) {
		if($i===0) $header_array['HTTP-Code'] = $line;
		else {
			list ($key, $value) = explode(': ', $line);
			$header_array[$key] = $value;
		}
	}

//	if ( $header_array['HTTP-Code'] == 'HTTP/1.1 202 Accepted' ) {
//		return array('error' => 'Request pending for '.$Repository['Slug'].'. Please try again in a moment.');
//	} else if ( $header_array['X-RateLimit-Remaining'] == 0 ) {
//		return array('error' => 'Unable to fetch contributions to '.$Repository['Slug'].', GitHub API Rate-Limit Reached.');
//	}

	$data = substr($data, $header_size);
	$data = json_decode($data, true);// The true in is important

	return $data;
}
