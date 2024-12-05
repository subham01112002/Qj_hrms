<?php
    session_start();
    include("./loginCheck.php");
    include("./connection/connection.php");
    include("./functions/sessionMessage.php");
    $empID = $_SESSION['employee'];
    $curDate = date("Y-m-d");
    $status = 1;
    if(isset($_REQUEST['apply']))
    {
        if(!isset($_REQUEST['start-date']) || $_REQUEST['start-date'] == "" || !isset($_REQUEST['end-date']) || $_REQUEST['end-date'] == "" || !isset($_REQUEST['reason']) || $_REQUEST['reason'] == "")
        {
            header("Location: leaves.php");
            exit();
        }
        $startDate = $_REQUEST['start-date'];
        $endDate = $_REQUEST['end-date'];
        if($startDate > $endDate)
        {
            setSessionMessage(1, "Start Date cannot be greater than End Date", "./leaves.php");
            exit();
        }
        if($endDate < $curDate)
        {
            $status = 0;
        }
        $reason = $_REQUEST['reason'];
        $query = "INSERT INTO `employee_leave_map` (`emp_id`, `leave_id`, `start_date`, `end_date`, `count`, `reason`, `status`) VALUES ('$empID', 1, '$startDate', '$endDate', DATEDIFF('$endDate', '$startDate'), '$reason', '$status');";
        $data = mysqli_query($connection, $query);
        setSessionMessage($data, "Leave application submitted successfully", "./leaves.php");
    }
    if(isset($_REQUEST['update']))
    {
        if(!isset($_REQUEST['start-date']) || $_REQUEST['start-date'] == "" || !isset($_REQUEST['end-date']) || $_REQUEST['end-date'] == "" || !isset($_REQUEST['reason']) || $_REQUEST['reason'] == "")
        {
            header("Location: leaves.php");
            exit();
        }
        $elID = (int) $_REQUEST['el-id'];
        $startDate = $_REQUEST['start-date'];
        $endDate = $_REQUEST['end-date'];
        if($startDate > $endDate)
        {
            setSessionMessage(1, "Start Date cannot be greater than End Date", "./leaves.php");
            exit();
        }
        if($endDate < $curDate)
        {
            $status = 0;
        }
        $reason = $_REQUEST['reason'];
        $query = "UPDATE `employee_leave_map` SET `leave_id` = 1, 
                                                  `start_date` = '$startDate', 
                                                  `end_date` = '$endDate',
                                                  `count` = DATEDIFF('$endDate', '$startDate'),
                                                  `reason` = '$reason',
                                                  `status` = '$status' WHERE `el_id` = '$elID' AND `start_date` > '$curDate' AND `status` = 1;";
        $data = mysqli_query($connection, $query);
        setSessionMessage($data, "Leave application updated successfully", "./leaves.php");
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
            .button-sn:nth-of-type(3)
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
                    Leaves
                </div>
                <div class="font-16 color-2" style="color: grey;">Apply or view your leaves here</div>
            </div>
            <div class="my-32 px-2vw">
                <input class="button-1 font-16" type="button" onclick="toggleOverlay()" value="Apply Leave">
                <?php printSessionMessage(); ?>
            </div>
            <div class="my-32 px-2vw">
                <div class="font-22">Your Leaves</div>
                <div class="my-8 flex flex-column gap-16">
                    <?php
                        $query = "SELECT
                                    elm.*
                                FROM `employee_leave_map` elm
                                WHERE elm.`emp_id` = '$empID';";
                        $data = mysqli_query($connection, $query);
                        $leaveStatus = ["Rejected", "Waitlisted", "Approved"];
                        while($ar = mysqli_fetch_array($data))
                        {
                    ?>
                            <div class="card-1">
                                <form class="flex flex-column gap-16" method="POST">
                                    <input type="hidden" value="<?php echo $ar['el_id']; ?>" name="el-id">
                                    <div class="flex align-center justify-between" style="min-height: 45px;">
                                        <div class="flex align-center gap-8">
                                            <img src="./res/icons/right-cheveron.svg" alt="" onclick="showDetails(this)">
                                            <div class="font-18">Casual Leave</div>
                                        </div>
                                        <?php
                                            if($ar['start_date'] > $curDate && $ar['status'] == 1)
                                            {
                                        ?>
                                                <input class="button-1 font-16" type="submit" value="Update" name="update">
                                        <?php
                                            }
                                            else
                                            {
                                                echo $leaveStatus[($ar['status']+1)];
                                            }
                                        ?>
                                    </div>
                                    <div class="pl-24">
                                        <div class="bg-color-1 h2-px"></div>
                                        <div class="font-18 my-8">
                                            <div class="flex gap-16">
                                                <div>
                                                    Start Date:
                                                    <br>
                                                    <input class="input-field font-18" type="date" value="<?php echo $ar['start_date']; ?>" name="start-date">
                                                </div>
                                                <div>
                                                    End Date:
                                                    <br>
                                                    <input class="input-field font-18" type="date" value="<?php echo $ar['end_date']; ?>" name="end-date">
                                                </div>
                                                <div>
                                                    No.of Days:
                                                    <br>
                                                    <input class="input-field font-18" type="number" value="<?php echo $ar['count']; ?>">
                                                </div>
                                            </div>
                                            <div class="my-8">
                                                Reason:
                                                <textarea class="input-field font-18 w100-percent" name="reason" id=""><?php echo trim($ar['reason']); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                    <?php
                        }
                    ?>
                </div>
            </div>
        </div>
        <div id="overlay-form" class="overlay flex align-center justify-center absolute h100-vh">
            <form class="base-form flex flex-column gap-16">
                <div class="text-center font-28">Apply for Leave</div>
                <br>
                <div class="flex flex-column gap-8 font-18">
                    Leave type
                    <select class="input-field font-16" disabled>
                        <option value="" selected>Casual Leave</option>
                    </select>
                </div>
                <div class="flex flex-column gap-8 font-18">
                    Start Date
                    <input id="start-date" class="input-field font-16" type="date" name="start-date" onchange="checkInputField(this)">
                </div>
                <div class="flex flex-column gap-8 font-18">
                    End Date
                    <input id="end-date" class="input-field font-16" type="date" name="end-date" onchange="checkInputField(this)">
                </div>
                <div class="flex flex-column gap-8 font-18">
                    Reason of Leave
                    <textarea id="reason" class="input-field font-16 resize-y" name="reason" style="height: 100px; max-height: 100px;" onchange="checkInputField(this)"></textarea>
                </div>
                <div class="flex flex-column gap-8">
                    <input id="apply-leave" class="button-2 font-16" type="submit" value="Apply" name="apply">
                </div>
                <div class="flex flex-column gap-8">
                    <input class="button-2 font-16" type="button" value="Cancel" onclick="toggleOverlay()">
                </div>
            </form>
        </div>
        <script src="./js/script.js"></script>
        <script src="./js/validator.js"></script>
    </body>
</html>