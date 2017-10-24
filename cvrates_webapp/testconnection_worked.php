<?php
$servername = "127.0.0.1";
$password = "";
$username = "root";
$db = "CvRate";


// Create connection
$conn = mysqli_connect($servername, $username, $password, $db);


$sql = "SELECT name FROM mycity";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "id: " . $row["name"]. "<br>";
    }
} else {
    echo "0 results";
}
$conn->close();

?>


