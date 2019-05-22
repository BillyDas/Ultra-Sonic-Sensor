<?php
include_once 'DataBaseCon.inc';


//show errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn= @mysqli_connect($host, $user, $pwd, $sql_db);

if (!$conn) {
	      echo "<p>Database Connection Failure</p>";
	    } 
	    else 
	    {
	    	echo "<table border=\"2\">\n";
			echo "<tr>\n "
	 		."<th scope=\"col\">Number Plate</th>\n "
	 		."<th scope=\"col\">Park Time</th>\n "
	 		."<th scope=\"col\">Fee</th>\n "
	 		."</tr>\n ";
	 		
   
	    	$query = "SELECT * FROM ParkedDuration ORDER BY ID DESC LIMIT 10";        
			$result = mysqli_query($conn, $query); 
	    	while ($row = mysqli_fetch_assoc($result))
         {
         	echo "<tr>";
         	$Price = $row["ParkTime"];
         	echo "<td>".$row["NumberPlate"]."</td>";
         	echo "<td>".$Price." Minutes</td>";
         	$Price = $Price * .166;
         	echo "<td>$".$Price." AUD</td>";
         	echo "</tr>";
         }
        
         echo "</table>";
       }
       mysqli_close($conn);

?>


