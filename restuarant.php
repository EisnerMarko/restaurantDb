<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "RestaurantDatabase";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

$customers = [];
$customerResult = $conn->query(
    "SELECT CustomerID, FirstName, LastName FROM Customer ORDER BY CustomerID"
);

if (!$customerResult) {
    die("Customer query failed: " . htmlspecialchars($conn->error));
}

while ($row = $customerResult->fetch_assoc()) {
    $customers[] = $row;
}

$bookings = [];
$bookingOptionsResult = $conn->query(
    "SELECT b.BookingID, b.BookingDateTime, c.FirstName, c.LastName
     FROM Booking AS b
     JOIN Customer AS c ON b.CustomerID = c.CustomerID
     ORDER BY b.BookingDateTime, b.BookingID"
);

if (!$bookingOptionsResult) {
    die("Booking query failed: " . htmlspecialchars($conn->error));
}

while ($row = $bookingOptionsResult->fetch_assoc()) {
    $bookings[] = $row;
}

$customerID = null;
$requestedCustomerID = filter_input(INPUT_GET, "customer_id", FILTER_VALIDATE_INT);

foreach ($customers as $customer) {
    if ((int) $customer["CustomerID"] === $requestedCustomerID) {
        $customerID = (int) $customer["CustomerID"];
        break;
    }
}

if ($customerID === null && count($customers) > 0) {
    $customerID = (int) $customers[0]["CustomerID"];
}

$bookingID = null;
$requestedBookingID = filter_input(INPUT_GET, "booking_id", FILTER_VALIDATE_INT);

foreach ($bookings as $booking) {
    if ((int) $booking["BookingID"] === $requestedBookingID) {
        $bookingID = (int) $booking["BookingID"];
        break;
    }
}

if ($bookingID === null && count($bookings) > 0) {
    $bookingID = (int) $bookings[0]["BookingID"];
}

echo "<!DOCTYPE html>";
echo "<html lang='en'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Restaurant Database</title>";

echo "<style>
    body {
        font-family: Arial, sans-serif;
        margin: 30px;
        background: #f4f4f4;
        color: #333;
    }

    h1, h2 {
        color: #263746;
    }

    table {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 35px;
        background: white;
    }

    th, td {
        border: 1px solid #ccc;
        padding: 10px;
        text-align: left;
    }

    th {
        background: #263746;
        color: white;
    }

    tr:nth-child(even) {
        background: #f2f2f2;
    }

    .success {
        color: green;
        font-weight: bold;
    }

    .info {
        margin-bottom: 25px;
    }
</style>";

echo "</head>";
echo "<body>";

echo "<h1>Restaurant Database</h1>";
echo "<p class='success'>Database connection successful!</p>";


// QUERY 1: ALL RESTAURANT TABLES


echo "<h2>1. All Restaurant Tables</h2>";

$sql1 = "SELECT
            TableID,
            TableNumber,
            Capacity,
            Location
         FROM RestaurantTable
         ORDER BY TableNumber";

$result1 = $conn->query($sql1);

