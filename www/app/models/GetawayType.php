<?php

class GetawayType extends Eloquent {

	protected $table = 'GetawayType';

	public function getaway()
	{
		return $this->hasMany('Getaway', 'type_id');
	}

}


?>