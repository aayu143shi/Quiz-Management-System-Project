<?php

ini_set('display_errors', 1);
session_start();

include "connect.php";


$logintableResult = mysqli_query($con, "SELECT * FROM logintable");

$current_timedate = date("Y-m-d H:i:s");

if ($logintableResult === false) {
    die("Error in logintable query: " . mysqli_error($con));
}

if (mysqli_num_rows($logintableResult) > 0) {

    while ($logindata = mysqli_fetch_assoc($logintableResult)) {

        $testendtime = mysqli_query($con, "SELECT * FROM sys_param WHERE testno='$logindata[testno]' AND status=1");

        if (mysqli_num_rows($testendtime) > 0) {

            $testendtime = mysqli_fetch_assoc($testendtime);

            $start_time = new DateTime($testendtime['date']);
            $duration = new DateInterval('PT' . $testendtime['validtime'] . 'M');
            $start_time->add($duration);
            $end_time = $start_time->format('Y-m-d H:i:s');

            if ($current_timedate >= $testendtime['date']  && $current_timedate < $end_time) {
            } else {

                $testcomplteresult = mysqli_query($con, "SELECT * FROM testcomplete WHERE userid='$logindata[id]' AND testno='$logindata[testno]' ");

                if ($testcomplteresult === false) {
                    die("Error in testcomplete query: " . mysqli_error($con));
                }

                if (mysqli_num_rows($testcomplteresult) > 0) {
                } else {
                    mysqli_query($con, "DELETE  FROM logintable WHERE id=$logindata[id]");
                }
            }
        }
    }
}


// Auto Disable Test

$testEnableResult = mysqli_query($con, "SELECT * FROM sys_param WHERE status=1");

if (mysqli_num_rows($testEnableResult) > 0) {

    while ($testEnableData = mysqli_fetch_assoc($testEnableResult)) {
        $date = date("Y-m-d H:i:s");
        $start_time = new DateTime($testEnableData['date']);
        $duration = new DateInterval('PT' . $testEnableData['validtime'] . 'M');
        $start_time->add($duration);
        $end_time = $start_time->format('Y-m-d H:i:s');

        if ($date >= $testEnableData['date']  && $date < $end_time) {
        } else {
            $testupdate_query = "UPDATE sys_param SET status=0 WHERE testno=$testEnableData[testno]";
            mysqli_query($con, $testupdate_query);
        }
    }
}
