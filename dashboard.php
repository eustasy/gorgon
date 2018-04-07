<?php

require_once __DIR__.'/_puff/sitewide.php';

$Page['Type']        = 'Page';
$Page['Title']       = 'Gorgon Dashboard';
$Page['Description'] = '';
require_once $Sitewide['Templates']['Header'];

$Data = 'SELECT * FROM `Meta`;';
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
<h1>Gorgon</h1>
<p>GitHub Organisation Management</p>
<table class="duplex tablesorter">
	<thead>
		<tr>
			<th class="clickable text-left">Name
			<th class="clickable text-right">Updated
			<th class="clickable text-right">Affected
			<th class="clickable text-right">Work Items
			<th class="clickable text-right">Covered
			<th class="clickable text-right">Completion
		</tr>
	</thead>
	<tbody><?php

		while ( $Row = mysqli_fetch_assoc($Data) ) {
			echo '
			<tr>
				<td><a href="'.$Row['Name'].'">'.$Row['Name'].'</a></td>';

			if ( $Row['Updated'] >= ( $Time - 3600 ) ) {
				echo '
				<td class="color-flatui-nephritis';
			} else {
				echo '
				<td class="color-flatui-pumpkin';
			}
			echo ' text-right" data-text="'.$Row['Updated'].'">';
			echo strtolower(Time_Readable_Difference($Row['Updated'])['Preferred']);
			echo '</td>';

			if ( $Row['Total'] == $Row['Affected'] ) {
				echo '
				<td class="color-flatui-pomegranate text-right">'.$Row['Affected'].'</td>';
			} else if ( $Row['Affected'] > 0 ) {
				echo '
				<td class="color-flatui-pumpkin text-right">'.$Row['Affected'].'</td>';
			} else {
				echo '
				<td class="color-flatui-nephritis text-right">'.$Row['Affected'].'</td>';
			}

			if ( $Row['WorkItems'] > 0 ) {
				echo '
				<td class="color-flatui-pomegranate text-right">'.$Row['WorkItems'].'</td>';
			} else if ( $Row['Percentage'] >= 100 ) {
				echo '
				<td class="color-flatui-nephritis text-right">0</td>';
			} else {
				echo '
				<td class="color-flatui-asbestos text-right">Unknown</td>';
			}

			if ( $Row['Total'] > 0 ) {
				echo '
				<td class="color-flatui-nephritis text-right">'.$Row['Total'].'</td>';
			} else {
				echo '
				<td class="color-flatui-pomegranate text-right">'.$Row['Total'].'</td>';
			}

			if ( $Row['Percentage'] >= 100 ) {
				echo '
				<td class="color-flatui-nephritis text-right">100%</td>';
			} else if ( $Row['Percentage'] >= 75 ) {
				echo '
				<td class="color-flatui-pumpkin text-right">'.$Row['Percentage'].'%</td>';
			} else {
				echo '
				<td class="color-flatui-pomegranate text-right">'.$Row['Percentage'].'%</td>';
			}

			echo '
			</tr>';
		}
	?>
	</tbody>
</table>
<?php require_once $Sitewide['Templates']['Footer'];
