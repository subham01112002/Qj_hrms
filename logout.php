<?php
    session_start();
    unset($_SESSION['employee'], $_SESSION['dob'], $_SESSION['pswd-state']);
    session_destroy();
    header("Location: ./index.php");
?>