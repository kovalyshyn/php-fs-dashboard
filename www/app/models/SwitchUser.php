<?php

class SwitchUser extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'SwitchUsers';

	protected $fillable = array('email', 'password', 'name', 'type');

	public static $rules = array('name'=>'required|min:3', 'email'=>'required|email', 'password'=>'required|min:6', 'type'=>'');
	public static $uprules = array('name'=>'required|min:3', 'email'=>'required|email');

	 /**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password');

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

	// samael
	public function getType()
  	{
		switch ($this->type) {
		    case 0:
		        return "Admin";
		        break;
		    case 1:
		        return "Agent";
		        break;
		    case 2:
		        return "User";
		        break;
		}
        return "Guest";
  	}

	public function getTypeClass()
  	{
		switch ($this->type) {
		    case 0:
		        return "success";
		        break;
		    case 1:
		        return "warning";
		        break;
		}
        return "";
  	}

  	// getaways
  	public function getaways()
	{
		return $this->hasMany('Getaway', 'user_id');
	}

	// users by parent_id
	public function users()
	{
		return $this->hasMany('SwitchUser', 'parent_id');
	}
	public function parent()
	{
		return $this->belongsTo('SwitchUser', 'parent_id');
	}


}
?>
