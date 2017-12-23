<?php

require 'db.php';

$company_id = $_GET['company_id'];
$exchange_id = $_GET['exchange_id'];

$sql = "SELECT c.id, i.symbol, c.name, c.sector, c.industry, i.id as instrument_id, e.id as exchange_id, e.name as exchange_name,e.world_ex_id
        FROM company as c
        inner join instrument as i on i.company_id = c.id
        inner join exchange as e on i.exchange_id = e.id
        where 1=1";

if($company_id)
    $sql .= " and c.id=" . $company_id;
    
if ($exchange_id)
    $sql .= " and e.id=" . $exchange_id;

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
    echo "<thead><tr><th>Id</th><th>Symbol</th><th>Name</th><th>Sector</th><th>Industry</th>";
    foreach($result as $row) {
        $myArray[] = $row;
        echo "<tr>";
        echo "<td><a href='company.php?company_id=".$row['id']."'>" . $row['id'] . "</a></td>";
        echo "<td><a href='plot.php?instrument_id=" . $row['instrument_id'] . "'>" . $row['symbol'] . "</a></td>";
        echo "<td><a href='company.php?company_id=".$row['id']."'>" . $row['name'] . "</a></td>";
        echo "<td>" . $row['sector'] . "</td>";
        echo "<td>" . $row['industry'] . "</td>";
        echo "<td><a href='company.php?exchange_id=".$row['exchange_id']."'>" . $row['exchange_name'] . "<td>";
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
