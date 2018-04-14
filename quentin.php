<?php
require 'db.php';

$instrument_id = $_GET['instrument_id'];
$d = (isset($_GET['d']) ? $_GET['d'] : 5);

$query = "SELECT id, price_date, open_price, close_price, low_price, high_price, volume
          FROM min_price 
          where instrument_id='".$instrument_id."' and date(price_date)>=date_sub(curdate(), interval ".$d." day) order by price_date asc";

$result = db::getInstance()->get_results($query);

$myArray = array();

if ($result) {
    echo json_encode($result);
} else {
    echo "Results not found: " . $query;
}
?>
