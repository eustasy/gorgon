<?php

require_once __DIR__.'/_puff/sitewide.php';

$Page['Type']        = 'Page';
$Page['Title']       = 'Unreleased Commits';
$Page['Description'] = number_format($RepositoriesAffected).' repositories with unreleased commits.';
require_once $Sitewide['Templates']['Header'];

$Meta = 'SELECT * FROM `Meta` WHERE `Name`=\'repositories-with-unreleased-commits\';';
$Meta = mysqli_fetch_once($Sitewide['Database']['Connection'], $Meta);
$Data = 'SELECT * FROM `repositories-with-unreleased-commits` ORDER BY `Organisation` DESC, `Repository` DESC;';
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
<h1><?php echo number_format($Meta['Data1']); ?> commits since last release.</h1>
<p><?php echo $Meta['Affected'].' / '.$Meta['Total']; ?> repositories affected.</p>
<table class="tablesorter">
	<thead>
		<tr>
			<th class="text-left">Repository</th>
			<th class="text-left">Release Version</th>
			<th class="text-left">Release Name</th>
			<th class="text-left">Release SemVer</th>
			<th class="text-left">Released</th>
			<th class="text-right">Commits Since</th>
		</tr>
	</thead>
	<tbody><?php

	while ( $Repository = mysqli_fetch_assoc($Data) ) {
		echo '
		<tr>
			<td><a href="https://github.com/'.$Repository['Organisation'].'/'.$Repository['Repository'].'/releases">'.$Repository['Repository'].'</a></td>';
		if ( empty($Repository['ReleaseVersion']) && empty($Repository['ReleaseTime']) ) {
			echo '
			<td class="color-flatui-asbestos">None</td>
			<td class="color-flatui-asbestos">None</td>
			<td class="color-flatui-asbestos">N/a</td>
			<td data-text="0" class="color-flatui-pomegranate">Never</td>';
		} else {
			echo '
			<td>'.$Repository['ReleaseVersion'].'</td>
			<td>'.$Repository['ReleaseString'].'</td>';
			if ( $Repository['ReleaseSemVer'] ) {
				echo '
			<td class="color-flatui-nephritis">'.Yes.'</td>';
			} else {
				echo '
			<td class="color-flatui-pomegranate">'.Yes.'</td>';
			}
		echo '
			<td data-text="'.
				$Repository['ReleaseTime'].
				'"';
		if ( $Repository['ReleaseTime'] > ( $Time - 2419200 ) ) {
			echo ' class="color-flatui-nephritis"';
		}
		echo '>'.date(
					'Y-m-d H:i:s',
					$Repository['ReleaseTime']
				).'</td>';
		}

		echo '
			<td class="color-'.$Repository['CommitsColor'].' text-right">'.$Repository['CommitsSince'].'</td>
		</tr>';

}

?>

	</tbody>
</table>
<?php require_once $Sitewide['Templates']['Footer'];
