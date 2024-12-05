<?php
    session_start();
    include("./loginCheck.php");
    include("./connection/connection.php");
    include("./functions/sessionMessage.php");
    $empID = $_SESSION['employee'];
    $curMonth = date("m");
    $query = "SELECT 
                  COUNT(ptmt.`pt_id`) AS length
              FROM `project_task_map_temp` ptmt
              INNER JOIN `project_employee_map` pem ON pem.`pe_id` = ptmt.`pe_id`
              WHERE MONTH(ptmt.`date`) < $curMonth AND pem.`emp_id` = $empID";
    $data = mysqli_query($connection, $query);
    $length = mysqli_fetch_array($data)['length'];
    if($length)
    {
        $_SESSION['prev-month'] = 1;
    }
    if(isset($_REQUEST['add']))
    {
        if(!isset($_REQUEST['pe-id']) || $_REQUEST['pe-id'] == 0 || !isset($_REQUEST['tst-id']) || $_REQUEST['tst-id'] == 0 || !isset($_REQUEST['date']) || $_REQUEST['date'] == "" || !isset($_REQUEST['start-time']) || $_REQUEST['start-time'] == "" || !isset($_REQUEST['end-time']) || $_REQUEST['end-time'] == "")
        {
            header("Location: ./taskDrafts.php");
            exit();
        }
        $date = $_REQUEST['date'];
        if(date('m', strtotime($date)) == date('m') && $length > 0)
        {
            header("Location: ./taskDrafts.php");
            exit();
        }
        if($date > date("Y-m-d"))
        {
            setSessionMessage(1, "Date cannot exceed today's date", "./taskDrafts.php");
            exit();
        }
        $peID = (int) $_REQUEST['pe-id'];
        $taskMapID = (int) $_REQUEST['tst-id'];
        $startTime = $_REQUEST['start-time'];
        $endTime = $_REQUEST['end-time'];
        $remarks = $_REQUEST['remarks'];
        $query = "INSERT INTO `project_task_map_temp` (`pe_id`, `tst_id`, `start_time`, `end_time`, `total`, `date`, `remarks`) VALUES ('$peID', '$taskMapID', '$startTime', '$endTime', TIMEDIFF('$endTime', '$startTime'), '$date', '$remarks')";
        $data = mysqli_query($connection, $query);
        setSessionMessage($data, "Task added successfully", "./taskDrafts.php");
    }
    if(isset($_REQUEST['update']))
    {
        if(!isset($_REQUEST['pt-id']) || !isset($_REQUEST['date']) || $_REQUEST['date'] == "" || !isset($_REQUEST['start-time']) || $_REQUEST['start-time'] == "" || !isset($_REQUEST['end-time']) || $_REQUEST['end-time'] == "")
        {
            header("Location: ./taskDrafts.php");
            exit();
        }
        $date = $_REQUEST['date'];
        if(date('m', strtotime($date)) == date('m') && $length > 0)
        {
            header("Location: ./taskDrafts.php");
            exit();
        }
        if($date > date("Y-m-d"))
        {
            setSessionMessage(1, "Date cannot exceed today's date", "./taskDrafts.php");
            exit();
        }
        $ptID = (int) $_REQUEST['pt-id'];
        $startTime = $_REQUEST['start-time'];
        $endTime = $_REQUEST['end-time'];
        $remarks = $_REQUEST['remarks'];
        $query = "UPDATE `project_task_map_temp` SET `start_time` = '$startTime',
                                                     `end_time` = '$endTime',
                                                     `total` = TIMEDIFF('$endTime', '$startTime'),
                                                     `date` = '$date',
                                                     `remarks` = '$remarks' WHERE `pt_id` = '$ptID'";
        $data = mysqli_query($connection, $query);
        setSessionMessage($data, "Task updated successfully", "./taskDrafts.php");
    }
    if(isset($_REQUEST['submit']))
    {
        if(!isset($_REQUEST['submit-month']) || $_REQUEST['submit-month'] == 0)
        {
            header("Location: ./taskDrafts.php");
            exit();
        }
        $submitMonth = (int) $_REQUEST['submit-month'];
        $query = "INSERT INTO `project_task_map`
                  SELECT 
                      ptmt.*
                  FROM `project_task_map_temp` ptmt
                  INNER JOIN `project_employee_map` pem ON pem.`pe_id` = ptmt.`pe_id`
                  WHERE pem.`emp_id` = $empID
                  AND MONTH(ptmt.`date`) = $submitMonth";
        $data = mysqli_query($connection, $query);
        if($data)
        {
            $query = "DELETE ptmt
                      FROM `project_task_map_temp` ptmt
                      INNER JOIN `project_employee_map` pem ON pem.`pe_id` = ptmt.`pe_id`
                      WHERE pem.`emp_id` = $empID
                      AND MONTH(ptmt.`date`) = $submitMonth";
            $data = mysqli_query($connection, $query);
            setSessionMessage($data, "Tasks submitted successfully", "./taskDrafts.php");
        }
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
                Draft Tasks
                <div class="font-16 color-2" style="color: grey;">Draft or submit your tasks here</div>
            </div>
            <div class="my-32 px-2vw">
                <input class="button-1 font-16" type="button" onclick="toggleOverlay()" value="Add your work +">
                <?php
                    if(isset($_SESSION['prev-month']))
                    {
                        echo "<div class=\"my-8\" style=\"color: red\">Submit previous months drafts to add this months drafts</div>";
                        unset($_SESSION['prev-month']);
                    }
                    printSessionMessage();
                ?>
            </div>
            <div class="my-32 px-2vw overflow-x-scroll">
                <div class="table w100-percent font-16">
                    <div class="row">
                        <div class="cell" style="min-width: 64px !important;"><b>Sl. No.</b></div>
                        <div class="cell"><b>Date</b></div>
                        <div class="cell"><b>Project Name</b></div>
                        <div class="cell"><b>Worked On</b></div>
                        <div class="cell"><b>From</b></div>
                        <div class="cell"><b>To</b></div>
                        <div class="cell"><b>Total Time Worked</b></div>
                        <div class="cell"><b>Remarks</b></div>
                        <div class="cell"><b>Operations</b></div>
                    </div>
                    <?php
                        $query = "SELECT
                                    ptmt.`pt_id`,
                                    ptmt.`start_time`,
                                    ptmt.`end_time`,
                                    ptmt.`total`,
                                    ptmt.`date`,
                                    ptmt.`remarks`,
                                    pm.`title` AS project_title,
                                    tm.`title` AS tm_title,
                                    stm.`title` AS stm_title
                                FROM `project_task_map_temp` ptmt
                                INNER JOIN `project_employee_map` pem ON pem.`pe_id` = ptmt.`pe_id`
                                INNER JOIN `task_sub_task_map` tstm ON tstm.`tst_id` = ptmt.`tst_id`
                                INNER JOIN `project_master` pm ON pm.`project_id` = pem.`project_id`
                                INNER JOIN `task_master` tm ON tm.`task_id` = tstm.`task_id`
                                INNER JOIN `sub_task_master` stm ON stm.`sub_task_id` = tstm.`sub_task_id`
                                WHERE pem.`emp_id` = '$empID'
                                ORDER BY ptmt.`date` DESC";
                        $data = mysqli_query($connection, $query);
                        $slNo = 1;
                        while($ar = mysqli_fetch_array($data))
                        {
                    ?>
                            <form class="row" method="POST">
                                <input type="hidden"  value="<?php echo $ar['pt_id']; ?>" name="pt-id">
                                <div class="cell" style="min-width: 64px !important;">
                                    <?php echo $slNo++; ?>
                                </div>
                                <div class="cell">
                                    <input class="font-16" type="date" value="<?php echo $ar['date']; ?>" name="date" max="<?php echo date("Y-m-d"); ?>">
                                </div>
                                <div class="cell">
                                    <?php echo $ar['project_title']; ?>
                                </div>
                                <div class="cell">
                                    <?php echo $ar['tm_title']; ?> > <?php echo $ar['stm_title']; ?>
                                </div>
                                <div class="cell">
                                    <input class="font-16" type="time" value="<?php echo $ar['start_time']; ?>" name="start-time">
                                </div>
                                <div class="cell">
                                    <input class="font-16" type="time" value="<?php echo $ar['end_time']; ?>" name="end-time">
                                </div>
                                <div class="cell">
                                    <?php echo substr($ar['total'], 0, 5); ?>
                                </div>
                                <div class="cell">
                                    <input class="font-16" type="text" value="<?php echo $ar['remarks']; ?>" name="remarks">
                                </div>
                                <div class="cell" style="width: min-content">
                                    <input class="button-1" type="submit" value="Update" name="update">
                                </div>
                            </form>
                    <?php
                        }
                    ?>
                </div>
            </div>
            <div class="my-32 px-2vw flex">
                <form class="w300-px" method="POST">
                    <div class="flex flex-column gap-16">
                        <div class="flex flex-column gap-8 font-18">
                            Select a Month
                            <select class="input-field font-16" name="submit-month">
                                <option value="0">Select a month</option>
                                <?php
                                    $query = "SELECT
                                                MONTH(ptmt.`date`) AS month_num,
                                                MONTHNAME(ptmt.`date`) AS month_name
                                            FROM `project_task_map_temp` ptmt
                                            INNER JOIN `project_employee_map` pem ON pem.`pe_id` = ptmt.`pe_id`
                                            WHERE pem.`emp_id` = '$empID'
                                            GROUP BY MONTH(ptmt.`date`), MONTHNAME(ptmt.`date`)
                                            ORDER BY month_num";
                                    $data = mysqli_query($connection, $query);
                                    while($ar = mysqli_fetch_array($data))
                                    {
                                ?>
                                        <option value="<?php echo $ar['month_num']; ?>"><?php echo $ar['month_name']; ?></option>
                                <?php
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="flex flex-column gap-8">
                            <input class="button-1 font-16" type="submit" value="Submit" name="submit">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div id="overlay-form" class="overlay flex align-start justify-center fixed h100-vh">
            <form class="base-form flex flex-column gap-16">
                <div class="text-center font-28">Add your work</div>
                <br>
                <div class="flex flex-column gap-8 font-18">
                    Select a project
                    <select id="pe-id" class="input-field font-16" name="pe-id" onchange="checkInputField(this)">
                        <option value="0">Select a project</option>
                        <?php
                            $query = "SELECT
                                        pem.`pe_id`,
                                        pm.`title` 
                                    FROM `project_master` pm
                                    INNER JOIN `project_employee_map` pem ON pem.`project_id` = pm.`project_id`
                                    WHERE pem.`emp_id` = '$empID' AND pem.`end_date` IS NULL";
                            $data = mysqli_query($connection, $query);
                            while($ar = mysqli_fetch_array($data))
                            {
                        ?>
                                <option value="<?php echo $ar['pe_id']; ?>">
                                    <?php echo $ar['title']; ?>
                                </option>
                        <?php
                            }
                        ?>
                    </select>
                </div>
                <div class="flex flex-column gap-8 font-18">
                    Select a Primary Task
                    <select id="primary-task" class="input-field font-16" onchange="getSecondaryTasks(); checkInputField(this);" name="primt-id">
                        <option value="0">Select a primary task</option>
                        <?php
                            $query = "SELECT * FROM `task_master`";
                            $data = mysqli_query($connection, $query);
                            while($ar = mysqli_fetch_array($data))
                            {
                        ?>
                                <option value="<?php echo $ar['task_id']; ?>">
                                    <?php echo $ar['title']; ?>
                                </option>
                        <?php
                            }
                        ?>
                    </select>
                </div>
                <div class="flex flex-column gap-8 font-18">
                    Select a Secondary Task
                    <select id="secondary-task" class="input-field font-16" name="tst-id" onchange="checkInputField(this)">
                        <option value="0">Select a secondary task</option>
                    </select>
                </div>
                <div class="flex flex-column gap-8 font-18">
                    Date
                    <input id="task-date" class="input-field font-16" type="date" name="date" max="<?php echo date("Y-m-d"); ?>" value="<?php echo date("Y-m-d");?>" onchange="checkInputField(this)">
                </div>
                <div class="flex flex-column gap-8 font-18">
                    Start Time
                    <input id="task-start-time" class="input-field font-16" type="time" name="start-time" value="10:00" onchange="checkInputField(this)">
                </div>
                <div class="flex flex-column gap-8 font-18">
                    End Time
                    <input id="task-end-time" class="input-field font-16" type="time" name="end-time" value="18:00" onchange="checkInputField(this)">
                </div>
                <div class="flex flex-column gap-8 font-18">
                    Remarks
                    <input class="input-field font-16" type="text" name="remarks" placeholder="Remarks if any....">
                </div>
                <div class="flex flex-column gap-8">
                    <input id="add-task-draft" class="button-2 font-16" type="submit" value="Add" name="add">
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