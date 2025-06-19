<?php



ini_set('display_errors', 1);



session_start();

include "connect.php";



if (isset($_GET['id'])) {

    $id = $_GET['id'];

    $logintable_sql = "SELECT  * FROM logintable WHERE id=" . $id;



    $result = mysqli_query($con, query: $logintable_sql);



    if (mysqli_num_rows($result) > 0) {



        $data = mysqli_fetch_assoc($result);



        // echo "<pre>";

        // print_r($data);

        // die();

    }
} else {

    header("Location : user.php");

    exit;
}

// echo "<pre>";

// print_r($_POST);

// die();







$_SESSION['countdown_end_time'] = strtotime($_SESSION['testdetails']['date']) + ($_SESSION['testdetails']['validtime'] * 60);





// echo "<pre>";

// print_r($_SESSION['testdetails']);

// print_r($_SESSION['testdetails']['validtime']);

// echo "<br>";

// print_r(date('d M Y H:i:s',$_SESSION['countdown_end_time']));

// die();



if (isset($_POST['submit'])) {

    $name = $_POST['name'];

    $testno = $_POST['testno'];

    $date = date("Y-m-d H:i:s");

    // $date = $_POST['date'];

    $questions = isset($_POST['question']) ? implode(",", $_POST['question']) : null;

    // $answers = isset($_POST['question']) ? implode(",", $_POST['question']) : null;



    $check_test_disable = mysqli_query($con, 'SELECT * FROM sys_param WHERE testno=' . $data['testno'] . ' AND status=0');



    if (mysqli_num_rows($check_test_disable) > 0) {

        mysqli_query($con, "DELETE  * FROM logintable WHERE id=" . $_GET['id']);



?>

        <script>
            alert('This Test Has Disabled.');

            window.location.href = "<?php echo SITTE_URL; ?>user.php";
        </script>

    <?php

        die();
    }





    foreach ($_POST['question'] as $id) {

        if (!isset($_POST['answer'][$id])) {

            $_POST['answer'][$id] = "Not Attempt";
        }
    }



    $answers = isset($_POST['answer']) ? json_encode($_POST['answer']) : null;



    $attemptquestiondata = [];

    foreach ($_POST['question'] as $questionid) {

        $question = mysqli_query($con, "SELECT * FROM questiontable WHERE id=$questionid");



        if (mysqli_num_rows($question) > 0) {

            $questiondata = mysqli_fetch_assoc($question);

            $attemptquestiondata[] = $questiondata;

            // echo "<pre>";

            // print_r($questiondata);

            // die();

        }
    }

    $_POST['question'] = $attemptquestiondata;



    $questions = isset($_POST['question']) ? json_encode($_POST['question']) : null;



    // echo "<pre>";

    // print_r($_POST['question']);

    // echo "<br>";

    // print_r($_POST['answer']);

    // print_r($questions);

    // print_r($answers);

    // echo "<br>";

    // die();



    // $answer1 = isset($_POST['q_1']) ? implode(",", $_POST['q_1']) : null;

    // $answer2 = isset($_POST['q2']) ? implode(",", $_POST['q2']) : null;



    $userid = $_GET['id'];



    $sql = "INSERT INTO testcomplete(userid,name, testno, date, question, answer) VALUES(?,?, ?, ?, ?, ?)";

    $stmt = $con->prepare($sql);

    $stmt->bind_param("isssss", $userid, $name, $testno, $date, $questions, $answers);

    $stmt->execute();



    if ($stmt->affected_rows > 0) {

        echo "Data inserted successfully!";



    ?>

        <script>
            window.location.href = "<?php echo SITTE_URL;  ?>submit.php?id=<?php echo $_GET['id']; ?>";
        </script>

<?php



        exit();
    } else {

        echo "Error: " . $stmt->error;
    }



    $stmt->close();

    $con->close();
}



// if (time() >= $_SESSION['countdown_end_time']) {

//     header("Location: submit.php?id=$_GET[id]");

//     exit();

// }

?>



