<?php
// connect to mysql
$Pass = 'Pepperjen(23'; // insert your password
$DB = 'lexHealth';
$conn = mysqli_connect('127.0.0.1', 'root', $Pass, $DB);


$lat = $_POST["lat"];
$lon = $_POST["lon"];

if (!$conn) {
   echo "Connection failed: ". mysqli_connect_error(). "\n";
}
else {
    // check username
    $user_query = "SELECT name, rating, priceRating FROM restaurant WHERE latitude ='".$lat."' AND longitude='".$lon."'";
    if(!$result = mysqli_query($conn,$user_query)) {
	echo "Query failed: ". $mysqli->error. "\n";
    }

    if(mysqli_num_rows($result) > 0) {
        $restaurants = array();
        //read the rows of result
        while($row = mysqli_fetch_assoc($result)) {
             $restaurants[] = $row;
        }

        header('Content-type: application/json');
        print json_encode($restaurants);
	}
}
mysqli_close($conn);
?>
