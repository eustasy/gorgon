<?php

////	Query GitHub for the repo
function github_fetch_once($URL, $RatesAndPages = true) {
	global $APIQueries, $Client;
	$APIQueries++;
	//$URL = $URL.'&client_id='.$Client['ID'].'&client_secret='.$Client['Secret'];

	$Headers = array(
		'Accept: application/json',
		'Accept: application/vnd.github.black-panther-preview+json', // Community profile metrics
		'Accept: application/vnd.github.inertia-preview+json',       // Projects
		'Accept: application/vnd.github.luke-cage-preview+json',     // Require multiple approving reviews
		'Accept: application/vnd.github.mercy-preview+json',         // Topics
		'Accept: application/vnd.github.scarlet-witch-preview+json', // Codes of conduct
		'Accept: application/vnd.github.squirrel-girl-preview+json', // Reactions
		'Accept: application/vnd.github.polaris-preview+json',       // Squash merge support
		'Accept: application/vnd.github.zzzax-preview+json',         // Require signed commits
		'Authorization: token '.$Client['Token'],
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
	//var_dump(curl_getinfo($ch));
	curl_close($ch);
	//var_dump($data);
	//exit;

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

	if ( $RatesAndPages ) {
		$data['X-RateLimit-Remaining'] = $header_array['X-RateLimit-Remaining'];
		if ( !empty($header_array['Link']) ) {
			$header_array['Link'] = explode(',', $header_array['Link']);
			foreach ( $header_array['Link'] as $Link ) {
				$Link = explode(';', $Link);
				$Link[0] = trim($Link[0], ' <>#');
				$Link[1] = trim($Link[1], ' <>#');
				if ( $Link[1] == 'rel="last"' ) {
					$Index = explode('?', $Link[0])[1];
					$Index = explode('&', $Index);
					foreach ( $Index as $Potential ) {
						if ( substr($Potential, 0, 4) == 'page' ) {
							$data['API Pagination End'] = intval(explode('=', $Potential)[1]);
						}
					}
				}
			}
		}
		if ( empty($data['API Pagination End']) ) {
			$data['API Pagination End'] = false;
		}
	}

	return $data;
}
