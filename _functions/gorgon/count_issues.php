<?php

////	Count all Issues
function count_issues($condition = false) {
	global $Sitewide;

	$SQL = '
		SELECT
			`Issues`.`Organisation`,
			`Issues`.`Repository`,
			`Issues`.`Number`
		FROM
			`Issues`
		LEFT JOIN
			`Repositories`
				ON
					`Issues`.`Organisation` = `Repositories`.`Organisation`
				AND
					`Issues`.`Repository` = `Repositories`.`Repository`
		';
	if ( $condition ) {
		$SQL .= ' WHERE '.$condition.';';
	} else {
		$SQL .= ';';
	}

	$Issues = mysqli_query($Sitewide['Database']['Connection'], $SQL);
	$Count = mysqli_num_rows($Issues);
	return $Count;
}
