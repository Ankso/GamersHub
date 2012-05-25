<?php 
if (!isset($_GET['friendName']))
    die('<div style="background-color:#000000;color:#FF0000;">An error has been triggered. Please try again soon.</div>');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Confirm friend removal</title>
<style type="text/css">
.btnyes {
	min-width:50px;
	background-color:#FF0000;
	color:#000000;
	border:2px #FFFFFF solid;
	border-radius:0.5em;
}
.btnyes:hover {
	cursor:pointer;
}
</style>
<script type="text/javascript">
function SendRemoveFriend()
{
    var friendName = <?php echo '"'. $_GET['friendName'] .'"'; ?>;
    $.post("../core/friends/removefriend.php", {friendName: friendName}, function(data) {
		if (data.length > 0)
		{
			if (data == "SUCCESS")
			{
				$('div.button').html('<span class="btnyes">&nbsp;' + friendName + ' is no longer your friend&nbsp;</span>');
				$('span.btnyes').css("background-color", "#00FF00");
			}
			else
			    $('div.button').html('<span class="btnyes">&nbsp;An error occurred. Please try again soon.&nbsp;</span>');
		}
		else
		    $('div.button').html('<span class="btnyes">&nbsp;An unknown error occurred. Please try again soon.&nbsp;</span>');
    });
}
</script>
</head>
<body>
<div style="background-color:#000000; text-align:center;padding:20px 20px 20px 20px;">
	<span>Are you <b>really</b> sure that you want to remove <?php echo $_GET['friendName']; ?> from your friends?</span><br/>
	<div style="margin-top:15px;"><span class="btnyes" onclick="SendRemoveFriend();">&nbsp;Yes, I'm sure&nbsp;</span></div>
</div>
</body>
</html>