<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/common.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/SharedDefines.php");

session_start();
if(!isset($_SESSION['userId']))
    die("Error: you must be logged in!");

$user = new User($_SESSION['userId']);
?>
<div class="myAccountMenu">
	<div class="myAccountMenuTop"><strong>Account configuration</strong></div>
	<div id="myAccountOptionBasic" class="myAccountMenuOption" onclick="MenuOptionClick(0);">Basic</div>
	<div id="myAccountOptionPrivacy" class="myAccountMenuOption" onclick="MenuOptionClick(1);">Privacy</div>
	<div id="myAccountOptionCustomization" class="myAccountMenuOption" onclick="MenuOptionClick(2);">Customization</div>
	<div id="myAccountOptionSecurity" class="myAccountMenuOption" onclick="MenuOptionClick(3);">Security</div>
</div>
<div id="myAccountBasic" class="myAccountTab">
	<div class="myAccountTabItem">*Username: <input disabled type="text" value="<?php echo $user->GetUsername(); ?>" /></div>
	<div class="myAccountTabItem">E-mail: <input id="email" type="text" value="<?php echo $user->GetEmail(); ?>" /></div>
	<div class="myAccountTabItem">New password: <input id="password" type="password" /></div>
	<div class="myAccountTabItem">Retype new password: <input id="passwordCheck" type="password" /></div>
	<div class="myAccountSubmit" onclick="SubmitBasicChanges();">Save</div><span id="myAccountSubmitResult" class="myAccountSubmitResult"></span>
	<div style="margin-top:55px; font:15px Calibri;"><i>*Note: This field can't be modified without the Staff's authorization.</i></div>
</div>
<div id="myAccountPrivacy" class="myAccountTab">
	<div class="myAccountTabItem">
		People who can view my email:
		<div class="myAccountSubTabItem">
			<input type="radio" name="email" value="noBody" />Nobody<br />
    		<input type="radio" name="email" value="friends" checked="checked" />Only my friends<br />
    		<input type="radio" name="email" value="clanMembers" />Only my friends and the members of my clan(s)<br />
    		<input type="radio" name="email" value="everyone" />Everyone (not recommended)
		</div>
	</div>
	<div class="myAccountTabItem">
		People who can view my profile details:
		<div class="myAccountSubTabItem">
    		<input type="radio" name="profileDetails" value="friends" checked="checked" />Only my friends<br />
    		<input type="radio" name="profileDetails" value="clanMembers" />Only my friends and the members of my clan(s)<br />
    		<input type="radio" name="profileDetails" value="everyone" />Everyone (not recommended)
		</div>
	</div>
	<div class="myAccountTabItem">
		People who can view my LiveStream:
		<div class="myAccountSubTabItem">
    		<input type="radio" name="liveStream" value="friends" checked="checked" />Only my friends<br />
    		<input type="radio" name="liveStream" value="clanMembers" />Only my friends and the members of my clan(s)<br />
    		<input type="radio" name="liveStream" value="everyone" />Everyone (not recommended)
		</div>
	</div>
	<div class="myAccountSubmit" onclick="SubmitPrivacyChanges();">Save</div><span id="myAccountSubmitResult" class="myAccountSubmitResult"></span>
</div>
<div id="myAccountCustomization" class="myAccountTab">
	<div class="myAccountTabItem">
		LiveStream:
		<div class="myAccountSubTabItem">
    		<input type="checkbox" name="liveStream" value="liveStream" checked="checked" />Enable LiveStream.<br />
    		<input type="checkbox" name="liveComments" value="liveComments" checked="checked" />Enable LiveStream's Live Comments section.
		</div>
	</div>
	<div class="myAccountTabItem">
		Latest News:
		<div class="myAccountSubTabItem">
			<input type="checkbox" name="latestNews" value="latestNews" checked="checked" />Enable Latest News section.
		</div>
	</div>
	<div class="myAccountSubmit" onclick="SubmitPrivacyChanges();">Save</div><span id="myAccountSubmitResult" class="myAccountSubmitResult"></span>
</div>
<div id="myAccountSecurity" class="myAccountTab">
	<div class="myAccountTabItem">Your last login: <?php echo ($user->GetLastLogin() != "1000-01-01 00:00:00") ? $user->GetLastLogin() : "This is your first login"; ?></div>
	<div class="myAccountTabItem">Last login from: <?php echo $user->GetLastIp(); ?></div>
	<div style="margin-top:20px; font:15px Calibri;"><i>Do you think that something is wrong? Then, change your password ASAP and, if you think that's necessary, contact us!</i></div>
</div>
<script type="text/javascript">
MenuOptionClick(0);
</script>