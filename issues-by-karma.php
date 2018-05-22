<?php

require_once __DIR__.'/_puff/sitewide.php';

$Repositories = list_repositories('`Repository` NOT LIKE \'copyof-%\'');

$SQL = <<<SQL
SELECT
	*
FROM
	`Issues`
LEFT JOIN
	`Repositories`
		ON
			`Issues`.`Organisation` = `Repositories`.`Organisation`
		AND
			`Issues`.`Repository` = `Repositories`.`Repository`
WHERE
	`State` = 'open'
ORDER BY
	`Karma Total` DESC;
SQL;
$Issues = mysqli_query($Sitewide['Database']['Connection'], $SQL);
$Issues_Count = mysqli_num_rows($Issues);

$SQL = <<<SQL
SELECT
	SUM(`Karma Total`) AS `Karma Total`,
	AVG(`Karma Total`) AS `Karma Average`
FROM
	`Issues`
LEFT JOIN
	`Repositories`
		ON
			`Issues`.`Organisation` = `Repositories`.`Organisation`
		AND
			`Issues`.`Repository` = `Repositories`.`Repository`
WHERE
	`State` = 'open';
SQL;
$TotalKarma = mysqli_fetch_once($Sitewide['Database']['Connection'], $SQL);
$AverageKarma = $TotalKarma['Karma Average'];
$TotalKarma = $TotalKarma['Karma Total'];

$Page['Type']        = 'Page';
$Page['Title']       = 'Issues by Karma.';
$Page['Description'] = 'All open issues sorted by Karma.';
require_once $Sitewide['Templates']['Header'];
?>
<script>
	$(document).ready(
		function() {
			$('.tablesorter').tablesorter({
				sortList: [
					[9,1]
				]
			});
		}
	);
</script>
<h1><?php echo $Page['Description']; ?></h1>
<p>There are <?php echo number_format($Issues_Count); ?> open issues with <?php echo number_format($TotalKarma); ?> total karma. The average is <?php echo number_format($AverageKarma); ?> karma per issue.</p>
<table class="duplex tablesorter">
	<thead>
		<tr>
			<th class="clickable text-left">Repository
			<th class="clickable text-right">Number
			<th class="clickable text-left">Title
			<th class="clickable text-left">Type
			<th class="clickable text-left">Language
			<th class="clickable text-left">Status
			<th class="clickable text-left">Priority
			<th class="clickable text-left">Size
			<th class="clickable text-right">Comments
			<th class="clickable text-right">Bounty
			<th class="clickable text-right">Opened
			<th class="clickable text-right">Modified
		</tr>
	</thead>
	<tbody>

<?php
while ( $Issue = mysqli_fetch_assoc($Issues) ) {

	$GitHub_URL = 'https://github.com/'.$Issue['Organisation'].'/'.$Issue['Repository'].'/issues/'.$Issue['Number'];

	echo '<tr id="'.strtolower(str_replace(' ', '-', $Issue['Repository'])).'">'.PHP_EOL;

	echo '<td class="text-left">';
	echo '<a href="https://github.com/'.$Issue['Organisation'].'/'.$Issue['Repository'].'">';
	echo $Issue['Repository'].PHP_EOL;

	echo '<td class="text-right" data-text="'.$Issue['Number'].'">';
	echo '<a href="'.$GitHub_URL.'">';
	echo '#'.number_format($Issue['Number']);
	echo '</a>'.PHP_EOL;

	echo '<td class="text-left half">'.substr($Issue['Title'], 0, 96);
	if ( strlen($Issue['Title']) > 96 ) {
		echo '&hellip;';
	}
	echo PHP_EOL;

	// Priority and Size
	$Issue['Labels'] = json_decode($Issue['Labels'], true);
	$Priority['color'] = 'eee';
	$Priority['name'] = 'Priority: Untriaged';
	$Priority['priority'] = 1;
	$Size['color'] = 'eee';
	$Size['name'] = '';
	$Size['priority'] = 1;
	foreach ( $Issue['Labels'] as $Label ) {
		if ( substr($Label['name'], 0, 10) == 'Priority: ' ) {
			$Priority = $Label;
			switch ($Label['name']) {
				case 'Priority: Critical':
					$Priority['priority'] = 100;
				break;
				case 'Priority: High':
					$Priority['priority'] = 50;
				break;
				case 'Priority: Medium':
					$Priority['priority'] = 20;
				break;
				case 'Priority: Low':
					$Priority['priority'] = 10;
				break;
				case 'Priority: Wishlist':
					$Priority['priority'] = 5;
				break;
			}
		}
		if ( substr($Label['name'], 0, 6) == 'Size: ' ) {
			$Size = $Label;
			switch ($Label['name']) {
				case 'Size: Goliath':
					$Size['priority'] = 100;
				break;
				case 'Size: Large':
					$Size['priority'] = 50;
				break;
				case 'Size: Medium':
					$Size['priority'] = 20;
				break;
				case 'Size: Small':
					$Size['priority'] = 10;
				break;
				case 'Size: Bitesize':
				case 'Size: Bytesize':
					$Size['priority'] = 5;
				break;
			}
		}
		if ( substr($Label['name'], 0, 10) == 'Language: ' ) {
			$Language = $Label;
		}
		if ( substr($Label['name'], 0, 8) == 'Status: ' ) {
			$Status = $Label;
		}
		if ( substr($Label['name'], 0, 6) == 'Type: ' ) {
			$Type = $Label;
		}
	}
	echo '
		<td style="background-color: #'.$Type['color'].'">
		'.substr($Type['name'], 6).'</td>
		<td style="background-color: #'.$Language['color'].'">
		'.substr($Language['name'], 10).'</td>
		<td style="background-color: #'.$Status['color'].'">
		'.substr($Status['name'], 8).'</td>
		<td data-text="'.$Priority['priority'].'" style="background-color: #'.$Priority['color'].'">
		'.substr($Priority['name'], 10).'</td>
		<td data-text="'.$Size['priority'].'" style="background-color: #'.$Size['color'].'">
		'.substr($Size['name'], 6).'</td>';

	echo '<td class="text-right" data-text="'.$Issue['Comments'].'">';
	echo number_format($Issue['Comments']).PHP_EOL;

	// Karma Bounty Indicator
	echo '<td class="text-right" data-text="'.(($Issue['Cash Total']*100000)+$Issue['Karma Total']).'">';
	if (
		!empty($Issue['Cash Total']) &&
		( $Issue['Cash Total'] > 0 )
	) {
		echo '<a href="https://bountysource.com/search?query='.urlencode($GitHub_URL).'">';
		echo '<span class="bounty-button some">$'.number_format($Issue['Cash Total']).'</span>';
		echo '</a> + ';
	}
	echo number_format($Issue['Karma Total']).' Karma'.PHP_EOL;

	echo '<td class="text-right" data-text="'.$Issue['Created At'].'">'.strtolower(Time_Readable_Difference($Issue['Created At'])['Preferred']).PHP_EOL;
	echo '<td class="text-right" data-text="'.$Issue['Modified At'].'">'.strtolower(Time_Readable_Difference($Issue['Modified At'])['Preferred']).PHP_EOL;
	echo '</tr>'.PHP_EOL.PHP_EOL;
}
?>
	</tbody>
</table>
<?php require_once $Sitewide['Templates']['Footer'];
