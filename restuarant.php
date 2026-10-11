<?php
// Database connection settings
$host = "localhost";
$user = "root";
$password = "";
$database = "RestaurantDatabase";

// Connect to MySQL
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

echo "<h1>Restaurant Database</h1>";
echo "<p>Database connection successful!</p>";

$sql = "SELECT TableID, TableNumber, Capacity, Location
        FROM RestaurantTable
        ORDER BY TableNumber";

$result = $conn->query($sql);

echo "<h2>Restaurant Tables</h2>";

if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='8'>";
    echo "<tr>
            <th>Table ID</th>
            <th>Table Number</th>
            <th>Capacity</th>
            <th>Location</th>
          </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row["TableID"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["TableNumber"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["Capacity"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["Location"]) . "</td>";
        echo "</tr>";
    }

    echo "</table>";
} elseif ($result) {
    echo "<p>No restaurant tables found.</p>";
} else {
    echo "<p>Query failed: "
        . htmlspecialchars($conn->error) . "</p>";
}

$conn->close();
?>