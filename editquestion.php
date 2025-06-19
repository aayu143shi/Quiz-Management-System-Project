<?php
include "connect.php";
session_start();

if (!isset($_SESSION['admin_name'])) {
    header("Location: adminlogin.php");
    exit();
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    $question_sql = "SELECT * FROM questiontable WHERE id=$id";

    $exc = mysqli_query($con,$question_sql);

    if(mysqli_num_rows($exc) > 0){
        $question_data = mysqli_fetch_assoc($exc);

        // print_r($question_data);
        // die;

    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question = $_POST['question'];
    $option1 = $_POST['option1'];
    $option2 = $_POST['option2'];
    $option3 = $_POST['option3'];
    $option4 = $_POST['option4'];
    // $correct_option = $_POST['correct_option'];
    $correct_option = $_POST['option' . $_POST['correct_option']];

    $stmt = $con->prepare("UPDATE questiontable SET question = ?, optionA = ?, optionB = ?, optionC = ?, optionD = ?, answer = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $question, $option1, $option2, $option3, $option4, $correct_option, $id);

    if ($stmt->execute()) {
        echo "<p>Question updated successfully!</p>";
        header('location: admin_dashboard.php');
        exit;
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

$con->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Question</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f0f4f8;
        }

        .form-container {
            width: 400px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #6a0dad;
        }

        label {
            display: block;
            margin: 10px 0 5px;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #6a0dad;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #590bb1;
        }
    </style>
</head>

<body>

    <div class="form-container">
        <h2>Edit a Question</h2>
        <form action="editquestion.php?id=<?php echo $id ?>" method="POST">
            <label for="question">Question Text:</label>
            <input type="text" id="question" name="question" value="<?php echo $question_data['question'] ?>" required>

            <label for="option1">Option 1:</label>
            <input type="text" id="option1" name="option1" value="<?php echo $question_data['optionA'] ?>" required>

            <label for="option2">Option 2:</label>
            <input type="text" id="option2" name="option2" value="<?php echo $question_data['optionB'] ?>" required>

            <label for="option3">Option 3:</label>
            <input type="text" id="option3" name="option3" value="<?php echo $question_data['optionC'] ?>" required>

            <label for="option4">Option 4:</label>
            <input type="text" id="option4" name="option4" value="<?php echo $question_data['optionD'] ?>" required>

            <label for="correct_option">Correct Option:</label>
            <select id="correct_option" name="correct_option" required>
                <option value="1" <?php echo ($question_data['optionA'] === $question_data['answer']) ? 'selected' : '' ; ?>><?php echo $question_data['optionA'] ?></option>
                <option value="2" <?php echo ($question_data['optionB'] === $question_data['answer']) ? 'selected' : '' ; ?>><?php echo $question_data['optionB'] ?></option>
                <option value="3" <?php echo ($question_data['optionC'] === $question_data['answer']) ? 'selected' : '' ; ?>><?php echo $question_data['optionC'] ?></option>
                <option value="4" <?php echo ($question_data['optionD'] === $question_data['answer']) ? 'selected' : '' ; ?>><?php echo $question_data['optionD'] ?></option>
            </select>
            <!-- <input type="text" id="correct_option" name="correct_option" value="<?php echo $question_data['answer'] ?>" required> -->

            <button type="submit">Update Question</button>
        </form>
    </div>



</body>

</html>