if ($result1 && $result1->num_rows > 0) {

    echo "<table>";
    echo "<tr>
            <th>Table ID</th>
            <th>Table Number</th>
            <th>Capacity</th>
            <th>Location</th>
          </tr>";

    while ($row = $result1->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row["TableID"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["TableNumber"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["Capacity"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["Location"]) . "</td>";
        echo "</tr>";
    }

    echo "</table>";

} elseif ($result1) {
    echo "<p>No restaurant tables found.</p>";
} else {
    echo "<p>Query error: "
        . htmlspecialchars($conn->error) . "</p>";
}


// QUERY 2: BOOKINGS FOR A SELECTED CUSTOMER

echo "<h2>2. Bookings for a Customer</h2>";
echo "<form method='get'>";
echo "<label for='customer_id'>Customer ID:</label> ";
echo "<select name='customer_id' id='customer_id' required"
    . (count($customers) === 0 ? " disabled" : "") . ">";

foreach ($customers as $customer) {
    $id = (int) $customer["CustomerID"];
    $name = $customer["FirstName"] . " " . $customer["LastName"];
    $selected = $id === $customerID ? " selected" : "";
    echo "<option value='" . $id . "'" . $selected . ">"
        . htmlspecialchars($id . " - " . $name) . "</option>";
}

if (count($customers) === 0) {
    echo "<option value=''>No customers available</option>";
}

echo "</select> ";
if ($bookingID !== null) {
    echo "<input type='hidden' name='booking_id' value='" . $bookingID . "'>";
}
echo "<button type='submit'>Show bookings</button>";
echo "</form>";

$sql2 = "SELECT
            b.BookingID,
            b.BookingDateTime,
            b.NumberOfGuests,
            rt.TableNumber,
            rt.Location
         FROM Booking AS b
         JOIN RestaurantTable AS rt
            ON b.TableID = rt.TableID
         WHERE b.CustomerID = ?
         ORDER BY b.BookingDateTime ASC";

if ($customerID !== null) {
    echo "<h3>Bookings for Customer ID: " . $customerID . "</h3>";
    $stmt2 = $conn->prepare($sql2);

    if (!$stmt2) {
        die("Booking query preparation failed: " . htmlspecialchars($conn->error));
    }

    $stmt2->bind_param("i", $customerID);
    if (!$stmt2->execute()) {
        die("Booking query failed: " . htmlspecialchars($stmt2->error));
    }
    $result2 = $stmt2->get_result();
} else {
    $result2 = null;
}

if ($result2 && $result2->num_rows > 0) {

    echo "<table>";
    echo "<tr>
            <th>Booking ID</th>
            <th>Arrival Date and Time</th>
            <th>Number of Guests</th>
            <th>Table Number</th>
            <th>Location</th>
          </tr>";

    while ($row = $result2->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row["BookingID"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["BookingDateTime"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["NumberOfGuests"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["TableNumber"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["Location"]) . "</td>";
        echo "</tr>";
    }

    echo "</table>";

} elseif ($customerID !== null) {
    echo "<p>No bookings found for this customer.</p>";
} else {
    echo "<p>No customers are available.</p>";
}

if (isset($stmt2)) {
    $stmt2->close();
}


// QUERY 3: DETAILS FOR A SELECTED BOOKING

$sql3 = "SELECT
            b.BookingID,
            b.BookingDateTime,
            b.NumberOfGuests,
            rt.TableID,
            rt.TableNumber,
            c.CustomerID,
            c.FirstName,
            c.LastName,
            c.Email,
            c.Phone
         FROM Booking AS b
         JOIN RestaurantTable AS rt
            ON b.TableID = rt.TableID
         JOIN Customer AS c
            ON b.CustomerID = c.CustomerID
         WHERE b.BookingID = ?";

echo "<h2>3. Details for a Booking</h2>";
echo "<form method='get'>";
echo "<label for='booking_id'>Booking ID:</label> ";
echo "<select name='booking_id' id='booking_id' required"
    . (count($bookings) === 0 ? " disabled" : "") . ">";

foreach ($bookings as $booking) {
    $id = (int) $booking["BookingID"];
    $label = "Booking " . $id . " - "
        . $booking["BookingDateTime"] . " - "
        . $booking["FirstName"] . " " . $booking["LastName"];
    $selected = $id === $bookingID ? " selected" : "";
    echo "<option value='" . $id . "'" . $selected . ">"
        . htmlspecialchars($label) . "</option>";
}

if (count($bookings) === 0) {
    echo "<option value=''>No bookings available</option>";
}

echo "</select> ";
if ($customerID !== null) {
    echo "<input type='hidden' name='customer_id' value='" . $customerID . "'>";
}
echo "<button type='submit'>Show booking</button>";
echo "</form>";

if ($bookingID !== null) {
    $stmt3 = $conn->prepare($sql3);

    if (!$stmt3) {
        die("Booking query preparation failed: " . htmlspecialchars($conn->error));
    }

    $stmt3->bind_param("i", $bookingID);
    if (!$stmt3->execute()) {
        die("Booking query failed: " . htmlspecialchars($stmt3->error));
    }
    $result3 = $stmt3->get_result();
} else {
    $result3 = null;
}

if ($result3 && $result3->num_rows > 0) {

    echo "<table>";
    echo "<tr>
            <th>Booking ID</th>
            <th>Arrival Date and Time</th>
            <th>Guests</th>
            <th>Table Number</th>
            <th>Customer ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Phone</th>
          </tr>";

    while ($row = $result3->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row["BookingID"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["BookingDateTime"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["NumberOfGuests"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["TableNumber"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["CustomerID"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["FirstName"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["LastName"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["Email"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["Phone"]) . "</td>";
        echo "</tr>";
    }

    echo "</table>";

} elseif ($bookingID === null) {
    echo "<p>No bookings are available.</p>";
} else {
    echo "<p>Booking not found.</p>";
}

if (isset($stmt3)) {
    $stmt3->close();
}


$conn->close();

echo "</body>";
echo "</html>";
?>