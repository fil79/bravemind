<?php

require_once("inc/Csv.Class.php");

$group   = (isset($_REQUEST['group'])? $_REQUEST['group'] : null );
$deal_id = (isset($_REQUEST['deal_id'])? $_REQUEST['deal_id'] : null );

$csv   = new Csv();
$csv->setdb();
//No need to escape-PDO handle it 
$rows  = $csv->getSales($group, $deal_id);
$sales_id = $csv->getSalesId();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>INFO</title>
</head>
<body>
<form action="info.php" method="get">

Group By:
<select name="group">
	<option value="0">group by</option>
	<option value="hour">hour</option>
	<option value="day">day</option>
	<option value="month">month</option>
</select>
Id:
<select name="deal_id">
<option value="0">sales_id</option>
<?php 
foreach ($sales_id as $id){
	echo '<option value="'.$id[0].'">'.$id[0].'</option>';
}
?>
</select>

<input type="submit" value="Search">
</form>
<table style="padding:20px; text-align:center;" border="2">
<tr>
	<th>deal_id</th>
	<th>hour</th>
	<th>sent</th>
	<th>accepted</th>
	<th>refused</th>
</tr>
<?php

foreach ($rows as $sales){

	echo "<tr>
			<td>".$sales[0]."</td>
			<td>".$sales[1]."</td>
			<td>".$sales[2]."</td>
			<td>".$sales[3]."</td>
			<td>".$sales[4]."</td>
		  </tr>	\n";
}

?>

</table>
</body>
</html>