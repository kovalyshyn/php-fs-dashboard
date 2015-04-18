<?php


class News extends Eloquent {

	protected $table = 'news';

	protected $fillable = array('name', 'the_news');

	public static $rules = array('name'=>'required|min:10', 'the_news'=>'required|min:20');

}

?>