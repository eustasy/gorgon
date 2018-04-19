<?php

require_once __DIR__.'/_puff/sitewide.php';

$Page['Type']        = 'Page';
$Page['Title']       = 'Repository Badges';
$Page['Description'] = 'Repositories out of compliance with Badge rules.';
require_once $Sitewide['Templates']['Header'];

$Meta = 'SELECT * FROM `Meta` WHERE `Name`=\'repositories-badges\';';
$Meta = mysqli_fetch_once($Sitewide['Database']['Connection'], $Meta);
$Data = 'SELECT * FROM `repositories-badges` ORDER BY `Organisation` DESC, `Repository` DESC;';
$Data = mysqli_query($Sitewide['Database']['Connection'], $Data);
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
<h1>Badge Compliance.</h1>
<p><?php echo $Meta['Affected'].' / '.$Meta['Total']; ?> repositories affected. <?php echo $Meta['WorkItems']; ?> work items. jsDelivr is not a failure condition.</p>
<table class="tablesorter">
	<thead>
		<tr>
			<th class="text-left">Repository</th>
			<th class="text-left">Work Items</th>
			<th class="text-left">Travis CI</th>
			<th class="text-left">Codacy</th>
			<th class="text-left">Code Climate</th>
			<th class="text-left">BountySource</th>
			<th class="text-left">jsDelivr</th>
			<th class="text-left">ReadMe</th>
		</tr>
	</thead>
	<tbody><?php

	while ( $Repository = mysqli_fetch_assoc($Data) ) {

		echo '
		<tr>
			<td><a href="https://github.com/'.$Repository['Organisation'].'/'.$Repository['Repository'].'">'.
		$Repository['Repository'].'</a></td>';

		if (
			$Repository['Affected'] > 0 &&
			$Repository['ReadMe'] == false
		) {
			echo '
			<td class="color-flatui-pomegranate" data-text="100">No ReadMe, no badges.</a></td>';
		} else if ( $Repository['Affected'] > 0 ) {
			echo '
			<td class="color-flatui-orange" data-text="'.$Repository['Affected'].'">'.$Repository['Affected'].' work items.</a></td>';
		} else {
			echo '
			<td class="color-flatui-nephritis" data-text="0">No work items.</td>';
		}

		if ( $Repository['TravisCI'] > 0 ) {
			echo '
			<td class="color-flatui-nephritis" data-text="0">Compliant</td>';
		} else if ( $Repository['ReadMe'] == false ) {
			echo '
			<td class="color-flatui-orange" data-text="1">Non-compliant</a></td>';
		} else {
			echo '
			<td class="color-flatui-pomegranate" data-text="2">Non-compliant</a></td>';
		}

		if ( $Repository['Codacy'] > 0 ) {
			echo '
			<td class="color-flatui-nephritis" data-text="0">Compliant</td>';
		} else if ( $Repository['ReadMe'] == false ) {
			echo '
			<td class="color-flatui-orange" data-text="1">Non-compliant</a></td>';
		} else {
			echo '
			<td class="color-flatui-pomegranate" data-text="2">Non-compliant</a></td>';
		}

		if ( $Repository['CodeClimate'] > 0 ) {
			echo '
			<td class="color-flatui-nephritis" data-text="0">Compliant</td>';
		} else if ( $Repository['ReadMe'] == false ) {
			echo '
			<td class="color-flatui-orange" data-text="1">Non-compliant</a></td>';
		} else {
			echo '
			<td class="color-flatui-pomegranate" data-text="2">Non-compliant</a></td>';
		}

		if ( $Repository['BountySource'] > 0 ) {
			echo '
			<td class="color-flatui-nephritis" data-text="0">Compliant</td>';
		} else if ( $Repository['ReadMe'] == false ) {
			echo '
			<td class="color-flatui-orange" data-text="1">Non-compliant</a></td>';
		} else {
			echo '
			<td class="color-flatui-pomegranate" data-text="2">Non-compliant</a></td>';
		}

		if ( $Repository['jsDelivr'] > 0 ) {
			echo '
			<td class="color-flatui-nephritis" data-text="0">Present</td>';
		} else if ( $Repository['ReadMe'] == false ) {
			echo '
			<td class="color-flatui-orange" data-text="1">Not present</a></td>';
		} else {
			echo '
			<td class="color-flatui-orange" data-text="2">Not present</a></td>';
		}

		if ( $Repository['ReadMe'] == false ) {
			echo '
			<td class="color-flatui-pomegranate" data-text="100">Non-existent</a></td>';
		} else {
			echo '
			<td class="color-flatui-nephritis" data-text="0">Exists</a></td>';
		}

		echo '
		</tr>';
	}

?>

	</tbody>
</table>
<?php require_once $Sitewide['Templates']['Footer'];
