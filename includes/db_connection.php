<?php
function connectToDatabase()
{
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "weather";

    echo "connecting to the db";

    $conn = new mysqli($servername, $username, $password, $database);


    if ($conn->connect_error) {
        echo "faield to the db";

        die("Connection failed: " . $conn->connect_error);
    }

    echo "connected to the db";

    return $conn;
}
?>