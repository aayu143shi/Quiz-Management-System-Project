<?php

error_reporting(E_ALL);

ini_set('display_errors', 1);



include "connect.php";



session_start();



unset($_SESSION['countdown_end_time']);

unset($_SESSION['testdetails']);

unset($_SESSION['userselectedQuestion']);



if (isset($_POST['submit'])) {

    // $date = $_POST['date'];

    $date = date("Y-m-d H:i:s");

    $name = $_POST['name'];

    $mobile = $_POST['mobile'];

    $testno = $_POST['testno'];

    // $reporting = $_POST['reporting'];
    $reporting = "";


    //sukh 
    $select_branch = $_POST['select_branch'];
    $reporting_head = $_POST['reporting_head'];

    $user_type = ($_POST['user_type'] == "0") ? 'Fresher' : $_POST['emp_code'];


    $testalreadyattempt = mysqli_query($con, "SELECT * FROM logintable WHERE mobile=$mobile AND testno='$testno' AND branch='$select_branch'");

    if (mysqli_num_rows($testalreadyattempt) > 0) {
    ?>
        <script>
            alert('This test has already been attempted or is not enabled.');
            window.location.href = "<?php echo SITTE_URL ?>user.php";
        </script>
    <?php

        die();
    }

    // print_r($date);

    // die;



    // $test_exist = "SELECT * FROM sys_param WHERE testno='$testno' AND date='$date' AND status=1";



    $testvalidtime = "SELECT * FROM sys_param WHERE testno='$testno' AND status=1 AND branch='$select_branch'";

    if (mysqli_num_rows(mysqli_query($con, $testvalidtime)) > 0) {

        $testdata = mysqli_fetch_assoc(mysqli_query($con, $testvalidtime));
    } else {

    ?>
        <script>
            alert('Test No. is  Invalid. or Test is not enabled.');

            window.location.href = "<?php echo SITTE_URL ?>user.php";
        </script>

    <?php

        die();
    }



    // $testdata = mysqli_fetch_assoc(mysqli_query($con, $testvalidtime));



    // $start_time = new DateTime('2024-11-05 12:58:56'); 

    $start_time = new DateTime($testdata['date']);

    $duration = new DateInterval('PT' . $testdata['validtime'] . 'M');

    $start_time->add($duration);

    $end_time = $start_time->format('Y-m-d H:i:s');



    // echo $end_time;

    // die();



    // print_r($date);

    // echo "<pre>";

    // echo $end_time;

    // die();



    // print_r($date);

    // echo "<br>";

    // print_r($testdata['date']);

    // echo "<br>";

    // print_r($end_time);

    // echo "<br>";

    // die();



    // if ($date < $end_time) {


    if ($date >= $testdata['date']  && $date < $end_time) {


        // die('yes');

        $_SESSION['testdetails'] = $testdata;



        $sql = "INSERT INTO logintable (date, name, mobile, testno, reporting,branch,reporting_head,user_type) VALUES ('$date', '$name', '$mobile', '$testno', '$reporting','$select_branch','$reporting_head','$user_type')";

        // echo $sql;
        // die;

        $result = mysqli_query($con, $sql);

        $insert_id = mysqli_insert_id($con);

        if ($result) {
            header("Location: display.php?id=" . $insert_id);
            exit();
        } else {
            die("Error: " . mysqli_error($con));
        }
    } else {
    ?>
        <script>
            alert('Test No. is  Invalid. or Test is not enabled.');
            window.location.href = "<?php echo SITTE_URL ?>user.php";
        </script>

    <?php
        die();
    }
    //     $test_exist = "SELECT * FROM sys_param 

    //                WHERE testno='$testno' 

    //                AND date BETWEEN '$date' AND '$end_date' 

    //                AND status=1";





    //     $test_exist_result = mysqli_query($con, $test_exist);

    //     if (mysqli_num_rows($test_exist_result) > 0) {

    //         $test_exist_data = mysqli_fetch_assoc($test_exist_result);



    //         // echo "<pre>";

    //         // print_r($test_exist_data);

    //         // die();



    //         $_SESSION['testdetails'] = $test_exist_data;



    //         $sql = "INSERT INTO logintable (date, name, mobile, testno, reporting) VALUES ('$date', '$name', '$mobile', '$testno', '$reporting')";



    //         $result = mysqli_query($con, $sql);



    //         $insert_id = mysqli_insert_id($con);



    //         if ($result) {



    //             header("Location: display.php?id=" . $insert_id);

    //             exit();

    //         } else {



    //             die("Error: " . mysqli_error($con));

    //         }

    //     } else {

    //     

    ?>

    <!-- //         <script>

//             alert('Test No. is  Invalid. or Test is not enabled.');

//             window.location.href = "<?php echo SITTE_URL ?>user.php";

//         </script> -->

    // <?php

        //     }

    }



    $branches = mysqli_query($con, "SELECT id,branch FROM branches WHERE status=1 GROUP BY branch");
        ?>






<!DOCTYPE html>

<html lang="en">



