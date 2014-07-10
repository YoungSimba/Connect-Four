
<!DOCTYPE html>

<html>
<body>  
	<h1>New User</h1>
<?php 
	if (isset($errorMsg)) {
		echo "<p>" . $errorMsg . "</p>";
	}

	echo form_open('account/createNew');
	echo form_label('Name'); 
	echo form_error('name');
	echo form_input('name',set_value('name'),"required");
	echo form_submit('submit', 'Login');
	echo form_close();
?>	
</body>

</html>

