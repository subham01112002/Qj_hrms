<?php
    session_start();
    include("./loginCheck.php");
    include("./connection/connection.php");
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
            .button-sn:nth-of-type(2)
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
            <div class="my-32 px-2vw">
                <div class="font-32">
                    Messages
                </div>
                <div class="font-16 color-2" style="color: grey;">Messages from admin</div>
            </div>
            <div class="my-32 px-2vw">
                <div class="font-22">Your messages</div>
                <div class="my-8 flex flex-column gap-16">
                    <?php
                        $query = "SELECT * FROM `message_master` WHERE `emp_id` = '".$_SESSION['employee']."' OR `emp_id` = 0";
                        $data = mysqli_query($connection, $query);
                        while($ar = mysqli_fetch_array($data))
                        {
                    ?>
                            <textarea class="card-1 font-18 w100-percent"><?php echo trim($ar['message']); ?></textarea>
                    <?php
                        }
                    ?>
                </div>
            </div>
        </div>
        <script src="./js/script.js"></script>
    </body>
</html>