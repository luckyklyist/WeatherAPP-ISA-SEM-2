<!DOCTYPE html>
<html>

<head>
    <title>Weather App</title>
    <link rel="stylesheet" type="text/css" href="styles/style.css">
    <!-- Importing tailwind css  -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto py-20 text-center">
        <?php
        include './includes/header.php'
            ?>


        <div class="bg-white p-8 rounded-lg shadow-lg bg-opacity-80">
            <div id="error" class="text-red-500 mb-4 rounded-lg bg-red-100 px-6 py-5 text-base text-danger-700 hidden">
            </div>

            <!-- Displaying weather data part -->
            <div class="weatherData">
               <?php
               include './includes/db_connection.php';
               include './includes/displayWeatherData.php';

               $conn = connectToDatabase();
               ?>
            </div>

            <!-- Weather History -->
            <?php 
            include './includes/displayWeatherHistory.php'
            ?>
        </div>
    </div>
    <!-- <script src="scripts/index.js"></script> -->
</body>

</html>