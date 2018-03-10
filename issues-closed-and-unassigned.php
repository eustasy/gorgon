<?php
require_once __DIR__.'/_puff/sitewide.php';
// TODO Meta on scrobble

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
	`Issues`.`Repository` NOT LIKE 'copyof-%'
	AND `Repositories`.`Description` NOT LIKE 'EOL: %'
	AND `State` = 'closed'
	AND `Assignee` = ''
ORDER BY
	`Karma Total` DESC;
SQL;
$Issues = mysqli_query($Sitewide['Database']['Connection'], $SQL);
$Issues_Count = mysqli_num_rows($Issues);

$Page['Type']        = 'Page';
$Page['Title']       = 'Issues closed and unassigned.';
$Page['Description'] = number_format($Issues_Count).' issues closed and unassigned.';
require_once $Sitewide['Templates']['Header'];
?>
<script>
	$(document).ready(
		function() {
			$('.tablesorter').tablesorter({
				sortList: [
					[4,1]
				]
			});
		}
	);
</script>
<h1><?php echo $Page['Description']; ?></h1>
<table class="duplex tablesorter">
	<thead>
		<tr>
			<th class="clickable text-left faded">Title
			<th class="clickable text-left">Repository
			<th class="clickable text-right">Number
			<th class="clickable text-right">Comments
			<th class="clickable text-right">Bounty
			<th class="clickable text-right">Opened
			<th class="clickable text-right">Modified
		</tr>
	</thead>
	<tbody>

<?php
while ( $Issue = mysqli_fetch_assoc($Issues) ) {
	// ["Assignee"]=> string(0) ""
	// ["Description"]=> string(226) "Are there already plans for packaging this as an RPM for easy installation? If so, great! How can I help? If not, I'd like to volunteer to build them. Either way, I'd like to help out in any way that I can."
	//["Milestone"]=> string(0) ""
	//["Bounty"]=> string(1) "0" }
	echo '<tr id="'.strtolower(str_replace(' ', '-', $Issue['Repository'])).'">'.PHP_EOL;
	echo '<td class="text-left half">'.substr($Issue['Title'], 0, 96);
	if ( strlen($Issue['Title']) > 96 ) {
		echo '&hellip;';
	}
	echo PHP_EOL;
	echo '<td class="text-left">'.$Issue['Repository'].PHP_EOL;
	echo '<td class="text-right" data-text="'.$Issue['Number'].'"><a href="/issues/'.$Issue['Organisation'].'/'.$Issue['Repository'].'/'.$Issue['Number'].'">#'.number_format($Issue['Number']).'</a>'.PHP_EOL;
	echo '<td class="text-right" data-text="'.$Issue['Comments'].'"><a href="https://github.com/'.$Issue['Organisation'].'/'.$Issue['Repository'].'/issues/'.$Issue['Number'].'">'.number_format($Issue['Comments']).'</a>'.PHP_EOL;

	// Karma Bounty Indicator
	echo '<td class="text-right" data-text="'.(($Issue['Cash Total']*100000)+$Issue['Karma Total']).'">';
	if ( !empty($Issue['Cash Total']) ) echo '<span class="bounty-button some">$'.number_format($Issue['Cash Total']).'</span>'.' + ';
	echo number_format($Issue['Karma Total']).' Karma'.PHP_EOL;

	echo '<td class="text-right" data-text="'.$Issue['Created At'].'">'.strtolower(Time_Readable_Difference($Issue['Created At'])['Preferred']).PHP_EOL;
	echo '<td class="text-right" data-text="'.$Issue['Modified At'].'">'.strtolower(Time_Readable_Difference($Issue['Modified At'])['Preferred']).PHP_EOL;
	echo '</tr>'.PHP_EOL.PHP_EOL;
}
?>
	</tbody>
</table>
<?php require_once $Sitewide['Templates']['Footer'];
