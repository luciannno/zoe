<?php

require 'db.php';

$exchange_id = $_GET['exchange_id'];
$instrument_type_id = $_GET['instrument_type_id'];


$sql = "SELECT i.id, i.symbol, c.id as company_id, c.name, e.id as exchange_id, e.name as exchange_name, t.type as instrument_type, t.id as instrument_type_id
        FROM instrument as i 
        left join company as c on i.company_id = c.id 
        inner join exchange as e on i.exchange_id = e.id
	inner join instrument_type as t on i.instrument_type_id = t.id
        where 1=1";

if ($exchange_id) 
    $sql .= " and i.exchange_id=" . $exchange_id; 
if ($instrument_type_id) 
    $sql .= " and i.instrument_type_id=" . $instrument_type_id;

$sql .= " order by e.id, c.name"; 

$result = db::getInstance()->get_results($sql);

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
<?php
if ($result) 
{ 
    // results found 
    echo "<thead><tr><th>Id</th><th>Symbol</th><th>Name</th><th>Type</th><th>Exchange</th>";
    foreach ($result as $row) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td><a href='plot.php?instrument_id=" . $row['id'] . "&d=1'>" . $row['symbol'] . "</a></td>";
        echo "<td><a href='company.php?company_id=" . $row['company_id'] . "'>" . $row['name'] . "</a></td>";
        echo "<td><a href='instrument.php?instrument_type_id=" . $row['instrument_type_id'] .  "&exchange_id=" . $exchange_id . "'>" . $row['instrument_type'] . "</a></td>";
        echo "<td><a href='instrument.php?instrument_type_id=" . $instrument_type_id . "&exchange_id=".$row['exchange_id']."'>" . $row['exchange_name'] . "</a></td>";
        echo "</tr>";
    }
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
