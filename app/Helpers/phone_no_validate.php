<?php

function validate_phone_query_builder($num , $registrant_email,$i , $created_at , $updated_at)
  {
  		

  		$str = '';
  		try
      {
  			if($num != '')
	  		{
	  			$no = explode('.',$num);

	  			if(isset($no[1]))
		  			$arr = ($this->validateUSPhoneNumber($no[1]));
		  		else
		  			$arr = ($this->validateUSPhoneNumber($no[0]));
		  		if($arr['http_code'] == 200)
		  		{
		  			$str = "NULL , '"
		  					.$arr['phone_number']
		  					."','".str_replace($this->search, $this->replace, $arr['validation_status'])
		  					."','".str_replace($this->search, $this->replace, $arr['state'])
		  					."','".str_replace($this->search, $this->replace, $arr['major_city'])
		  					."','".str_replace($this->search, $this->replace, $arr['primary_city'])
		  					."','".str_replace($this->search, $this->replace, $arr['county'])
		  					."','".str_replace($this->search, $this->replace, $arr['carrier_name'])
		  					."','".str_replace($this->search, $this->replace, $arr['number_type'])
                ."','".$created_at
                ."','".$updated_at
		  					."','".str_replace($this->search, $this->replace, $registrant_email)."'";
		  		}
	  		}
  		}
  		catch(\Exception $e)
  		{
  			dd($i , $num ,$no);
  		}
  		return $str;
  }


?>