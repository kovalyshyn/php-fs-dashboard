<?php

class GsmBalance extends Eloquent {

	protected $table = 'GsmBalance';

	public function getaway()
	{
		return $this->belongsTo('Getaway', 'imei', 'imei');
	}

}


?>