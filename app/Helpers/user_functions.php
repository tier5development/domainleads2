<?php

function generateDateRange($start_date,$end_date)
{
    $dates = [];
    if($start_date == null && $end_date == null)
    {
    	return null;
    }
    else if($start_date == null)
    {
    	$dates[0] = $end_date;
    	return $dates;
    }
    else if($end_date == null)
    {
    	$dates[0] = $start_date;
    	return $dates;
    }
    else
    {
    	$day = 3600*24;
    	if(strtotime($start_date) > strtotime($end_date) )
		{
			$temp = $start_date;
			$start_date = $end_date;
			$end_date = $temp;
		}
		$final_time = strtotime($end_date);
    	for($d = strtotime($start_date); $d <= $final_time ; $d += $day) 
    	{
	        $dates[] = date('Y-m-d',$d);
	    }
    	return $dates;
    }
}

?>