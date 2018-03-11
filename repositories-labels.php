<?php

require_once __DIR__.'/_puff/sitewide.php';

$Page['Type']        = 'Page';
$Page['Title']       = 'Labels Compliance';
$Page['Description'] = number_format($RepositoriesAffected).' repositories out of compliance with GitHub Labels rules.';
require_once $Sitewide['Templates']['Header'];

$Meta = 'SELECT * FROM `Meta` WHERE `Name`=\'repositories-labels\';';
$Meta = mysqli_fetch_once($Sitewide['Database']['Connection'], $Meta);
$Data = 'SELECT * FROM `repositories-labels` ORDER BY `Organisation` DESC, `Repository` DESC;';
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
<h1>Repositories with incompliant labels.</h1>
<p><?php echo $Meta['Affected'].' / '.$Meta['Total']; ?> repositories affected.</p>
<p>Use <a href="https://github.com/piwik/github-sync">Piwiks GitHub Sync</a> to copy off of a compliant repo to fix.</p>
<code>./github-sync.php sync --token=%TOKEN% --skip-milestones eustasy/puff-core eustasy/puff-db</code><br>
<code>./github-sync.php sync --token=%TOKEN% --skip-milestones eustasy/puff-core "eustasy/*"</code>
<table class="duplex tablesorter">
	<thead>
		<tr>
			<th class="clickable text-left">Repository
			<th class="clickable text-right">Valid
			<th class="clickable text-right">Invalid
			<th class="clickable text-right">Missing
			<th class="clickable text-right">Compliant
		</tr>
	</thead>
	<tbody><?php

	foreach ( $Data as $Row ) {
			echo '
			<tr>
				<td><a href="https://github.com/'.$Row['Organisation'].'/'.$Row['Repository'].'/labels">'.$Row['Repository'].'</a></td>';

			if ( $Row['Valid'] > 0 ) {
				echo '
				<td class="color-flatui-nephritis text-right">'.$Row['Valid'].'</td>';
			} else {
				echo '
				<td class="color-flatui-pomegranate text-right">'.$Row['Valid'].'</td>';
			}

			if ( $Row['Invalid'] > 0 ) {
				echo '
				<td class="color-flatui-pomegranate text-right">'.$Row['Invalid'].'</td>';
			} else {
				echo '
				<td class="color-flatui-nephritis text-right">'.$Row['Invalid'].'</td>';
			}

			if ( $Row['Missing'] > 0 ) {
				echo '
				<td class="color-flatui-pomegranate text-right">'.$Row['Missing'].'</td>';
			} else {
				echo '
				<td class="color-flatui-nephritis text-right">'.$Row['Missing'].'</td>';
			}

			if ( $Row['Missing'] > 0 ) {
				echo '
				<td class="color-flatui-pomegranate text-right">Non-compliant</td>';
			} else if ( $Row['Invalid'] > 0 ) {
				echo '
				<td class="color-flatui-orange text-right">Partially Compliant</td>';
			} else {
				echo '
				<td class="color-flatui-nephritis text-right">Compliant</td>';
			}

			echo '
			</tr>';
		}
	?>
	</tbody>
</table>
<?php require_once $Sitewide['Templates']['Footer'];
