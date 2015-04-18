<?php

class Destinations extends Eloquent {

	protected $table = 'Destinations';

	protected $fillable = array('name', 'number_length', 'local_prefix', 'global_prefix');

	public static $rules = array('name'=>'required|min:4', 'number_length'=>'required|min:1', 'global_prefix'=>'required|min:1');
	//'local_prefix'=>'required|min:1', 

	public function getActive()
  	{
		switch ($this->active) {
		    case 0:
		        return "Offline";
		        break;
		    case 1:
		        return "Active";
		        break;
		}
        return "Unknown";
  	}

  	public function getActiveIcon()
  	{
		switch ($this->active) {
		    case 0:
		        return "icon-remove";
		        break;
		    case 1:
		        return "icon-ok";
		        break;
		}
        return "icon-question-sign";
  	}

  	public function getaways()
	{
		return $this->hasMany('Getaway', 'destinations');
	}
	
	public function getCDR()
	{
		return $this->hasMany('CDR', 'destination_id');
	}

	public function user()
	{
		return $this->belongsTo('SwitchUser', 'user_id');
	}

}

?>