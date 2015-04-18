<?php

class CDR extends Eloquent {

	protected $table = 'cdr';

	public function getaway()
	{
		return $this->belongsTo('Getaway', 'gw_id');
	}
	
	public function destination()
	{
		return $this->belongsTo('Destinations', 'destination_id');
	}

}


?>