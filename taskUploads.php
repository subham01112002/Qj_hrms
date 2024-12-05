<?php
    session_start();
    include("./loginCheck.php");
    include("./connection/connection.php");
    $empID = $_SESSION['employee'];
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
            <div class="my-32 px-2vw">
                <div class="font-32">
                    Your Works
                </div>
                <div class="font-16 color-2" style="color: grey;">View your uploaded tasks here</div>
            </div>
            <div class="flex flex-column gap-8 my-32 px-2vw font-22">
                Filters
                <div class="flex justify-between font-16">
                    <div class="flex gap-8">
                        <div class="flex flex-column">
                            Filter By
                            <select id="primary-filter" class="input-field" onchange="getSecondaryFilters()">
                                <option selected value="0">None</option>
                                <option value="1">Projects</option>
                                <option value="2">Tasks</option>
                            </select>
                        </div>
                        <div class="flex flex-column">
                            For
                            <select id="secondary-filter" class="input-field" onchange="getData()">
                                <option selected value="0">None</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-8">
                        <div class="flex flex-column">
                            Month
                            <select id="in-month" class="input-field" name="in-month" onchange="getData()">
                                <?php
                                    $currMonth = date("m");
                                    $monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                                    for($i=0; $i<12; $i++)
                                    {
                                ?>
                                        <option value="<?php echo $i+1; ?>" <?php echo (($i+1) == $currMonth)? "selected" : ""; ?>><?php echo $monthNames[$i]; ?></option>
                                <?php
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="flex flex-column">
                            Year
                            <select id="in-year" class="input-field" name="in-year" onchange="getData()">
                                <option value="2024" selected>2024</option>
                                <option value="2025">2025</option>
                                <option value="2026">2026</option>
                                <option value="2027">2027</option>
                                <option value="2028">2028</option>
                                <option value="2029">2029</option>
                                <option value="2030">2030</option>
                                <option value="2031">2031</option>
                                <option value="2032">2032</option>
                                <option value="2033">2033</option>
                                <option value="2034">2034</option>
                            </select>
                        </div>
                        <div class="flex flex-column">
                            Order
                            <select id="sorting-order" class="input-field" onchange="getData()">
                                <option value="0">Ascending</option>
                                <option value="1">Descending</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="my-32 px-2vw overflow-x-scroll">
                <table class="w100-percent">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th style="text-wrap: nowrap;">Project Name</th>
                            <th>Worked On</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Total Time Worked</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                    </tbody>
                </table>
            </div>
        </div>
        <script src="./js/script.js"></script>
        <script src="./js/eapi.js"></script>
    </body>
</html>