<!DOCTYPE html>

<html lang="en">



<head>
    <link rel="shortcut icon" href="/exam_favicon.jpg" type="image/x-icon">
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Test Form with Countdown</title>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        * {

            box-sizing: border-box;

        }



        body {

            font-family: Arial, sans-serif;

            display: flex;

            justify-content: center;

            align-items: center;

            /* height: 100vh; */

            margin: 0;

            background-color: #f4f4f4;

        }



        .form-container {

            width: 500px;

            border: 1px solid #000;

            padding: 20px;

            background-color: #fff;

        }



        .header,

        .countdown {

            display: flex;

            justify-content: space-between;

            margin-bottom: 20px;

        }



        .input-group {

            display: flex;

            flex-direction: column;

            width: 30%;

        }



        .input-group label {

            font-weight: bold;

            margin-bottom: 5px;

        }



        .input-group input {

            padding: 5px;

            border: 1px solid #000;

            width: 100%;

        }



        .question {

            border-top: 1px solid #000;

            padding-top: 10px;

            margin-top: 10px;

        }



        .options label {

            display: flex;

            align-items: center;

        }



        .submit {

            width: 100%;

            padding: 10px;

            background-color: rgb(20, 64, 105);

            color: white;

            border: none;

            border-radius: 4px;

            cursor: pointer;

            font-size: 16px;

        }



        .submit:hover {

            background-color: rgb(20, 64, 105);

        }
    </style>

</head>



