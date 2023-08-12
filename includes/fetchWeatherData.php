<?php
function fetchWeatherData($apiKey, $city)
{
    $apiUrl = "https://api.openweathermap.org/data/2.5/weather?q={$city}&units=metric&appid={$apiKey}";

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
?>
