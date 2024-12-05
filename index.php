<?php
    session_start();
    if(isset($_SESSION['employee'])) {
        header("Location: dashboard.php");
    }
    if(isset($_REQUEST['submit'])) {

        if(isset($_REQUEST['phone']) && isset($_REQUEST['password'])) {

            $phone = $_REQUEST['phone'];
            $password = md5($_REQUEST['password']);

            include("./connection/connection.php");
            $query = "SELECT `emp_id`, `phone`, `password`, `pswd_state`, `dob` FROM `employee_master`";
            $data = mysqli_query($connection, $query);

            while($ar = mysqli_fetch_array($data)) {

                if($ar['phone'] == $phone && $ar['password'] == $password) {
                    
                    $_SESSION['employee'] = $ar['emp_id'];
                    $DOB = explode("-", $ar['dob']);
                    $_SESSION['dob'] = $DOB[2]."-".$DOB[1];
                    if($ar['pswd_state'])
                    {
                        $_SESSION['pswd-state'] = $ar['pswd_state'];
                    }
                    header("Location: dashboard.php");
                    exit();
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php include("title.php"); ?>
        <link rel="stylesheet" href="./css/style.css">
    </head>
    <body>
        <div class="flex justify-center align-center h100-vh">
            <form class="base-form flex flex-column gap-16" action="" method="POST">
                <div class="text-center font-28">Sign In</div>
                <br>
                <div class="flex flex-column gap-8 font-18">
                    Phone
                    <input class="input-field font-16" type="text" placeholder="Enter your phone" name="phone">
                </div>
                <div class="flex flex-column gap-8 font-18">
                    Password
                    <input class="input-field font-16" type="password" placeholder="Enter your password" name="password">
                </div>
                <div class="flex flex-column gap-8">
                    <input class="button-1 font-16" type="submit" name="submit">
                </div>
            </form>
        </div>
    </body>
</html>