<?php

require_once __DIR__.'/_puff/sitewide.php';

$Page['Type']        = 'Page';
$Page['Title']       = 'Normalized Builds Compliance';
$Page['Description'] = number_format($RepositoriesAffected).' repositories out of compliance with Normalized Builds rules.';
require_once $Sitewide['Templates']['Header'];

$Meta = 'SELECT * FROM `Meta` WHERE `Name`=\'repositories-without-normalized-builds\';';
$Meta = mysqli_fetch_once($Sitewide['Database']['Connection'], $Meta);
$Data = 'SELECT * FROM `repositories-without-normalized-builds` ORDER BY `Organisation` DESC, `Repository` DESC;';
$Data = mysqli_query($Sitewide['Database']['Connection'], $Data);
?>
<script>
	$(document).ready(
		function() {
			$('.tablesorter').tablesorter({
				sortList: [
					[3,0]
				]
			});
		}
	);
</script>
<h1>Normalized Builds Compliance.</h1>
<p><?php echo $Meta['Affected'].' / '.$Meta['Total']; ?> repositories affected.</p>
<table class="tablesorter">
	<thead>
		<tr>
			<th class="text-left">Repository</th>
			<th class="text-left">Exists</th>
			<th class="text-left">Content</th>
			<th class="text-left">Status</th>
		</tr>
	</thead>
	<tbody><?php

while ( $Repository = mysqli_fetch_assoc($Data) ) {
	echo '
	<tr>
		<td><a href="https://github.com/'.$Repository['Organisation'].'/'.$Repository['Repository'].'">'.
		$Repository['Repository'].'</a></td>';

	if ( empty($Repository['VersionString']) ) {
		echo '
		<td class="color-flatui-pomegranate">Non-existant (<a href="https://github.com/'.
		$Repository['Organisation'].'/'.$Repository['Repository'].
		'">Create</a>)</td>
		<td></td>';
	} else {
		echo '
		<td><a href="https://github.com/'.$Repository['Organisation'].'/'.$Repository['Repository'].'/blob/master/.travis.yml" class="color-flatui-nephritis">Exists</td>
		<td class="color-'.$Repository['VersionColor'].'">'.$Repository['VersionString'].'</td>';
	}

	echo '
		<td data-text="'.( $Repository['state'] ? $Repository['state'] : 'z' ).'"><a href="https://travis-ci.org/'.$Repository['Organisation'].'/'.$Repository['Repository'].'"><img src="https://travis-ci.org/'.$Repository['Organisation'].'/'.$Repository['Repository'].'.svg"></a></td>
	</tr>';
}
?>

	</tbody>
</table>
<?php require_once $Sitewide['Templates']['Footer'];
