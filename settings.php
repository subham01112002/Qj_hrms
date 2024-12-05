<?php
    session_start();
    include("./loginCheck.php");
    include("./connection/connection.php");
    include("./functions/sessionMessage.php");
    if(isset($_REQUEST['submit']))
    {
        if(!isset($_REQUEST['password']))
        {
            header("Location: ./settings.php");
            exit();
        }
        $empID = $_SESSION['employee'];
        $password = md5($_REQUEST['password']);
        $query = "UPDATE `employee_master` SET `password` = '$password',
                                               `pswd_state` = 0 WHERE `emp_id` = $empID";
        $data = mysqli_query($connection, $query);
        if($data)
        {
            if(isset($_SESSION['pswd-state']))
            {
                unset($_SESSION['pswd-state']);
            }
        }
        setSessionMessage($data, "Password changed successfully", "./settings.php");
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php include("./title.php"); ?>
        <link rel="stylesheet" href="./css/style.css">
        <link rel="stylesheet" href="./css/navigation.css">
    </head>
    <body>
        <style>
            .button-sn:nth-of-type(4)
            {
                background-color: var(--theme-color-2);
                img
                {
                    filter: invert(1);
                }
                div
                {
                    color: var(--theme-color-3);
                }
            }
        </style>
        <?php include("./topNavigation.php"); ?>
        <?php include("./sideNavigation.php"); ?>
        <div class="container">
            <?php include("./functions/passwordState.php"); ?>
            <div class="my-32 px-2vw font-32">
                Settings
            </div>
            <div class="my-32 px-2vw">
                <form class="w300-px" method="POST">
                    <div class="flex flex-column gap-16">
                        <div class="flex flex-column gap-8 font-18">
                            Change Password
                            <input class="input-field font-16" type="password" placeholder="Enter new password" name="password">
                        </div>
                        <div class="flex flex-column gap-8">
                            <input class="button-1 font-16" type="submit" value="Submit" name="submit">
                        </div>
                    </div>
                </form>
                <?php printSessionMessage(); ?>
            </div>
        </div>
        <script src="./js/script.js"></script>
    </body>
</html>