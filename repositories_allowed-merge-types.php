<?php

require_once __DIR__.'/_puff/sitewide.php';

$Page['Type']        = 'Page';
$Page['Title']       = 'Repository - Allowed Merge Types';
$Page['Description'] = 'Repositories out of compliance with Allowed Merge Type rules.';
require_once $Sitewide['Templates']['Header'];

$Meta = 'SELECT * FROM `Meta` WHERE `Name`=\'repositories_allowed-merge-types\';';
$Meta = mysqli_fetch_once($Sitewide['Database']['Connection'], $Meta);
$Data = 'SELECT * FROM `repositories_allowed-merge-types` ORDER BY `Organisation` DESC, `Repository` DESC;';
$Data = mysqli_query($Sitewide['Database']['Connection'], $Data);
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
<h1>Allowed Merge Type Compliance.</h1>
<p><?php echo $Meta['Affected'].' / '.$Meta['Total']; ?> repositories affected. <?php echo $Meta['WorkItems']; ?> work items.</p>
<table class="tablesorter">
	<thead>
		<tr>
			<th class="text-left">Repository</th>
			<th class="text-left">Rebase</th>
			<th class="text-left">Squash</th>
			<th class="text-left">Commit</th>
		</tr>
	</thead>
	<tbody><?php

	while ( $Repository = mysqli_fetch_assoc($Data) ) {

		echo '
		<tr>
			<td><a href="https://github.com/'.$Repository['Organisation'].'/'.$Repository['Repository'].'/settings">'.
		$Repository['Repository'].'</a></td>';



		if ( $Repository['allow_rebase_merge'] > 0 ) {
			echo '
			<td class="color-flatui-pomegranate" data-text="100">Allowed</td>';
		} else {
			echo '
			<td class="color-flatui-nephritis" data-text="0">Not allowed.</td>';
		}

		if ( $Repository['allow_squash_merge'] < 1 ) {
			echo '
			<td class="color-flatui-pomegranate" data-text="100">Not allowed</td>';
		} else {
			echo '
			<td class="color-flatui-nephritis" data-text="0">Allowed.</td>';
		}

		if ( $Repository['allow_merge_commit'] > 0 ) {
			echo '
			<td class="color-flatui-pomegranate" data-text="100">Allowed</td>';
		} else {
			echo '
			<td class="color-flatui-nephritis" data-text="0">Not allowed.</td>';
		}

		echo '
		</tr>';
	}

?>

	</tbody>
</table>
<?php require_once $Sitewide['Templates']['Footer'];