<body>

    <div class="form-container">

        <!-- <div class="countdown" id="countdown">Time Left: <span id="timer"><?php echo $_SESSION['testdetails']['validtime']; ?>:00</span></div> -->



        <form method="post" action="display.php?id=<?php echo $_GET['id']; ?>">

            <div style="position: sticky;top: 0;background: white;">

                <div class="countdown" id="countdown">Time Left: <span id="timer"><?php echo $_SESSION['testdetails']['validtime']; ?>:00</span></div>

                <div class="header">

                    <div class="input-group">

                        <label for="name">Name:</label>

                        <input type="text" value="<?php echo (isset($data['name'])) ? $data['name'] : '';

                                                    ?>" disabled>

                        <input type="hidden" value="<?php echo (isset($data['name'])) ? $data['name'] : ''; ?>" name="name">

                    </div>

                    <div class="input-group">

                        <label for="testno">Test No:</label>

                        <input type="text" value="<?php echo (isset($data['testno'])) ? $data['testno'] : '';

                                                    ?>" disabled>

                        <input type="hidden" value="<?php echo (isset($data['testno'])) ? $data['testno'] : ''; ?>" name="testno">

                    </div>

                    <div class="input-group">

                        <label for="date">Date:</label>

                        <input type="text" value="<?php echo (isset($data['date'])) ? $data['date'] : '';

                                                    ?>" disabled>

                        <input type="hidden" value="<?php echo (isset($data['date'])) ? $data['date'] : ''; ?>" name="date">

                    </div>

                    <div class="input-group">

                        <!-- <p>Q=<?php echo intval($_SESSION['testdetails']['questionask']); ?></p> -->

                        <label>Questions:</label>

                        <input type="text" value="<?php echo intval($_SESSION['testdetails']['questionask']); ?>" disabled>

                    </div>

                </div>

            </div>



            <div class="question">

                <?php

                $qcount = 0;
                // unset($_SESSION['userselectedQuestion']); // SUKH TEsting 
                if (!isset($_SESSION['userselectedQuestion']) || empty($_SESSION['userselectedQuestion'])) {

                    unset($_SESSION['userselectedQuestion']);

                    $limit = intval($_SESSION['testdetails']['questionask']);

                    // $questiontable_sql = "SELECT * FROM questiontable ORDER BY RAND() LIMIT $limit";

                    // $questiontable_sql = "SELECT DISTINCT * FROM questiontable  ORDER BY RAND() LIMIT $limit";

                    $question_categories = unserialize($_SESSION['testdetails']['categories']);

                    $question_categories = array_map(function ($cat) use ($con) {
                        return "'" . $cat . "'";
                    }, $question_categories);

                    $question_categories = implode(',', $question_categories);

                    $questiontable_sql = "SELECT DISTINCT * FROM questiontable WHERE category IN ($question_categories) ORDER BY RAND() LIMIT $limit";

                    // echo $questiontable_sql;
                    // die;

                    $result = mysqli_query($con, $questiontable_sql);

                    $_SESSION['userselectedQuestion'] = [];

                    if (mysqli_num_rows($result) > 0) {

                        while ($data = mysqli_fetch_assoc($result)) {
                            $_SESSION['userselectedQuestion'][] = $data;
                        }
                    }


                    // echo "<pre>";
                    // print_r(count($_SESSION['userselectedQuestion']));
                    // print_r($_SESSION['userselectedQuestion']);
                    // echo "</pre>";
                    // die;
                }



                if (isset($_SESSION['userselectedQuestion']) && !empty($_SESSION['userselectedQuestion'])) {

                    foreach ($_SESSION['userselectedQuestion'] as $data) {

                        $qcount++;

                ?>

                        <input type="hidden" name="question[]" value="<?php echo $data['id']; ?>">

                        <p><strong><?php echo "Q" . $qcount . ". " . $data['question']; ?></strong></p>

                        <div class="options">

                            <label><input type="radio" name="answer[<?php echo $data['id']; ?>]" value="<?php echo $data['optionA']; ?>"><?php echo $data['optionA']; ?></label><br>

                            <label><input type="radio" name="answer[<?php echo $data['id']; ?>]" value="<?php echo $data['optionB']; ?>"><?php echo $data['optionB']; ?></label><br>

                            <label><input type="radio" name="answer[<?php echo $data['id']; ?>]" value="<?php echo $data['optionC']; ?>"><?php echo $data['optionC']; ?></label><br>

                            <label><input type="radio" name="answer[<?php echo $data['id']; ?>]" value="<?php echo $data['optionD']; ?>"><?php echo $data['optionD']; ?></label>

                        </div>

                <?php

                    }
                }

                ?>



            </div>



            <!-- <div class="question">

                <p><strong>Q1. What is your name?</strong></p>

                <div class="options">

                    <label><input type="checkbox" name="q1[]" value="a"> (a) Sushant</label><br>

                    <label><input type="checkbox" name="q1[]" value="b"> (b) Rohit</label><br>

                    <label><input type="checkbox" name="q1[]" value="c"> (c) Sagar</label><br>

                    <label><input type="checkbox" name="q1[]" value="d"> (d) Lokesh</label>

                </div>



                <p><strong>Q2. What is your name?</strong></p>

                <div class="options">

                    <label><input type="checkbox" name="q2[]" value="a"> (a) Sushant</label><br>

                    <label><input type="checkbox" name="q2[]" value="b"> (b) Rohit</label><br>

                    <label><input type="checkbox" name="q2[]" value="c"> (c) Sagar</label><br>

                    <label><input type="checkbox" name="q2[]" value="d"> (d) Lokesh</label>

                </div>

            </div> -->



            <br>

            <button type="submit" class="submit" name="submit">Submit</button>

        </form>

    </div>



    <script>
        let countdownEndTime = <?php echo $_SESSION['countdown_end_time'] * 1000; ?>;



        // console.log(countdownEndTime);



        function updateCountdown() {

            let now = new Date().getTime();

            let distance = countdownEndTime - now;



            if (distance <= 0) {

                // console.log('fgf');

                $("button[name='submit']").click();

                // console.log(distance);

                // window.location.href = "<?php echo SITTE_URL; ?>submit.php?id=<?php echo $_GET['id']; ?>";

            } else {

                let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

                let seconds = Math.floor((distance % (1000 * 60)) / 1000);

                document.getElementById("timer").innerHTML =

                    (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;

            }

        }



        setInterval(updateCountdown, 1000);
    </script>

</body>



</html>