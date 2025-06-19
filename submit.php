<?php
include "connect.php";

if (isset($_GET['id'])) {
    unset($_SESSION['testdetails']);
    unset($_SESSION['countdown_end_time']);

    $id = $_GET['id'];
    $user_sql = "SELECT * FROM logintable WHERE id=$id";
    $user_result = mysqli_query($con, $user_sql);

    if (mysqli_num_rows($user_result) > 0) {
        $user_data = mysqli_fetch_assoc($user_result);
        $test_id = $user_data['testno'];
        $userid = $user_data['id'];

        // $testcomplete_sql = "SELECT * FROM testcomplete WHERE testno=$test_id";
        $testcomplete_sql = "SELECT * FROM testcomplete WHERE testno=$test_id AND userid=$userid";
        $testcomplete_result = mysqli_query($con, $testcomplete_sql);

        if (mysqli_num_rows($testcomplete_result) > 0) {
            $testcomplete_data = mysqli_fetch_assoc($testcomplete_result);

            // $questiontable_sql = "SELECT * FROM questiontable WHERE id IN ($testcomplete_data[question])";
            // $questiontable_result = mysqli_query($con, $questiontable_sql);

            // echo "<pre>";
            // print_r(json_decode($testcomplete_data['question']));
            // die;

        }
    }
} else {
?>
    <script>
        window.location.href = "<?php echo SITTE_URL; ?>user.php";
    </script>
<?php
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="shortcut icon" href="/exam_favicon.jpg" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            text-align: center;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 400px;
            margin: 20px;
        }

        h1 {
            color: rgb(20, 64, 105);
        }

        p {
            color: #333;
            margin: 10px 0;
        }

        .home-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .home-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Thank You!</h1>
        <p>Mobile Number : <?php echo $user_data['mobile'] ?></p>
        <p>Test Number : <?php echo $user_data['testno'] ?></p>
        <p>Date : <?php echo $user_data['date'] ?></p>

        <!-- Commented out the question and answer display section -->
        <!-- <p>Questions</p>
        <div>
            <?php
            $sr = 0;
            $right = 0;
            $wrong = 0;

            $total_mark = 0;
            $total_recmark = 0;
            $percentage = 0;
            $student_result = 0;


            // while ($questiontable_data = mysqli_fetch_assoc($questiontable_result)) {
            foreach (json_decode($testcomplete_data['question'], true) as $questiontable_data) {
                $sr++;
            ?>
                <p style="<?php echo (json_decode($testcomplete_data['answer'], true)[$questiontable_data['id']] === $questiontable_data['answer']) ? 'color: green;' : 'color: red;'; ?>"><?php echo "Q" . $sr . ". " . $questiontable_data['question']; ?></p>
                <p><?php echo  "Your Answer : " . json_decode($testcomplete_data['answer'], true)[$questiontable_data['id']]; ?></p>
                <p><?php echo  "Right Answer : " . $questiontable_data['answer']; ?></p>

            <?php
                if (json_decode($testcomplete_data['answer'], true)[$questiontable_data['id']] === $questiontable_data['answer']) {
                    $right++;
                } else {
                    $wrong++;
                }


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

            ?>
        </div> -->

        <br><br>

        <?php

        // echo $wrong;
        // echo "<br>";
        // echo $right;

        // die();

        $testdetails_data = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM sys_param WHERE testno=$test_id"));
        $score = $testdetails_data['questionask'] - $wrong;

        $score_exist = "SELECT * FROM score WHERE user_id=$_GET[id] AND testno=$user_data[testno]";
        $score_existresult = mysqli_query($con, $score_exist);

        if (mysqli_num_rows($score_existresult) > 0) {
        } else {
            $scoreinsert = "INSERT INTO score (user_id, testno, score) VALUES ('$_GET[id]', '$user_data[testno]', '$score')";
            mysqli_query($con, $scoreinsert);
        }
        ?>

        <h3><strong>Score : <?php echo $score; ?></strong></h3>
        <!-- <h3><strong>Total Marks : <?php echo $total_mark; ?></strong></h3> -->
        <!-- <h3><strong>Received Marks : <?php echo $total_recmark; ?></strong></h3> -->
        <!-- <h3><strong>Percentage : <?php echo $percentage; ?>%</strong></h3> -->
        <h3><strong>Result : <?php echo $student_result; ?></strong></h3>
        <br>
        <p><b>Your record has been successfully submitted.</b></p>
        <a href="<?php echo SITTE_URL; ?>user.php" class="home-button">Go to Home</a>
    </div>
</body>

</html>