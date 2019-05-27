<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <!--<script src="ajax.js"></script> -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	 <link rel="stylesheet" type="text/css" href="styles/tables.css" >        
    <title>Admin Receipt Page</title>

</head>
<body>
<?php
include_once 'DataBaseCon.inc';
//show errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn= @mysqli_connect($host, $user, $pwd, $sql_db);

if (!$conn) 
	{
		echo "<p>Database Connection Failure</p>";
	} 
	else 
	{		
   	echo "<h1> Send Reciept To Customer </h1>"; 
      echo "<table border=\"2\">\n";
		echo "<tr>\n "
	 	."<th scope=\"col\">Number Plate</th>\n "
	 	."<th scope=\"col\">Park Time</th>\n "
	 	."<th scope=\"col\">Fee</th>\n "
	 	."</tr>\n ";	
   
	   $query = "SELECT NumberPlate,ParkTime FROM ParkedDuration ORDER BY ID DESC LIMIT 10";        
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
    ?>
    <br />
<form action="ReceptEmail.php" method="POST">
	<fieldset>
   	<legend>Email A Reciept To A Customer </legend>
	<br />
	<input type="text" id="NumberPlate" required="required" maxlength="6" name="NumberPlate" placeholder="Number Plate">
	<br />
	<br />
	<button type="submit" class="btn btn-primary">Email Customer</button>
	</fieldset>
</form>

	<?php
	require_once "Mail.php";
	
    if(isset($_POST['NumberPlate']) && ($_POST['NumberPlate'] != ""))
	{
		$Numberplate = $_POST['NumberPlate'];
		$query = "SELECT * FROM ParkingUsers WHERE Numberplate = '$Numberplate'";        
		$result = mysqli_query($conn, $query); 
		while ($row = mysqli_fetch_assoc($result))
         {
         	$queryForParkAmmount = "SELECT * FROM ParkedDuration WHERE NumberPlate = '$Numberplate' ORDER BY ID DESC";
      		$ParkAmmount = mysqli_query($conn, $queryForParkAmmount);
      		$resultParkAmmount = mysqli_fetch_assoc($ParkAmmount);
         	$Price = $resultParkAmmount["ParkTime"];
         	$to = '<'.$row["Email"].'>';	
         	$Email = $row["Email"];
         	$Price = $Price * .166;
         }
		
		$msg = "Thank You For Parking With Rip Off Parking \nYour Numberplate Is ".$Numberplate." \nThe Ammount Due Is $".$Price." AUD";
		
		
		$from = '<billydasuni@gmail.com>';
		$subject = 'Parking Reciept';
	
		$headers = array(
    	'From' => $from,
   	 'To' => $to,
   	 'Subject' => $subject
	);

		$smtp = Mail::factory('smtp', array(
        'host' => 'ssl://smtp.gmail.com',
        'port' => '465',
        'auth' => true,
        'username' => 'billydasuni@gmail.com',
        'password' => 'Billy202'
    ));
	
		$mail = $smtp->send($to, $headers, $msg);
		
		if (PEAR::isError($mail)) {
    		echo('<p>' . $mail->getMessage() . '</p>');
		} 
		else {
			echo "<br />";
    		echo '<p>Message successfully sent to '.$Email.'</p>';
		}	
		
	}

	?>
	<hr>
	<br>
	<a class="btn btn-primary btn-sm" href="index.html" role="button ">Back To Index</a>
  
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
