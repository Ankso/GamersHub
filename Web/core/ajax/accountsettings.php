<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/common.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/SharedDefines.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/SessionHandler.Class.php");

$sessionsHandler = new CustomSessionsHandler();
session_set_save_handler(
    array($sessionsHandler, "open"),
    array($sessionsHandler, "close"),
    array($sessionsHandler, "read"),
    array($sessionsHandler, "write"),
    array($sessionsHandler, "destroy"),
    array($sessionsHandler, "gc")
    );
register_shutdown_function("session_write_close");
session_start();
if(!isset($_SESSION['userId']))
    die("Error: you must be logged in!");

$user = new User($_SESSION['userId']);
$privacySettings = $user->GetPrivacySettings();
$customOptions = $user->GetCustomOptions();
?>
<script type="text/javascript">
$("div.myAccountMenuOption").click(function(event) {
	MyAccountMenuOptionClick(event);
});
$("div#myAccountOptionBasic").trigger("click");
</script>
<div class="myAccountMenu">
	<div class="myAccountMenuTop"><strong>Account configuration</strong></div>
	<div id="myAccountOptionBasic" class="myAccountMenuOption">Basic</div>
	<div id="myAccountOptionPrivacy" class="myAccountMenuOption">Privacy</div>
	<div id="myAccountOptionCustomization" class="myAccountMenuOption">Customization</div>
	<div id="myAccountOptionSecurity" class="myAccountMenuOption">Security</div>
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
    		<div id="radioEmail" class="myAccountRadio"><br />
    			<input type="radio" id="radio1" name="email" value="0" <?php if ($privacySettings[USER_PRIVACY_EMAIL] == PRIVACY_LEVEL_NOBODY) echo 'checked="checked"'; ?> /><label for="radio1">Nobody</label>
        		<input type="radio" id="radio2" name="email" value="1" <?php if ($privacySettings[USER_PRIVACY_EMAIL] == PRIVACY_LEVEL_FRIENDS) echo 'checked="checked"'; ?> /><label for="radio2">Only my friends</label>
        		<input type="radio" id="radio3" name="email" value="2" <?php if ($privacySettings[USER_PRIVACY_EMAIL] == PRIVACY_LEVEL_CLAN_MEMBERS) echo 'checked="checked"'; ?> /><label for="radio3">Only friends and clan members</label>
        		<input type="radio" id="radio4" name="email" value="3" <?php if ($privacySettings[USER_PRIVACY_EMAIL] == PRIVACY_LEVEL_EVERYONE) echo 'checked="checked"'; ?> /><label for="radio4">Everyone (not recommended)</label>
    		</div>
    	</div>
	</div>
	<div class="myAccountTabItem">
		People who can view my profile details:
		<div class="myAccountSubTabItem">
			<div id="radioProfile" class="myAccountRadio"><br />
        		<input type="radio" id="radio5" name="profileDetails" value="1" <?php if ($privacySettings[USER_PRIVACY_PROFILE] == PRIVACY_LEVEL_FRIENDS) echo 'checked="checked"'; ?> /><label for="radio5">Only my friends</label>
        		<input type="radio" id="radio6" name="profileDetails" value="2" <?php if ($privacySettings[USER_PRIVACY_PROFILE] == PRIVACY_LEVEL_CLAN_MEMBERS) echo 'checked="checked"'; ?> /><label for="radio6">Only friends and clan members</label>
        		<input type="radio" id="radio7" name="profileDetails" value="3" <?php if ($privacySettings[USER_PRIVACY_PROFILE] == PRIVACY_LEVEL_EVERYONE) echo 'checked="checked"'; ?> /><label for="radio7">Everyone</label>
			</div>
		</div>
	</div>
	<div class="myAccountTabItem">
		People who can view my LiveStream:
		<div class="myAccountSubTabItem">
			<div id="radioLiveStream" class="myAccountRadio"><br />
        		<input type="radio" id="radio8" name="liveStream" value="1" <?php if ($privacySettings[USER_PRIVACY_LIVESTREAM] == PRIVACY_LEVEL_FRIENDS) echo 'checked="checked"'; ?> /><label for="radio8">Only my friends</label>
        		<input type="radio" id="radio9" name="liveStream" value="2" <?php if ($privacySettings[USER_PRIVACY_LIVESTREAM] == PRIVACY_LEVEL_CLAN_MEMBERS) echo 'checked="checked"'; ?> /><label for="radio9">Only friends and clan members</label>
        		<input type="radio" id="radio10" name="liveStream" value="3" <?php if ($privacySettings[USER_PRIVACY_LIVESTREAM] == PRIVACY_LEVEL_EVERYONE) echo 'checked="checked"'; ?> /><label for="radio10">Everyone</label>
			</div>
		</div>
	</div>
	<div class="myAccountSubmit" onclick="SubmitPrivacyChanges();">Save</div><span id="myAccountSubmitResult" class="myAccountSubmitResult"></span>
</div>
<div id="myAccountCustomization" class="myAccountTab">
	<div class="myAccountTabItem">
		LiveStream:
		<div class="myAccountSubTabItem">
    		<input type="checkbox" name="liveStream" <?php if ($customOptions[CUSTOM_OPTION_LIVESTREAM]) echo 'checked="checked"'; ?> />Enable LiveStream.<br />
    		<input type="checkbox" name="liveStreamComments" <?php if ($customOptions[CUSTOM_OPTION_LIVESTREAM_COMMENTS]) echo 'checked="checked"' ?> />Enable LiveStream's Live Comments section.
		</div>
	</div>
	<!-- This option, latest news section, has no really sense, it will be enabled yes or yes. It's only here to try different options than LiveStream -->
	<div class="myAccountTabItem">
		Latest News:
		<div class="myAccountSubTabItem">
			<input type="checkbox" name="latestNews" <?php if ($customOptions[CUSTOM_OPTION_LATEST_NEWS]) echo 'checked="checked"'; ?> />Enable Latest News section.
		</div>
	</div>
	<div class="myAccountSubmit" onclick="SubmitCustomizationChanges();">Save</div><span id="myAccountSubmitResult" class="myAccountSubmitResult"></span>
</div>
<div id="myAccountSecurity" class="myAccountTab">
	<div class="myAccountTabItem">Your last login: <?php echo ($user->GetLastLogin() != "1000-01-01 00:00:00") ? $user->GetLastLogin() : "This is your first login"; ?></div>
	<div class="myAccountTabItem">Last login from: <?php echo $user->GetLastIp(); ?></div>
	<div style="margin-top:20px; font:15px Calibri;"><i>Do you think that something is wrong? Then, change your password ASAP and, if you think that's necessary, contact us!</i></div>
</div>