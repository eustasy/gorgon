<?php

require_once __DIR__.'/_puff/sitewide.php';

$Page['Type']        = 'Page';
$Page['Title']       = 'Community Compliance';
$Page['Description'] = 'Repositories out of compliance with Community rules.';
require_once $Sitewide['Templates']['Header'];

$Meta = 'SELECT * FROM `Meta` WHERE `Name`=\'repositories-community\';';
$Meta = mysqli_fetch_once($Sitewide['Database']['Connection'], $Meta);
$Data = 'SELECT * FROM `repositories-community` ORDER BY `Organisation` DESC, `Repository` DESC;';
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
<h1>Community Compliance.</h1>
<p><?php echo $Meta['Affected'].' / '.$Meta['Total']; ?> repositories affected.</p>
<table class="tablesorter">
	<thead>
		<tr>
			<th class="text-left">Repository</th>
			<th class="text-left">License (GitHub)</th>
			<th class="text-left">License (Detected)</th>
			<th class="text-left">Code of Conduct (GitHub)</th>
			<th class="text-left">Code of Conduct (Detected)</th>
			<th class="text-left">Contributing</th>
			<th class="text-left">Issue Template</th>
			<th class="text-left">Pull Request Template</th>
			<th class="text-left">ReadMe</th>
		</tr>
		<!--
		<tr class="faded tablesorter-childRow">
			<th class="text-left">Repository</th>
			<th class="text-left">LICENSE.md</th>
			<th class="text-left">.github/CODE_OF_CONDUCT.md</th>
			<th class="text-left">.github/CONTRIBUTING.md</th>
			<th class="text-left">.github/ISSUE_TEMPLATE.md</th>
			<th class="text-left">.github/PULL_REQUEST_TEMPLATE.md</th>
			<th class="text-left">README.md</th>
		</tr>
		-->
	</thead>
	<tbody><?php

	while ( $Repository = mysqli_fetch_assoc($Data) ) {

		echo '
		<tr>
			<td><a href="https://github.com/'.$Repository['Organisation'].'/'.$Repository['Repository'].'/community">'.
		$Repository['Repository'].'</a></td>';

		if (
			!empty($Repository['License_GitHub_Name']) &&
			$Repository['License_GitHub_Name'] != 'Other' &&
			strpos($Repository['License_GitHub_Link'], '/LICENSE.md') !== false
		) {
			echo '
			<td><a href="'.$Repository['License_GitHub_Link'].'" class="color-flatui-nephritis">'.$Repository['License_GitHub_Name'].'</a></td>';
		} else if (
			$Repository['License_GitHub_Name'] != 'Other' &&
			!empty($Repository['License_GitHub_Link'])
		) {
			echo '
			<td><a href="'.$Repository['License_GitHub_Link'].'" class="color-flatui-pumpkin">'.$Repository['License_GitHub_Name'].' in an invalid location.</a></td>';
		} else if (
			!empty($Repository['License_GitHub_Link']) &&
			strpos($Repository['License_GitHub_Link'], '/LICENSE.md') !== false
		) {
			echo '
			<td><a href="'.$Repository['License_GitHub_Link'].'" class="color-flatui-pumpkin">Undetectable License in an valid location.</a></td>';
		} else if ( !empty($Repository['License_GitHub_Link']) ) {
			echo '
			<td><a href="'.$Repository['License_GitHub_Link'].'" class="color-flatui-pumpkin">Undetectable License in an invalid location.</a></td>';
		} else {
			echo '
			<td class="color-flatui-pomegranate">No License</td>';
		}

		if ( !empty($Repository['License_Detected_Name']) ) {
			echo '
			<td><a href="https://github.com/'.
			$Repository['Organisation'].'/'.$Repository['Repository'].
			'/blob/master/LICENSE.md" class="color-'.$Repository['License_Detected_Color'].'">'.$Repository['License_Detected_Name'];
			if ( !empty($Repository['License_Detected_Year']) ) {
				echo ' ('.$Repository['License_Detected_Year'].')';
			}
			echo '</a></td>';
		} else {
			echo '
			<td></td>';
		}

		if (
			!empty($Repository['CoC_GitHub_Name']) &&
			strpos($Repository['CoC_GitHub_Link'], '/.github/CODE_OF_CONDUCT.md') !== false
		) {
			echo '
			<td><a href="'.$Repository['CoC_GitHub_Link'].'" class="color-flatui-nephritis">'.$Repository['CoC_GitHub_Name'].'</a></td>';
		} else if ( !empty($Repository['CoC_GitHub_Link']) ) {
			echo '
			<td><a href="'.$Repository['CoC_GitHub_Link'].'" class="color-flatui-pumpkin">'.$Repository['CoC_GitHub_Name'].' in an invalid location.</a></td>';
		} else {
			echo '
			<td><a href="https://github.com/'.
			$Repository['Organisation'].'/'.$Repository['Repository'].
			'/community/code-of-conduct/new" class="color-flatui-pomegranate">No Code of Conduct</td>';
		}

		if ( !empty($Repository['CoC_Detected_Name']) ) {
			echo '
			<td><a href="https://github.com/'.
			$Repository['Organisation'].'/'.$Repository['Repository'].
			'/blob/master/.github/CODE_OF_CONDUCT.md" class="color-'.$Repository['CoC_Detected_Color'].'">'.$Repository['CoC_Detected_Name'].'</a></td>';
		} else {
			echo '
			<td></td>';
		}

		if (
			!empty($Repository['Contributing']) &&
			strpos($Repository['Contributing'], '/.github/CONTRIBUTING.md') !== false
		) {
			echo '
			<td><a href="'.$Repository['Contributing'].'" class="color-flatui-nephritis">Contribution Guidelines</a></td>';
		} else if ( !empty($Repository['Contributing']) ) {
			echo '
			<td><a href="'.$Repository['Contributing'].'" class="color-flatui-pumpkin">Contribution Guidelines in an invalid location.</a></td>';
		} else {
			echo '
			<td class="color-flatui-pomegranate">No Contribution Guidelines</td>';
		}

		if (
			!empty($Repository['IssueTemplate']) &&
			strpos($Repository['IssueTemplate'], '/.github/ISSUE_TEMPLATE.md') !== false
		) {
			echo '
			<td><a href="'.$Repository['IssueTemplate'].'" class="color-flatui-nephritis">Issue Template</a></td>';
		} else if ( !empty($Repository['IssueTemplate']) ) {
			echo '
			<td><a href="'.$Repository['IssueTemplate'].'" class="color-flatui-pumpkin">Issue Template in an invalid location.</a></td>';
		} else {
			echo '
			<td class="color-flatui-pomegranate">No Issue Template</td>';
		}

		if (
			!empty($Repository['PullTemplate']) &&
			strpos($Repository['PullTemplate'], '/.github/PULL_REQUEST_TEMPLATE.md') !== false
		) {
			echo '
			<td><a href="'.$Repository['PullTemplate'].'" class="color-flatui-nephritis">Pull Request Template</a></td>';
		} else if ( !empty($Repository['PullTemplate']) ) {
			echo '
			<td><a href="'.$Repository['PullTemplate'].'" class="color-flatui-pumpkin">Pull Request Template in an invalid location.</a></td>';
		} else {
			echo '
			<td class="color-flatui-pomegranate">No Pull Request Template</td>';
		}

		if (
			!empty($Repository['ReadMe']) &&
			strpos($Repository['ReadMe'], '/README.md') !== false
		) {
			echo '
			<td><a href="'.$Repository['ReadMe'].'" class="color-flatui-nephritis">ReadMe</a></td>';
		} else if ( !empty($Repository['PullTemplate']) ) {
			echo '
			<td><a href="'.$Repository['ReadMe'].'" class="color-flatui-pumpkin">ReadMe in an invalid location.</a></td>';
		} else {
			echo '
			<td class="color-flatui-pomegranate">No ReadMe</td>';
		}

		echo '
		</tr>';
	}

?>

	</tbody>
</table>
<?php require_once $Sitewide['Templates']['Footer'];
