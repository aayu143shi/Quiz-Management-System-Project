<?php

require 'connect.php';

if (isset($_POST['action'])) {
    $action = $_POST['action'];

    echo $action();
} else {
    echo http_response_code(403);
    die("403 Forbidden");
}



function generate_test_number()
{
    global $con;

    do {
        $test_number = rand(1000, 9999);
        $checktestno = mysqli_query($con, "SELECT * FROM sys_param WHERE testno = $test_number");
    } while (mysqli_num_rows($checktestno) > 0);

    return $test_number;
}



function fetch_employee_details_by_code()
{
    global $con;
    $emp_code = $_POST['emp_code'];
    $employee_data = mysqli_query($con, "SELECT * FROM employees WHERE emp_code='$emp_code' AND status=1");

    $dataarray = array();
    if (mysqli_num_rows($employee_data) > 0) {
        $data = mysqli_fetch_assoc($employee_data);

        array_push($data,['status' => 200]);

        return json_encode($data);
    }

    return json_encode(['name' => '', 'msg' => 'Employee Code is invalid.', 'error' => true]);
}
