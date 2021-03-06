<?php
session_start();
$restaurant = $_GET['restaurant'];

// connect to mysql
$Pass = 'yourpassword'; // insert your password
$DB = 'lexHealth';
$conn = mysqli_connect('127.0.0.1', 'root', $Pass, $DB);

if (!$conn) {
   echo "Connection failed: ". mysqli_connect_error(). "\n";
}
else {
        /* Restaurant information block
	Query for restaurant info to display */
        $restaurant_query = mysqli_query($conn, "SELECT * FROM restaurant WHERE name = '$restaurant'");
        $restaurant_info = mysqli_fetch_row($restaurant_query);

        $open_hour = $restaurant_info[6];
        if($open_hour > 12) {
                $open_hour = $open_hour%12 .' PM';
        }
        else if($open_hour == 24) {
                $open_hour = '12 AM';
        }
        else if($open_Hour == 12) {
                $open_hour = '12 PM';
        }
        else {
                $open_hour = $open_hour .' AM';
        }

     $close_hour = $restaurant_info[7];
     if($close_hour > 12) {
          $close_hour = $close_hour%12 .' PM';
     }
     else if($close_hour == 24) {
          $close_hour = '12 AM';
     }
     else if($close_hour == 12) {
          $close_hour = '12 PM';
     }
     else {
          $close_hour = $close_hour .' AM';
     }

     $rid = $restaurant_info[0];
     $_SESSION["rid"] = $rid;
     $_SESSION["restaurant"] = $restaurant;
     $address = $restaurant_info[2];
     $latitude = $restaurant_info[3];
     $longitude = $restaurant_info[4];
     $price = $restaurant_info[5];
     $rating = $restaurant_info[8];
     $_SESSION["rating"] = $rating;
     $specialty = $restaurant_info[9];
     $description = $restaurant_info[10];

	// reviews query
	$review_query = mysqli_query($conn, "SELECT * FROM reviews WHERE rid = '$restaurant_info[0]' ORDER BY timestamp DESC");

	// items query
	$item_query = mysqli_query($conn, "SELECT * FROM menuitem WHERE rid = '$restaurant_info[0]'");
	
}
mysqli_close($conn);
?>

<html lang="en">
<head>
    <meta charset="utf-8">

    <title>The HTML5 Herald</title>
    <meta name="description" content="The HTML5 Herald">
    <meta name="author" content="SitePoint">

    <link rel="stylesheet" href="../css/style.css">
    <!-- google maps -->
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDRvaTL4If1SfVTXDalSe9aJwU7TQzP8D8">
    </script>

</head>

<body onload = "onload(<?php echo $latitude ?>, <?php echo $longitude ?>, '<?php echo $restaurant ?>')">
    <!-- nav bar -->
     <div class="top-bar">
         <div class='nav-name' id='home-button' onclick="window.location.href='./main.php'">
             <img id="icon" src="../css/images/icon.png" alt="lexHealth"/>
         </div>
        
         <h1 style="margin-top:5px;"><i>EatWell</i></h1>
        <!--
         <div id="search-div">
             <input type="text" name="search" style="width:90%;">
         </div>
         <button type="submit" id="browse"> Browse</button>
        -->
            
     
        <div class="mini-wrapper" style="margin-left:60%;">
            <div class='nav-name mini' id='plan-button' onclick="window.location.href='./plan.php'"> Plan</div>
        </div>

         <div class="mini-wrapper" >
             <div class='nav-name mini' id='restaurants-button' onclick="window.location.href='./restaurants.php'"> Restaurants</div>
         </div>

	 <div class="mini-wrapper">
             <div class='nav-name mini' id='profile-button' onclick="window.location.href='./profile.php'"> My Profile</div>
         </div>
     </div>
        
     <div style="width:50%; top:10%; height:100%; position:fixed; left:0%;">
        <!-- restaurant info -->
        <div style="position:fixed; left:2%; width:50%; top:10%; margin:0px;"> 
            <h1 style="margin:0px;"><?php echo $restaurant. '    '. $rating. '/5' ?></h1>
	    <span style="color:#4AC50;font-weight:bold; margin-top:2%;"><?php echo ' '.  $price. ' - '. $specialty?></span>
            <h3 style="margin-top:1%;"><?php echo 'Hours: '. $open_hour. '-'. $close_hour?></h3>
            <h3 style="width:30%;"><?php echo $address?></h3>
	    <div style="width:33%;"><?php echo $description?></div>
        </div>
        <div id="map" style="position:fixed; height:40%; left:40%; width:50%; top:5%; margin:0px; background-color:black;">
        
        </div>

	 <!-- featured items -->
        <div style="position:fixed; top:10%; width:50%; right:0%;">
            <h2>Featured Items</h2>
            <dl style="position:relative; left:2%">
                <?php
                    while($item = $item_query->fetch_assoc()) {
                        echo '<dt style="font-weight:bold;">'. $item['name'] . " - $" . $item['price'] . '</dt>';

                        $query = "SELECT * FROM contains WHERE rid = $restaurant_info[0] AND name = '$item[name]'";
                        $Pass = 'yourpassword'; // insert your password
                        $DB = 'lexHealth';
                        $conn = mysqli_connect('127.0.0.1', 'root', $Pass, $DB);

                        $contains_query = mysqli_query($conn, $query);

                        $ingredient = $contains_query->fetch_assoc();
                        echo "<dt> $ingredient[ingredientName]";
                        while($ingredient = $contains_query->fetch_assoc())
                        {
                            echo ", $ingredient[ingredientName]";
                        }
                        echo "</dt>";
                        mysqli_close($conn);
                    }
                ?>
            </dl>
        </div>

	<!-- review section -->
        <div style="position:fixed; top:50%; width:50%;left:2%;">
             <h2>Submit a Review</h2>
             <form action="../php/submit_review.php/?rid=<?php echo $rid?>" method="post">
        <?php echo 'How was '. $restaurant. '?'?>
                <label for="bad">1</label>
                <input name="rating" type="range" min="1" max="5" step="1"></textarea>
                <label for="">5</label>
                <br /><br />
                <textarea name="review" rows="6" cols="120"></textarea>
                <button id="submit_review" type="submit" style="position:relative;right:7%">Submit</button>
             </form>
     </div>

    <!--past reviews-->
    <div style="position:fixed; top:50%; right:0%; width:50%;">
	     <h2>Reviews</h2>
	     <dl style="position:relative; left:2%">
        <?php
        $length = 10;
        $i = 0;
        while($review = $review_query->fetch_assoc()){
            if($i < $length) {
                echo '<dt style="font-weight: bold">'. $review['timestamp'] . " " . $review['username'] . '</dt>';
                echo '<dd>'. $review['rating'] . '/5: ' . $review['review']. '</dd>';
            }
            $i = $i + 1;
		    }
		?>
	     </dl>
     </div>
	</div>

<script>
    var map; 

    function onload(lat, lon, res) {
        map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: lat, lng: lon},
            zoom: 15 });     
        
        var marker = new google.maps.Marker({
               position: new google.maps.LatLng(lat,lon),
               label: { fontWeight: 'bold', fontSize: '12px', text: res}
        });
        marker.setMap(map); 
    }

</script>

</body>

</html>
