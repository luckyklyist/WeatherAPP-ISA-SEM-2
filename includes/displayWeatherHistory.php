<div id="weather-history" class="mt-8">
    <h2 class="text-xl font-semibold mb-4">Weather History (Past 7 Days)</h2>
    <div class="grid grid-cols-7 gap-4">
        <?php

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['city'])) {
            $city = $_POST['city'];
        } else {
            $city = "Aberdeen";
        }
        $sql = "SELECT * FROM weather_data WHERE city = ? ORDER BY current_day_and_date DESC LIMIT 7";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $city);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="border p-4 rounded-lg bg-blue-100 text-center">';
                echo '<div class="text-gray-600 mb-1">' . $row['current_day_and_date'] . '</div>';
                echo '<div class="text-2xl text-blue-600">' . $row['temperature'] . '°C</div>';
                echo '<div class="text-sm text-blue-600">' . $row['description'] . '</div>';
                echo '</div>';
            }
        } else {
            echo "No weather data available.";
        }

        $stmt->close();
        $conn->close();
        ?>
    </div>
</div>