<head>

    <link rel="shortcut icon" href="/exam_favicon.jpg" type="image/x-icon">

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Form</title>

    <style>
        body {

            font-family: Arial, sans-serif;

            display: flex;

            justify-content: center;

            align-items: center;

            /* height: 100vh; */

            margin: 0;

            background-color: f0f0f0;

        }



        .form-container {

            max-width: 500px;

            width: 90%;

            padding: 20px;

            border: 1px solid #ddd;

            border-radius: 8px;

            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);

        }



        .form-group {

            margin-bottom: 15px;

        }



        label {

            display: block;

            margin-bottom: 5px;

            font-weight: bold;

        }



        input[type="text"],

        input[type="date"],

        input[type="number"] {

            width: 100%;

            padding: 8px;

            border: 1px solid #ddd;

            border-radius: 4px;

        }



        .submit {

            width: 100%;

            padding: 10px;

            background-color: rgb(20, 64, 105);
            /* background-color: #rgb(20, 64, 105); */

            color: white;

            border: none;

            border-radius: 4px;

            cursor: pointer;

            font-size: 16px;

        }



        .submit:hover {

            background-color: rgb(20, 64, 105);

        }



        .label-info {

            font-size: 14px;

            color: #555;

        }

        input[type="date"],
        input[type="text"],
        input[type="number"],
        select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            width: 100%;
            box-sizing: border-box;
        }


        button {
            background-color: rgb(20, 64, 105);
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            margin-top: 15px;
            cursor: pointer;
        }

        button:hover {
            background-color: rgb(20, 64, 105);
        }
    </style>

</head>



<body>

    <div class="form-container">
        <div class="container">
            <div style="display:flex;justify-content:left;">
                <img src="/exam_logo.jpg" alt="Exam App Version 2.0" width="20%">
            </div>
            <h3 style="text-align: center;">Learn. Grow. Succeed — Start Your Journey with Today’s Test</h3>
        </div>

        <!-- action=" <?php //echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" -->

        <form method="POST" onsubmit="disableButton()">

            <div class="form-group">

                <label for="date">Date*</label>

                <input type="date" value="<?php echo date('Y-m-d'); ?>" disabled>

                <input type="hidden" id="date" name="date" value="<?php echo date('Y-m-d'); ?>">

            </div>


            <div class="form-group">
                <label for="select_branch">Branch*</label>
                <select name="select_branch" id="select_branch" required>
                    <option value="">Select Here</option>
                    <?php
                    while ($branch = mysqli_fetch_assoc($branches)) {
                        echo "<option value='$branch[branch]'>$branch[branch]</option>";
                        // echo "<option value='$branch[id]'>$branch[branch]</option>";
                    }
                    ?>
                </select>
            </div>

            <div>
                <div class="form-group">
                    <label for="reporting_head">Employee Type*</label>
                    <select name="user_type" id="user_type">
                        <option value="1">Employee</option>
                        <option value="0">Fresher</option>
                    </select>
                </div>

                <div id="employee_code_container">
                    <!-- style="display: none;" -->
                    <div style="display: flex;align-items:center;gap:5px;">
                        <div class="form-group" style="flex-grow: 1;">
                            <label for="emp_code">Employee Code*</label>
                            <input type="text" name="emp_code" id="emp_code">
                        </div>
                        <div class="form-group">
                            <label style="color:white;">Employee Code*</label>
                            <button type="button" style="margin: 0px;width: 100%;" class="btn btn-success" id="emp_details_fetch_btn">Fetch</button>
                        </div>
                    </div>
                </div>

            </div>

            <div class="form-group">
                <label for="name">Name*</label>
                <input type="text" id="name" name="name" required pattern="[A-Za-z\s]+" minlength="2" placeholder="Enter your name"
                    oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')" readonly>
            </div>

            <div class="form-group">
                <label for="reporting_head">Reporting Head*</label>
                <select name="reporting_head" id="reporting_head" required>
                    <option value="">Select Here</option>
                    <?php
                    $employee_data = mysqli_query($con, "SELECT emp_head FROM employees GROUP BY emp_head");

                    while ($employee = mysqli_fetch_assoc($employee_data)) {
                        echo "<option value='$employee[emp_head]'>$employee[emp_head]</option>";
                    }
                    ?>
                </select>
            </div>



            <div class="form-group">

                <label for="mobile">Mobile*</label>

                <input type="number" id="mobile" name="mobile" required pattern="[0-9]{10}" placeholder="Enter 10-digit mobile number" min="10">

            </div>



            <div class="form-group">

                <label for="testno">Test No*</label>

                <!-- <input type="text" id="testno" name="testno" required placeholder="Enter test number"> -->
                <input type="number" id="testno" name="testno" required placeholder="Enter test number">

            </div>



            <!-- <div class="form-group">

                <label for="reporting">Reporting*</label>

                <input type="text" id="reporting" name="reporting" required placeholder="Enter reporting info" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')">

            </div> -->



            <button type="submit" class="submit" name="submit" id="submitButton">Login</button>


            <!-- <p class="label-info">Timing: 45 min</p> -->

        </form>
        <h5 style="text-align: right;"> Exam App Version 2.0</h5>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        function disableButton() {
            document.getElementById("submitButton").style.display = "none";
            // document.getElementById("submitButton").disabled = true;
        }

        jQuery(document).ready(function($) {

            $("form").submit(function(e) {
                const namefield = $("#name").val();
                if (namefield == "") {
                    e.preventDefault();
                    alert("Please Enter Your Employee Code.");
                }
            })

            $("#user_type").change(function() {
                var type = $(this).val();
                // console.log(type);
                $("#name").val('');

                if (type == "1") {
                    $("#employee_code_container").show();
                    $("#name").attr("readonly", true);
                } else {
                    $("#employee_code_container").hide();
                    $("#name").attr("readonly", false);
                }

            })



            $("#emp_details_fetch_btn").click(function() {
                var emp_code = $("#emp_code").val();
                $.ajax({
                    url: "/ajax.php",
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        emp_code: emp_code,
                        action: "fetch_employee_details_by_code"
                    },
                    success: function(response) {
                        // console.log(response);
                        if (response.name != '') {
                            $("#name").val(response.name);
                            $("#name").attr("readonly", true);

                            $("#reporting_head").val(response.emp_head);
                        }

                        if (response.error) {
                            alert(response.msg);
                        }
                    }
                })
            });
        });
    </script>
</body>



</html>