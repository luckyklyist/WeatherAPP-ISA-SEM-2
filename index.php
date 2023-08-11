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
               include './includes/displayWeatherData.php'
               ?>
            </div>

            <!-- Weather History -->
            <div id="weather-history" class="mt-8">
                <h2 class="text-xl font-semibold mb-4">Weather History (Past 7 Days)</h2>
                <div class="grid grid-cols-7 gap-4">
                    <?php
                    $servername = "localhost";
                    $username = "root"; // Your MySQL username
                    $password = ""; // Your MySQL password
                    $database = "weather"; // Your database name
                    
                    $conn = new mysqli($servername, $username, $password, $database);

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $sql = "SELECT * FROM weather_data ORDER BY current_day_and_date DESC LIMIT 7";
                    $result = $conn->query($sql);


                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="border p-4 rounded-lg bg-blue-100 text-center">';
                            echo '<div class="text-gray-600 mb-1">' . $row['current_day_and_date'] . '</div>';
                            echo '<div class="text-2xl text-blue-600">' . $row['temperature'] . '°C</div>';
                            echo '</div>';
                        }
                    }

                    $conn->close();
                    ?>
                </div>
            </div>
        </div>
    </div>
    <!-- <script src="scripts/index.js"></script> -->
</body>

</html>