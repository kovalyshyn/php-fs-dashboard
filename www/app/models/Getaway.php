<?php

class Getaway extends Eloquent {

	protected $table = 'Getaways';

	public static $rules = array('ip'=>'required|ip', 'limit'=>'required|integer', 'minutes'=>'required|integer', 'delay'=>'integer', 'port'=>'integer');

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

	public function user()
	{
		return $this->belongsTo('SwitchUser', 'user_id');
	}

	public function destination()
	{
		return $this->belongsTo('Destinations', 'destinations');
	}

	public function type()
	{
		return $this->belongsTo('GetawayType', 'type_id');
	}

	public function cdr()
	{
		return $this->hasMany('CDR', 'gw_id');
	}

	public function getBalance()
	{
		return $this->hasMany('GsmBalance', 'imei', 'imei');
	}

	public function getActiveClass()
  	{
		if ($this->active && $this->destination->active) {
		    return "success";
		}
        return "error";
  	}

  	public function CheckboxDisable()
  	{
		if (Auth::user()->type == '2') {
			return array('disabled'=>'disabled');
		}
        return "";
  	}

	public function Registration()
	{
		return $this->hasOne('Registrations', 'reg_user', 'id');
	}

}


?>