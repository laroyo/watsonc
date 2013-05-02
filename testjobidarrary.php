<?php

$job_ids = $_POST['job_ids'];
	
if( is_null($job_ids) )
{
	
	echo "null";
}
else if (empty($job_ids))
{
	
	echo "empty";
}
else {
	foreach ($job_ids as $job_id) {
		echo $job_id;
	//	echo ",";
	}
	
	//echo $job_ids;
}


?>
