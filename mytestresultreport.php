<?php
// ini_set('display_errors', 1);
include "connect.php";
session_start();
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

unset($_SESSION['mytestresultreport']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['view']) && $_POST['reporttype'] == 'Details') {
        $fromDate = $_POST['fromDate'];
        $toDate = $_POST['toDate'];
        // echo "<script>alert('Viewing My Test Result Report from $fromDate to $toDate');</script>";
        unset($_SESSION['mytestresultreport']);

        $_SESSION['mytestresultreport'] = [];

        if (isset($_POST['testno']) && !empty($_POST['testno'])) {
            $testno = $_POST['testno'];
            $testcomplete_sql = "SELECT * FROM testcomplete WHERE testno=$testno ORDER BY id DESC";
        } else {
            $testcomplete_sql = "SELECT * FROM testcomplete WHERE date BETWEEN '$fromDate 00:00:00' AND '$toDate 23:59:59' ORDER BY id DESC";
        }


        $testcomplete_result = mysqli_query($con, $testcomplete_sql);

        if (mysqli_num_rows($testcomplete_result) > 0) {
            // echo "<pre>";
            while ($testcomplete_data = mysqli_fetch_assoc($testcomplete_result)) {
                // print_r($testcomplete_data);

                if (!empty(json_decode($testcomplete_data['question'], true))) {
                    foreach (json_decode($testcomplete_data['question'], true) as $questiontable_data) {
                        // print_r($questiontable_data);
                        $usermobile = mysqli_query($con, "SELECT * FROM logintable WHERE id=$testcomplete_data[userid]");
                        $usermobile = mysqli_fetch_assoc($usermobile);

                        $_SESSION['mytestresultreport'][] = [
                            'date' => $testcomplete_data['date'],
                            'testno' => $testcomplete_data['testno'],
                            'mobile' => $usermobile['mobile'],
                            'name' => $testcomplete_data['name'],
                            // 'reporting' => $usermobile['reporting'],
                            'reporting' => $usermobile['reporting_head'],
                            'user_type' => $usermobile['user_type'],
                            'branch' => $usermobile['branch'],
                            'question' => $questiontable_data['question'],
                            'question_category' => $questiontable_data['category'],
                            'youranswer' => json_decode($testcomplete_data['answer'], true)[$questiontable_data['id']],
                            'rightanswer' => $questiontable_data['answer'],
                            'marks' => (json_decode($testcomplete_data['answer'], true)[$questiontable_data['id']] === $questiontable_data['answer']) ? '1' : '0'
                        ];
                    }
                }
            }

            // echo "<pre>";
            // print_r($_SESSION['mytestresultreport']);
            // die();
        } else {
            echo '<script>
            alert("Test Result Not Found.");
            window.location.href = "' . SITTE_URL . 'mytestresultreport.php";
            </script>';
            die();
        }
    }

    if (isset($_POST['view']) && $_POST['reporttype'] == 'Summary') {
        $fromDate = $_POST['fromDate'];
        $toDate = $_POST['toDate'];
        // echo "<script>alert('Viewing My Test Result Report from $fromDate to $toDate');</script>";
        unset($_SESSION['mytestresultreport']);

        $_SESSION['mytestresultreport'] = [];

        // $testcomplete_sql = "SELECT * FROM testcomplete WHERE date BETWEEN '$fromDate 00:00:00' AND '$toDate 23:59:59' ORDER BY id DESC";

        if (isset($_POST['testno']) && !empty($_POST['testno'])) {
            $testno = $_POST['testno'];
            $testcomplete_sql = "SELECT tc.userid,tc.question,tc.answer,tc.name,tc.testno,test.date FROM testcomplete as tc
        JOIN logintable as lt ON lt.id = tc.userid
        JOIN sys_param as test ON test.testno=tc.testno
        WHERE tc.testno=$testno ORDER BY test.date ASC,lt.branch ASC,tc.testno ASC,lt.reporting_head ASC,tc.name ASC";
        } else {
            $testcomplete_sql = "SELECT tc.userid,tc.question,tc.answer,tc.name,tc.testno,test.date FROM testcomplete as tc
        JOIN logintable as lt ON lt.id = tc.userid
        JOIN sys_param as test ON test.testno=tc.testno
        WHERE test.date BETWEEN '$fromDate 00:00:00' AND '$toDate 23:59:59' ORDER BY test.date ASC,lt.branch ASC,tc.testno ASC,lt.reporting_head ASC,tc.name ASC";
        }

        $testcomplete_result = mysqli_query($con, $testcomplete_sql);

        if (mysqli_num_rows($testcomplete_result) > 0) {
            // echo "<pre>";
            while ($testcomplete_data = mysqli_fetch_assoc($testcomplete_result)) {
                // print_r($testcomplete_data);
                if (!empty(json_decode($testcomplete_data['question'], true))) {
                    $total_mark = 0;
                    $total_recmark = 0;
                    $percentage = 0;
                    $student_result = 0;

                    $usermobile = mysqli_query($con, "SELECT * FROM logintable WHERE id=$testcomplete_data[userid]");
                    $usermobile = mysqli_fetch_assoc($usermobile);

                    foreach (json_decode($testcomplete_data['question'], true) as $questiontable_data) {
                        // print_r($questiontable_data);

                        $total_mark++;

                        if (json_decode($testcomplete_data['answer'], true)[$questiontable_data['id']] === $questiontable_data['answer']) {
                            $total_recmark++;
                        }
                    }

                    $percentage = ($total_recmark * 100) / $total_mark;

                    if ($percentage < 80) {
                        $student_result = "Fail";
                    }

                    if ($percentage >= 80) {
                        $student_result = "Pass";
                    }

                    $_SESSION['mytestresultreport'][] = [
                        'date' => $testcomplete_data['date'],
                        'testno' => $testcomplete_data['testno'],
                        'mobile' => $usermobile['mobile'],
                        'name' => $testcomplete_data['name'],
                        'reporting' => $usermobile['reporting_head'],
                        'user_type' => $usermobile['user_type'],
                        'branch' => $usermobile['branch'],
                        'total_mark' => $total_mark,
                        'total_recmark' => $total_recmark,
                        'percentage' =>  intval($percentage) . "%",
                        'result' =>  $student_result,
                    ];
                }
            }

            // echo "<pre>";
            // print_r($_SESSION['mytestresultreport']);
            // die();
        } else {
            echo '<script>
            alert("Test Result Not Found.");
            window.location.href = "' . SITTE_URL . 'mytestresultreport.php";
            </script>';
            die();
        }
    }

    if (isset($_POST['export']) && $_POST['reporttype'] == 'Details') {
        $fromDate = $_POST['fromDate'];
        $toDate = $_POST['toDate'];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Date');
        $sheet->setCellValue('B1', 'Branch');
        $sheet->setCellValue('C1', 'Test Number');
        $sheet->setCellValue('D1', 'Mobile Number');
        $sheet->setCellValue('E1', 'Name');
        // $sheet->setCellValue('F1', 'Employee Code');
        $sheet->setCellValue('F1', 'Reporting');
        $sheet->setCellValue('G1', 'Question');
        $sheet->setCellValue('H1', 'Question Category');
        $sheet->setCellValue('I1', 'Your Answer');
        $sheet->setCellValue('J1', 'Right Answer');
        $sheet->setCellValue('K1', 'Marks');

        if (isset($_POST['testno']) && !empty($_POST['testno'])) {
            $testno = $_POST['testno'];
            $testcomplete_sql = "SELECT * FROM testcomplete WHERE testno=$testno ORDER BY id DESC";
        } else {
            $testcomplete_sql = "SELECT * FROM testcomplete WHERE date BETWEEN '$fromDate 00:00:00' AND '$toDate 23:59:59'";
        }

        $testcomplete_result = mysqli_query($con, $testcomplete_sql);

        if (mysqli_num_rows($testcomplete_result) > 0) {
            $row = 2;
            // echo "<pre>";
            while ($testcomplete_data = mysqli_fetch_assoc($testcomplete_result)) {
                // print_r($testcomplete_data);

                if (!empty(json_decode($testcomplete_data['question'], true))) {
                    foreach (json_decode($testcomplete_data['question'], true) as $questiontable_data) {
                        // print_r($questiontable_data);

                        $usermobile = mysqli_query($con, "SELECT * FROM logintable WHERE id=$testcomplete_data[userid]");

                        $usermobile = mysqli_fetch_assoc($usermobile);

                        $sheet->setCellValue('A' . $row, $testcomplete_data['date']);
                        $sheet->setCellValue('B' . $row, $usermobile['branch']);
                        $sheet->setCellValue('C' . $row, $testcomplete_data['testno']);
                        $sheet->setCellValue('D' . $row, $usermobile['mobile']);
                        $sheet->setCellValue('E' . $row, $testcomplete_data['name']);
                        // $sheet->setCellValue('E' . $row, $usermobile['reporting']);
                        // $sheet->setCellValue('F' . $row, $usermobile['user_type']);
                        $sheet->setCellValue('F' . $row, $usermobile['reporting_head']);
                        $sheet->setCellValue('G' . $row, $questiontable_data['question']);
                        $sheet->setCellValue('H' . $row, $questiontable_data['category']);
                        $sheet->setCellValue('I' . $row, json_decode($testcomplete_data['answer'], true)[$questiontable_data['id']]);
                        $sheet->setCellValue('J' . $row, $questiontable_data['answer']);
                        $sheet->setCellValue('K' . $row, (json_decode($testcomplete_data['answer'], true)[$questiontable_data['id']] === $questiontable_data['answer']) ? '1' : '0');
                        $row++;
                    }
                }
            }

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="mytestresultreport.xlsx"');
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            // die();
        } else {
            echo "<script>
            alert('Test Result Report Not Found.');
            window.location.href='" . SITTE_URL . "mytestresultreport.php';
            </script>";
            // header('Location: mytestresultreport.php');
            exit;
        }
    }

    if (isset($_POST['export']) && $_POST['reporttype'] == 'Summary') {
        $fromDate = $_POST['fromDate'];
        $toDate = $_POST['toDate'];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Date');
        $sheet->setCellValue('B1', 'Branch');
        $sheet->setCellValue('C1', 'Test Number');
        // $sheet->setCellValue('D1', 'Mobile Number');
        $sheet->setCellValue('D1', 'Reporting Head');
        // $sheet->setCellValue('F1', 'Employee Code');
        $sheet->setCellValue('E1', 'Name');
        $sheet->setCellValue('F1', 'Marks');
        $sheet->setCellValue('G1', 'Received Marks');
        $sheet->setCellValue('H1', 'Percentage');
        $sheet->setCellValue('I1', 'Result');

        // $testcomplete_sql = "SELECT * FROM testcomplete WHERE date BETWEEN '$fromDate 00:00:00' AND '$toDate 23:59:59'";
        if (isset($_POST['testno']) && !empty($_POST['testno'])) {
            $testno = $_POST['testno'];
            $testcomplete_sql = "SELECT tc.userid,tc.question,tc.answer,tc.name,tc.testno,test.date FROM testcomplete as tc
        JOIN logintable as lt ON lt.id = tc.userid
        JOIN sys_param as test ON test.testno=tc.testno
        WHERE tc.testno=$testno ORDER BY test.date ASC,lt.branch ASC,tc.testno ASC,lt.reporting_head ASC,tc.name ASC";
        } else {
            $testcomplete_sql = "SELECT tc.userid,tc.question,tc.answer,tc.name,tc.testno,test.date FROM testcomplete as tc
        JOIN logintable as lt ON lt.id = tc.userid
        JOIN sys_param as test ON test.testno=tc.testno
        WHERE test.date BETWEEN '$fromDate 00:00:00' AND '$toDate 23:59:59' ORDER BY test.date ASC,lt.branch ASC,tc.testno ASC,lt.reporting_head ASC,tc.name ASC";
        }

        // echo $testcomplete_sql;
        // die;

        $testcomplete_result = mysqli_query($con, $testcomplete_sql);

        // echo  "<pre>";
        // while ($row = mysqli_fetch_assoc($testcomplete_result)) {
        //     print_r($row);
        // }
        // echo  "</pre>";
        // die;

        if (mysqli_num_rows($testcomplete_result) > 0) {
            $row = 2;
            // echo "<pre>";
            while ($testcomplete_data = mysqli_fetch_assoc($testcomplete_result)) {
                // print_r($testcomplete_data);

                if (!empty(json_decode($testcomplete_data['question'], true))) {
                    $total_mark = 0;
                    $total_recmark = 0;
                    $percentage = 0;
                    $student_result = 0;

                    $usermobile = mysqli_query($con, "SELECT * FROM logintable WHERE id=$testcomplete_data[userid]");

                    $usermobile = mysqli_fetch_assoc($usermobile);

                    foreach (json_decode($testcomplete_data['question'], true) as $questiontable_data) {
                        // print_r($questiontable_data);

                        $total_mark++;

                        if (json_decode($testcomplete_data['answer'], true)[$questiontable_data['id']] === $questiontable_data['answer']) {
                            $total_recmark++;
                        }
                    }



                    $percentage = ($total_recmark * 100) / $total_mark;

                    if ($percentage < 80) {
                        $student_result = "Fail";
                    }

                    if ($percentage >= 80) {
                        $student_result = "Pass";
                    }

                    $sheet->setCellValue('A' . $row, $testcomplete_data['date']);
                    $sheet->setCellValue('B' . $row, $usermobile['branch']);
                    $sheet->setCellValue('C' . $row, $testcomplete_data['testno']);
                    // $sheet->setCellValue('D' . $row, $usermobile['mobile']);
                    $sheet->setCellValue('D' . $row, $usermobile['reporting_head']);
                    // $sheet->setCellValue('E' . $row, $usermobile['reporting']);
                    // $sheet->setCellValue('F' . $row, $usermobile['user_type']);
                    $sheet->setCellValue('E' . $row, $testcomplete_data['name']);
                    $sheet->setCellValue('F' . $row, $total_mark);
                    $sheet->setCellValue('G' . $row, $total_recmark);
                    $sheet->setCellValue('H' . $row, intval($percentage) . "%");
                    $sheet->setCellValue('I' . $row, $student_result);
                    $row++;
                }
            }

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="mytestresultreport.xlsx"');
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            // die();
        } else {
            echo "<script>
            alert('Test Result Report Not Found.');
            window.location.href='" . SITTE_URL . "mytestresultreport.php';
            </script>";
            // header('Location: mytestresultreport.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Test Result Report</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.1.8/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.1.8/datatables.min.js"></script>
    <style>
        .content-body {
            font-family: Arial, sans-serif;
            /* display: flex; */
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
        }

        .container {
            /* max-width: 400px; */
            /* width: 100%; */
            background-color: #fff;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .title {
            font-size: 1.2em;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .form-group label {
            margin-bottom: 5px;
        }

        .form-group input {
            padding: 8px;
            font-size: 1em;
            width: 100%;
            max-width: 200px;
            box-sizing: border-box;
            text-align: center;
        }

        .btn {
            margin-top: 10px;
            padding: 8px 16px;
            font-size: 1em;
            cursor: pointer;
            border: none;
            background-color: rgb(20, 64, 105);
            color: #fff;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: rgb(20, 64, 105);
        }

        .note {
            font-size: 0.8em;
            color: #666;
            text-align: center;
        }

        input[type="date"],
        input[type="text"],
        input[type="number"],
        select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            width: 200px;
            box-sizing: border-box;
        }
    </style>
</head>

<body>

    <?php
    include "navbar.php";
    ?>

    <div class="content-body">
        <div class="container">
            <div class="title">Generate Test Result Report</div>
            <?php
            $sr = 0;
            if (isset($_SESSION['mytestresultreport']) && !empty($_SESSION['mytestresultreport'])) {

                if (isset($_POST['reporttype']) && $_POST['reporttype'] == 'Details') {
                    echo '<a href="' . SITTE_URL . 'mytestresultreport.php" class="btn">Back</a>';

                    echo "<table id='mytestresultreportfetchtable'>
                    <thead>
                        <tr>
                            <th>S.no</th>
                            <th>Date</th>
                            <th>Branch</th>
                            <th>Test Number</th>
                            <th>Mobile Number</th>
                            <th>Name</th>
                            <th>Employee Code</th>
                            <th>Reporting</th>
                            <th>Question</th>
                            <th>Question Category</th>
                            <th>Your Answer</th>
                            <th>Right Answer</th>
                            <th>Marks</th>
                        </tr>
                    </thead>
                    <tbody>";
                    foreach ($_SESSION['mytestresultreport'] as $data) {
                        $sr++;
                        echo "<tr>";
                        echo "<td>$sr</td>";
                        echo "<td>$data[date]</td>";
                        echo "<td>$data[branch]</td>";
                        echo "<td>$data[testno]</td>";
                        echo "<td>$data[mobile]</td>";
                        echo "<td>$data[name]</td>";
                        echo "<td>$data[user_type]</td>";
                        echo "<td>$data[reporting]</td>";
                        echo "<td>$data[question]</td>";
                        echo "<td>$data[question_category]</td>";
                        echo "<td>$data[youranswer]</td>";
                        echo "<td>$data[rightanswer]</td>";
                        echo "<td>$data[marks]</td>";
                        echo "</tr>";
                    }
                    echo "</tbody>
                    </table>";

                    echo "<script>
                    jQuery(document).ready(function($) {
                        $('#mytestresultreportfetchtable').DataTable();
                    });
                    </script>";
                }

                if (isset($_POST['reporttype']) && $_POST['reporttype'] == 'Summary') {
                    echo '<a href="' . SITTE_URL . 'mytestresultreport.php" class="btn">Back</a>';

                    echo "<table id='mytestresultreportfetchtable'>
                    <thead>
                        <tr>
                            <th>S.no</th>
                            <th>Date</th>
                            <th>Branch</th>
                            <th>Test Number</th>
                            <th>Reporting Head</th>
                            <!-- <th>Mobile Number</th> -->
                            <th>Name</th>
                            <!-- <th>Employee Code</th> -->
                            <th>Marks</th>
                            <th>Received Marks</th>
                            <th>Percentage</th>
                            <th>Result</th>
                        </tr>
                    </thead>
                    <tbody>";
                    foreach ($_SESSION['mytestresultreport'] as $data) {
                        $sr++;
                        echo "<tr>";
                        echo "<td>$sr</td>";
                        echo "<td>$data[date]</td>";
                        echo "<td>$data[branch]</td>";
                        echo "<td>$data[testno]</td>";
                        echo "<td>$data[reporting]</td>";
                        // echo "<td>$data[mobile]</td>";
                        echo "<td>$data[name]</td>";
                        // echo "<td>$data[user_type]</td>";
                        echo "<td>$data[total_mark]</td>";
                        echo "<td>$data[total_recmark]</td>";
                        echo "<td>$data[percentage]</td>";
                        echo "<td>$data[result]</td>";
                        echo "</tr>";
                    }
                    echo "</tbody>
                    </table>";

                    echo "<script>
                    jQuery(document).ready(function($) {
                        $('#mytestresultreportfetchtable').DataTable();
                    });
                    </script>";
                }
            } else { ?>
                <form method="post" style="display: flex;flex-direction: column;justify-content: center;">
                    <div class="form-group">
                        <label for="fromDate">From Date</label>
                        <input type="date" id="fromDate" name="fromDate" value="<?php echo Date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="toDate">To Date</label>
                        <input type="date" id="toDate" name="toDate" value="<?php echo Date('Y-m-d'); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="toDate">Test Number</label>
                        <input type="number" id="testno" name="testno">
                    </div>

                    <div class="form-group">
                        <label>Report Type</label>
                        <select name="reporttype">
                            <option value="Details">Details</option>
                            <option value="Summary">Summary</option>
                        </select>
                    </div>

                    <div style="padding: 0% 40%;display: flex;flex-direction: column;justify-content: center;">
                        <button type="submit" name="view" class="btn" style="display: flex;flex-direction: column;justify-content: center;align-items:center;">View</button>
                        <!-- <div class="note">By default, current date</div> -->
                        <button type="submit" name="export" class="btn" style="display: flex;flex-direction: column;justify-content: center;align-items:center;">Export</button>
                    </div>
                </form>
            <?php } ?>
        </div>
    </div>

</body>

</html>