<?php

require 'db.php';

$exchange_id = $_GET['exchange_id'];

$sql = "SELECT i.id, i.symbol, c.id as company_id, c.name, e.id as exchange_id, e.name as exchange_name
        FROM instrument as i 
        inner join company as c on i.company_id = c.id 
        inner join exchange as e on i.exchange_id = e.id
        where 1=1";

if ($exchange_id)
    $sql .= " and i.exchange_id=" . $exchange_id;     

$sql .= " order by e.id, c.name"; 

$result = db::getInstance()->get_results($sql);

$myArray = array();

?>
<html>
<body>
<div>
<a href="company.php">Companies</a>
</div>
<div>
<a href="instrument.php">Instruments</a>
</div>
<table>
<?
if ($result) 
{ 
    // results found 
    //echo mysqli_num_rows($result);
    echo "<thead><tr><th>Id</th><th>Symbol</th><th>Name</th><th>Exchange</th>";
    foreach ($result as $row) {
        //$myArray[] = $row;
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td><a href='plot.php?instrument_id=" . $row['id'] . "'>" . $row['symbol'] . "</a></td>";
        echo "<td><a href='company.php?company_id=".$row['company_id']."'>" . $row['name'] . "</a></td>";
        echo "<td><a href='instrument.php?exchange_id=".$row['exchange_id']."'>" . $row['exchange_name'] . "</a></td>";
        echo "</tr>";
    }
    //echo json_encode($myArray);
}
else
{ 
    // results not found 
    echo "Results not found";
}  

?>
</table>

</body>
</html>
