<?php
include 'fetchWeatherData.php';

function saveWeatherData($conn, $city, $weatherData)
{
    $temperature = $weatherData['main']['temp'];
    $description = $weatherData['weather'][0]['description'];
    $currentDate = date('Y-m-d');
    $pressure = $weatherData['main']['pressure'];
    $windSpeed = $weatherData['wind']['speed'];
    $humidity = $weatherData['main']['humidity'];

    // Check if data already exists for today
    $checkSql = "SELECT * FROM weather_data WHERE city = ? AND current_day_and_date = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("ss", $city, $currentDate);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows === 0) {
        // Prepare the SQL statement for inserting new data
        $insertSql = "INSERT INTO weather_data (city, temperature, description, current_day_and_date, pressure, wind_speed, humidity) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("sssssss", $city, $temperature, $description, $currentDate, $pressure, $windSpeed, $humidity);

        if ($insertStmt->execute()) {
            echo "Today's weather data inserted successfully.";
        } else {
            echo "Error: " . $insertSql . "<br>" . $conn->error;
        }

        $insertStmt->close();
    } else {
        // echo "Weather data for today already exists.";
    }

    $checkStmt->close();
    ?>
    <script>
        var weatherData = <?php echo json_encode($weatherData); ?>;
        var city = <?php echo json_encode($city); ?>;
        localStorage.setItem(city, JSON.stringify(weatherData));
    </script>
    <?php
}

$conn = new mysqli("localhost", "root", "", "weather"); // Update with your credentials
$apiKey = "23d7b189189528d5ade1c10729887b94";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['city'])) {
    $city = $_POST['city'];
} else {
    $city = "Aberdeen";
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$weatherData = fetchWeatherData($apiKey, $city);

if ($weatherData) {
    saveWeatherData($conn, $city, $weatherData);
    ?>
    <div id="location" class="text-4xl font-bold mb-2 text-gray-800">City:
        <?= $city ?>
    </div>
    <div class="flex justify-between mb-6">
        <div id="temperature" class="text-6xl font-bold text-gray-900">
            <?= $weatherData['main']['temp'] ?>°C
        </div>
        <div class="flex flex-col justify-end">
            <div id="description" class="text-lg text-gray-600 mb-2">
                Description:
                <?= $weatherData['weather'][0]['description'] ?>
            </div>
            <div id="current-day-and-date" class="text-lg text-gray-600">
                <?= date('Y-m-d') ?>
            </div>
        </div>
    </div>
    <table class="w-full">
        <tbody>
            <tr class="border-b">
                <td class="py-2 text-lg text-gray-600">Pressure:</td>
                <td id="pressure" class="py-2 text-lg text-gray-600">
                    <?= $weatherData['main']['pressure'] ?>MBar
                </td>
            </tr>
            <tr class="border-b">
                <td class="py-2 text-lg text-gray-600">Wind Speed:</td>
                <td id="windSpeed" class="py-2 text-lg text-gray-600">
                    <?= $weatherData['wind']['speed'] ?> m/s
                </td>
            </tr>
            <tr>
                <td class="py-2 text-lg text-gray-600">Humidity:</td>
                <td id="humidity" class="py-2 text-lg text-gray-600">
                    <?= $weatherData['main']['humidity'] ?>%
                </td>
            </tr>
        </tbody>
    </table>
    <?php
} else if (!$weatherData) {
    // Display error message based on the API response code
    echo "You are offline";
    ?>
        <html>
        <div id="location" class="text-4xl font-bold mb-2 text-gray-800">City:</div>
        <div class="flex justify-between mb-6">
            <div id="temperature" class="text-6xl font-bold text-gray-900">°C</div>
            <div class="flex flex-col justify-end">
                <div id="description" class="text-lg text-gray-600 mb-2">Description:</div>
                <div id="current-day-and-date" class="text-lg text-gray-600"></div>
            </div>
        </div>
        <table class="w-full">
            <tbody>
                <tr class="border-b">
                    <td class="py-2 text-lg text-gray-600">Pressure:</td>
                    <td id="pressure" class="py-2 text-lg text-gray-600">MBar</td>
                </tr>
                <tr class="border-b">
                    <td class="py-2 text-lg text-gray-600">Wind Speed:</td>
                    <td id="windSpeed" class="py-2 text-lg text-gray-600">m/s</td>
                </tr>
                <tr>
                    <td class="py-2 text-lg text-gray-600">Humidity:</td>
                    <td id="humidity" class="py-2 text-lg text-gray-600">%</td>
                </tr>
            </tbody>
        </table>

        <script>
            function displayWeatherData(data) {
                document.getElementById("location").textContent = "City: " + data.name;
                document.getElementById("temperature").textContent = data.main.temp + "°C";
                document.getElementById("description").textContent = "Description: " + data.weather[0].description;
                document.getElementById("current-day-and-date").textContent = "Date: " + new Date(data.dt * 1000).toLocaleDateString();
                document.getElementById("pressure").textContent = data.main.pressure + "MBar";
                document.getElementById("windSpeed").textContent = data.wind.speed + " m/s";
                document.getElementById("humidity").textContent = data.main.humidity + "%";
            }

            // Retrieve weather data from local storage
            var city = <?php echo json_encode($city); ?>;
            var storedWeatherData = localStorage.getItem(city);
            if (storedWeatherData) {
                var data = JSON.parse(storedWeatherData);
                displayWeatherData(data);
            } else {
                console.log("No weather data available in local storage.");
            }
        </script>

        </html>
    <?php
} else {
    echo "Error fetching weather data from the API.";
}

$conn->close();
?>