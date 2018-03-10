<?php
require_once __DIR__.'/_puff/sitewide.php';
$Page['Type']        = 'Page';
$Page['Title']       = 'Repositories Details';
$Page['Description'] = 'Repositories Details, such as Size, Popularity, Homepage, and Description.';
require_once $Sitewide['Templates']['Header'];
?>
<script>
	$(document).ready(
		function() {
			$('.tablesorter').tablesorter({
				sortList: [
					[0,0]
				]
			});
		}
	);
</script>
<h1>Repositories.</h1>
<table class="tablesorter">
	<thead>
		<tr>
			<th class="text-left">Repository</th>
			<th class="text-left">Updated At</th>
			<th class="text-left">Modified At</th>
			<th class="text-right">Size</th>
			<th class="text-right">Popularity</th>
			<th class="text-left">Homepage</th>
			<th class="text-left">Description</th>
		</tr>
	</thead>
	<tbody><?php

$SQL = '
	SELECT
		*
	FROM
		`Repositories`;';
$Result = mysqli_query($Sitewide['Database']['Connection'], $SQL);
while ( $Repository = mysqli_fetch_assoc($Result) ) {
	$Repositories[$Repository['Organisation'].'/'.$Repository['Repository']] = $Repository;
}

foreach ( $Repositories as $Repository ) {
	echo '
	<tr>
		<td>
			<a href="https://github.com/'.$Repository['Organisation'].'/'.$Repository['Repository'].'">
				'.$Repository['Repository'].'
			</a>
		</td>
		<td class="text-right" data-text="'.$Repository['Updated At'].'">'.strtolower(Time_Readable_Difference($Repository['Updated At'])['Preferred']).'
		<td class="text-right" data-text="'.$Repository['Modified At'].'">'.strtolower(Time_Readable_Difference($Repository['Modified At'])['Preferred']).'
		<td class="text-right">'.number_format($Repository['Size']).'</td>
		<td class="text-right">'.number_format($Repository['Popularity']).'</td>
		<td><a href="'.$Repository['Homepage'].'" class="';
		if ( substr($Repository['Homepage'], 0, 8) == 'https://' ) {
			echo 'color-flatui-nephritis';
		} else if ( substr($Repository['Homepage'], 0, 7) == 'http://' ) {
			echo 'color-flatui-pumpkin';
		}
		echo '">'.$Repository['Homepage'].'</td>
		<td>'.$Repository['Description'].'</td>
	</tr>';
}

?>
	</tbody>
</table>
<?php require_once $Sitewide['Templates']['Footer'];
