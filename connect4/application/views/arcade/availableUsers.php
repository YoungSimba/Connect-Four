
<table>
<?php 

	$first_name = $currentUser->first;
	echo "<p>" . $first_name . ", please choose someone to partner with</p>";
	
	if ($users) {
		foreach ($users as $user) {
			if ($user->first != $currentUser->first) {
?>		
			<tr>
			<td> 
			<?= anchor("arcade/pairUp?login=" . $user->id,$user->first) ?> 
			</td>
			</tr>

<?php 	
			}
		}
	}
?>

</table>
