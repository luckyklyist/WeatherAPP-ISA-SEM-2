<!DOCTYPE html>
<html data-theme="light">

<head>
    <title>Weather App</title>
    <link rel="stylesheet" type="text/css" href="styles/style.css">
    <!-- Importing tailwind css  -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/daisyui@3.5.1/dist/full.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <div class="container mx-auto py-20 text-center">
        <?php
        include './includes/header.php';
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
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