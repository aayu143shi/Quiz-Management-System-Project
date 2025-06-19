<?php
session_start();
?>

<!DOCTYPE html>

<html lang="en">



<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Test Report</title>

    <style>

        .content-body {

            font-family: Arial, sans-serif;

            display: flex;

            justify-content: center;

            align-items: center;

            height: 100vh;

            margin: 0;

            background-color: #f4f4f4;

        }



        .container {

            max-width: 400px;

            width: 100%;

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

            background-color: #007bff;

            color: #fff;

            border-radius: 5px;

            transition: background-color 0.3s;

        }



        .btn:hover {

            background-color: #0056b3;

        }



        .note {

            font-size: 0.8em;

            color: #666;

            text-align: center;

        }

    </style>

</head>



<body>

    <?php

    include "navbar.php";

    ?>

</body>



</html>