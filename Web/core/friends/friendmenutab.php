<?php
if (!isset($_GET['friendName']))
    die('<div><a class="ajax">Error loading data</a></div>');
$friendName = $_GET['friendName'];
?>
<html>
<head>
<script type="text/javascript">
$("a.removeFriend").fancybox();
</script>
</head>
<body>
<?php
echo '        <div><a class="ajax">Invite to chat</a></div>';
echo '        <div><a class="ajax">Invite to LiveStream</a></div>';
echo '        <div><a class="ajax">Send private message</a></div>';
echo '        <div style="background-color:#FF0000;"><a class="removeFriend" href="ajax/removefriendconfirmation.php?friendName='. $friendName .'">Remove friend</a></div>';
?>
</body>
</html>