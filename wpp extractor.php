<?php
include "config.php";

function between_two_strings($string, $first, $second)
{
	if($first != $second)
	{
		return substr($string, strpos($string, $first), strpos($string, $second)-strpos($string, $first));
	}
	else
	{
		return substr($string, strpos($string, $first), strpos($string, $second, strpos($string, ",")+1)-strpos($string, $first));
	}
}

$extracted_path = readline("Path of the extracted sql: ");
readline_add_history($extracted_path);

$output_name = readline("Output of the file sql: ");
readline_add_history($output_name );

$lines = file($extracted_path);

// line between are fixed the spawns of the creatures
$l = 0;
$l2 = 0;
for($i = 0; $i < count($lines); $i++)
{
	if(strpos($lines[$i], "INSERT INTO `creature`") > -1) { $l = $i; }
	if($l != 0 and strpos($lines[$i], "); -- ")) { $l2 = $i; break; }
}

$entries = array();
$m = 0;
$notneed = array();
$k = 0;

//extract the ids
for ($i = $l+1; $i < $l2; $i++)
{
	//take the id from line $i
	$id = between_two_strings($lines[$i], ",", ",");
	$id = str_replace(" ", "", $id);
	$id = str_replace(",", "", $id);

	// check if exist the id in creature_template
	$skip = 0;
	$query_exist = mysql_query("SELECT COUNT(*) FROM creature_template WHERE entry = $id");
	while ($row = mysql_fetch_array($query_exist))
	{
		if($row['COUNT(*)'] == 0) { $skip=1; }
	}

	if($skip != 1)
	{
		// to avoid duplicate
		$exist = 1;
		for($j = 0; $j < count($entries); $j++)
		{
			if($entries[$j] == $id) { $exist = 0; }
		}
	}
	else
	{
		$notneed[$k] = $id;
		$k++;
		$exist = 0;
		echo "$id doesn't exist in creature_template \n";
	}

	// check if exist the id in creature
	$exist_c = 1;
	$skip_c = 0;
	for($a = 0; $a < count($notneed); $a++)
	{
		if($id == $notneed[$a])
		{
			$skip_c = 1;
			break;
		}
	}
	if ($skip_c == 0)
	{
		$already_spawn = 1;
		$query_ = mysql_query("SELECT COUNT(*) FROM creature WHERE id = $id");
		while($rows = mysql_fetch_array($query_))
		{
			if($rows['COUNT(*)'] != 0) { $already_spawn = 0; $exist_c = 0; }
		}

		if($already_spawn != 0)
		{
			$notneed[$k] = $id;
			$k++;
			echo "$id is already spawned \n";
		}
	}
	
	// if exist in creature_template and if not exist in creature add to entries
	if($exist != 0 and $exist_c != 0) { $entries[$m] = $id; $m++; }
}

$fix = "SET @CGUID = XXXX;
DELETE FROM `creature` WHERE `guid` BETWEEN @CGUID AND @CGUID+N;
INSERT INTO `creature` (`guid`, `id`, `map`, `spawnMask`, `phaseMask`, `phaseId`, `position_x`, `position_y`, `position_z`, `orientation`, `spawntimesecs`, `spawndist`, `MovementType`) VALUES \n";

$n=0;
for($j = 0; $j < count($entries); $j++)
{
	for($i = $l; $i < $l2; $i++)
	{
		if(strpos($lines[$i], "$entries[$j],"))
		{
			$skip = 0;
			$add = $lines[$i];
			$add = str_replace(between_two_strings($add, "(@CGUID+", ","), "(@CGUID+$n", $add);
			$id = between_two_strings($add, ",", ",");
			$id = str_replace(" ", "", $id);
			$id = str_replace(",", "", $id);
			$add = substr($add, 0, strpos($add, "-- $id")+strlen("-- $id"));
			$query = mysql_query("SELECT name FROM creature_template WHERE entry = $id");
			while ($row = mysql_fetch_array($query)) { $add .= " ({$row['name']}) \n"; }
			$fix .= $add;
			$n++;
		}
	}
}

$n--;
$fix = str_replace("), -- $id", "); -- $id", $fix);
$fix = str_replace("@CGUID+0", "@CGUID", $fix);
$fix = str_replace("@CGUID+N", "@CGUID+$n", $fix);

$file = fopen($output_name, "w+");
fwrite($file, $fix);
fclose($file);
?>