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
                } else {
                    echo "Error fetching weather data from the API.";
                }

                $conn->close();
                ?>