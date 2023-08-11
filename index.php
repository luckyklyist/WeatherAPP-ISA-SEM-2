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

          <div class="flex flex-col items-center justify-center text-4xl font-bold text-gray-700 mt-8 mb-16">
              <div class="mb-4">
                  Weather App
              </div>
              <img src="https://uxwing.com/wp-content/themes/uxwing/download/weather/weather-icon.png" alt=""
                  class="h-16">
          </div>

          <div class="my-8 flex items-center justify-center ">
              <input type="text" placeholder="Enter city name" id="city-input"
                  class="p-3 w-64 border border-gray-300 rounded-l-lg focus:outline-none focus:border-blue-500 placeholder-gray-400">
              <button onclick="getWeather()"
                  class="px-6 py-3 bg-green-500 text-white font-bold rounded-lg ml-2 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 shadow-md">
                  Search
              </button>
          </div>

            <div class="bg-white p-8 rounded-lg shadow-lg bg-opacity-80">
            <div id="error"
                class="text-red-500 mb-4 rounded-lg bg-red-100 px-6 py-5 text-base text-danger-700 hidden"></div>

                <!-- Displaying weather data part -->
    <div class="weatherData">
        <?php
        function fetchWeatherData($apiKey, $city)
        {
            $apiUrl = "https://api.openweathermap.org/data/2.5/weather?q=${city}&units=metric&appid=${apiKey}";

            $curl = curl_init($apiUrl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $apiResponse = curl_exec($curl);

            if ($apiResponse === false) {
                echo "Error fetching weather data from the API: " . curl_error($curl);
                curl_close($curl);
                exit();
            }

            curl_close($curl);

            return json_decode($apiResponse, true);
        }

        function saveWeatherData($conn, $city, $weatherData)
        {
            $temperature = $weatherData['main']['temp'];
            $description = $weatherData['weather'][0]['description'];
            $currentDate = date('Y-m-d');
            $pressure = $weatherData['main']['pressure'];
            $windSpeed = $weatherData['wind']['speed'];
            $humidity = $weatherData['main']['humidity'];
            $icon = $weatherData['weather'][0]['icon'];

            // Check if data already exists for today
            $checkSql = "SELECT * FROM weather_data WHERE current_day_and_date = ?";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bind_param("s", $currentDate);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows === 0) {
                // Prepare the SQL statement for inserting new data
                $insertSql = "INSERT INTO weather_data (city, temperature, description, current_day_and_date, pressure, wind_speed, humidity, icon) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $insertStmt = $conn->prepare($insertSql);
                $insertStmt->bind_param("ssssssss", $city, $temperature, $description, $currentDate, $pressure, $windSpeed, $humidity, $icon);

                if ($insertStmt->execute()) {
                    echo "Today's weather data inserted successfully.";
                } else {
                    echo "Error: " . $insertSql . "<br>" . $conn->error;
                }

                $insertStmt->close();
            } else {
                echo "Weather data for today already exists.";
            }

            $checkStmt->close();
        }

        $apiKey = "23d7b189189528d5ade1c10729887b94";
        $city = "Aberdeen"; // Default city
        $conn = new mysqli("localhost", "root", "", "weather"); // Update with your credentials

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $weatherData = fetchWeatherData($apiKey, $city);

        if ($weatherData['cod'] === 200) {
          saveWeatherData($conn, $city, $weatherData);
          ?>
          <div id="location" class="text-4xl font-bold mb-2 text-gray-800">City: <?= $city ?></div>
          <div class="flex justify-between mb-6">
              <div id="temperature" class="text-6xl font-bold text-gray-900">
                  <?= $weatherData['main']['temp'] ?>°C
              </div>
              <div class="flex flex-col justify-end">
                  <div id="description" class="text-lg text-gray-600 mb-2">
                      Description: <?= $weatherData['weather'][0]['description'] ?>
                  </div>
                  <div id="current-day-and-date" class="text-lg text-gray-600"><?= date('Y-m-d') ?></div>
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
        } else {
            echo "Error fetching weather data from the API.";
        }

        $conn->close();
        ?>
    </div>

              <!-- Weather History -->
              <div id="weather-history" class="mt-8">
                  <h2 class="text-xl font-semibold mb-4">Weather History (Past 7 Days)</h2>
                  <div class="grid grid-cols-7 gap-4">
                      <?php
                      $servername = "localhost";
                      $username = "root"; // Your MySQL username
                      $password = "";     // Your MySQL password
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

