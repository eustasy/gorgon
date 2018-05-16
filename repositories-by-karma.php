<?php

require_once __DIR__.'/_puff/sitewide.php';

$SQL = <<<SQL
SELECT
	`Issues`.`Organisation`,
	`Issues`.`Repository`,
	SUM(`Karma Total`) AS `Karma Total`,
	SUM(`Cash Total`) AS `Cash Total`
FROM
	`Issues`
LEFT JOIN
	`Repositories`
		ON
			`Issues`.`Organisation` = `Repositories`.`Organisation`
		AND
			`Issues`.`Repository` = `Repositories`.`Repository`
WHERE
	`Issues`.`Repository` NOT LIKE 'copyof-%'
	AND `Repositories`.`Description` NOT LIKE 'EOL: %'
	AND `State` = 'open'
GROUP BY `Issues`.`Repository`
ORDER BY
	`Karma Total` DESC;
SQL;
$Repositories = mysqli_query($Sitewide['Database']['Connection'], $SQL);
$Repositories_Count = mysqli_num_rows($Repositories);

$SQL = <<<SQL
SELECT
	SUM(`Karma Total`) AS `Karma`
FROM
	`Issues`
LEFT JOIN
	`Repositories`
		ON
			`Issues`.`Organisation` = `Repositories`.`Organisation`
		AND
			`Issues`.`Repository` = `Repositories`.`Repository`
WHERE
	`Issues`.`Repository` NOT LIKE 'copyof-%'
	AND `Repositories`.`Description` NOT LIKE 'EOL: %'
	AND `State` = 'open';
SQL;
$TotalKarma = mysqli_fetch_once($Sitewide['Database']['Connection'], $SQL);
$TotalKarma = $TotalKarma['Karma'];

$Page['Type']        = 'Page';
$Page['Title']       = 'Repositories by Karma.';
$Page['Description'] = 'Repositories sorted by Karma.';
require_once $Sitewide['Templates']['Header'];
?>
<script>
	$(document).ready(
		function() {
			$('.tablesorter').tablesorter({
				sortList: [
					[1,1]
				]
			});
		}
	);
</script>
<h1><?php echo $Page['Description']; ?></h1>
<p>There are <?php echo number_format($Repositories_Count); ?> repositories with open issues totalling <?php echo number_format($TotalKarma); ?> total karma.</p>
<table class="duplex tablesorter" style="width: auto;">
	<thead>
		<tr>
			<th class="clickable text-left">Repository
			<th class="clickable text-right">Bounty
		</tr>
	</thead>
	<tbody>

<?php
while ( $Repository = mysqli_fetch_assoc($Repositories) ) {

	$GitHub_URL = 'https://github.com/'.$Repository['Organisation'].'/'.$Repository['Repository'].'/issues';

	echo '<tr id="'.strtolower(str_replace(' ', '-', $Repository['Repository'])).'">'.PHP_EOL;

	echo '<td class="text-left">';
	echo '<a href="https://github.com/'.$Repository['Organisation'].'/'.$Repository['Repository'].'">';
	echo $Repository['Repository'].PHP_EOL;

	// Karma Bounty Indicator
	echo '<td class="text-right" data-text="'.(($Repository['Cash Total']*100000)+$Repository['Karma Total']).'">';
	if (
		!empty($Repository['Cash Total']) &&
		( $Repository['Cash Total'] > 0 )
	) {
		echo '<a href="https://bountysource.com/search?query='.urlencode($GitHub_URL).'">';
		echo '<span class="bounty-button some">$'.number_format($Repository['Cash Total']).'</span>';
		echo '</a> + ';
	}
	echo number_format($Repository['Karma Total']).' Karma'.PHP_EOL;
}
?>
	</tbody>
</table>
<?php require_once $Sitewide['Templates']['Footer'];
