
<!DOCTYPE html>

<html>
<head>
	<script src="http://code.jquery.com/jquery-latest.js"></script>
	<script src="<?= base_url() ?>/js/jquery.timers.js"></script>
	<script>
		$(function(){
			$("#partners").everyTime(1000,function(){
				$("#partners").load('<?= base_url() ?>arcade/getAvailableUsers');
			});
		});
	</script>
</head>
<body>  
	<h1>Easy Pairing</h1>
	
<div id="partners">
</div>
</body>

</html>

 
