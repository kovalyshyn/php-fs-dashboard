<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

/*Route::get('/', function()
{
	return View::make('index');
});*/

Route::get('/', array('as' => 'home', function () {
    return View::make('index');
}));

// Switch Users manager Routes
Route::controller('adm/users', 'SwitchUsersController');
Route::get('adm/user/{id}', function ($id) {
	$user = SwitchUser::find($id);
	if ($user) {
		return View::make('adm.user')->with('user', $user);
	}
	return Redirect::to('adm/users')
		->with('message', 'No such user!');
});
Route::get('switchusers', function () {
	if (Auth::attempt(array('email' => $email, 'password' => $password))) {
  		$users = SwitchUser::all();
		return View::make('switchusers')->with('switchusers', $users);
	}
});

Route::controller('users', 'UsersController');
Route::get('login', array('as' => 'login', function () { 
	return View::make('index');
}))->before('guest');
Route::post('login', function () { });
Route::get('logout', array('as' => 'logout', function () {
    Auth::logout();
    return Redirect::route('home')
        ->with('flash_notice', 'You are successfully logged out.');
}))->before('auth');
Route::get('profile', array('as' => 'profile', function () { }))->before('auth');


Route::group(array('before' => 'auth'), function()
{

// Gataways manager routes
Route::controller('getaways', 'GetawaysController');
// Destinations
Route::controller('destinations', 'DestinationsController');
// add new getaway
Route::get('gw/{id}', function ($id) {
	switch ($id) {
		case '1':
			return View::make('layouts.goip');
			break;
		case '2':
			return View::make('layouts.sipgsm');
			break;
		case '3':
			return View::make('layouts.gw');
			break;
		default:
			return View::make('layouts.gw');
			break;
	}
});

// Reports
Route::controller('reports', 'ReportsController');
// The news
Route::controller('news', 'NewsController');
// FAS
Route::controller('adm/fas', 'FasController');
// CDR
Route::controller('adm/cdr', 'CdrController');
Route::controller('adm/cdr/del', 'CdrController');
// Dialer Manager
Route::controller('adm/dialer', 'DialerController');
// BlackLists Manager
Route::controller('blacklist', 'NumberListController');

});
?>
