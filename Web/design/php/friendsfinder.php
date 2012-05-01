<?php
require_once("F:/GamersNet/GamersNet_Beta/Common/SharedDefines.php");
require_once("F:/GamersNet/GamersNet_Beta/Classes/Database.Class.php");

if (isset($_POST['nickname']))
{
    if (strlen($_POST['nickname']) >= 3)
    {
        $DB = New Database($DATABASES['USERS']);
        $result = $DB->Execute("SELECT username FROM user_data WHERE username LIKE '". $_POST['nickname'] ."%' ORDER BY username LIMIT 10");
        if ($result)
        {
            if (mysql_num_rows($result) === 0)
                echo '<div class="listItem">No users found with that nickname</div>';
            else
            {
                while ($row = mysql_fetch_array($result, MYSQL_NUM))
                    echo '<div class="listItem" onclick="Fill(\'', $row[0], '\');">', $row[0], '</div>';
            }
        }
        elseif($result === false)
            echo '<div class="listItem">There was a problem connecting to the server</div>';
    }
}
?>