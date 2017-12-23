<?php
//$mysqli = new mysqli("localhost", "renaissance", "*******", "renaissance");
require 'db.php';

$instrument_id = $_GET['instrument_id'];
$output = $_GET['output'];
$query = "SELECT id, price_date, open_price, close_price, low_price, high_price, volume
          FROM min_price 
          where instrument_id='".$instrument_id."' order by price_date asc";

$result = db::getInstance()->get_results($query);

$myArray = array();

if ($result)
    echo json_encode($result);
else
    echo "Results not found";

/*if ($result)  { 
    // results found 
    while($row = $) {
        $myArray[] = $row;
    }
    echo json_encode($myArray);
} else { 
    // results not found 
    echo "Results not found";
}  */
?>
