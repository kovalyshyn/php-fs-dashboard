<?php

class Registrations extends Eloquent {

	protected $table = 'registrations';

	public function Getaway()
	{
		return $this->belongsTo('Getaway', 'id', 'reg_user');
	}

}

?>