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
            .button-sn:nth-of-type(1)
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
                Welcome
            </div>
            <div class="flex gap-16 my-32 px-2vw font-32">
                <a href="taskDrafts.php"><div class="card-2 font-18">Draft Tasks</div></a>
                <a href="taskUploads.php"><div class="card-2 font-18">Uploaded Tasks</div></a>
            </div>
        </div>
        <script src="./js/script.js"></script>
    </body>
</html>