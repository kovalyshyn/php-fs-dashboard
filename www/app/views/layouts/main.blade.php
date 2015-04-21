<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>switch manager v.1.3</title>
        <meta name="description" content="switch manager by samael">

        {{ HTML::style('css/main.css') }}
        {{ HTML::style('css/bootstrap.min.css') }}
        {{ HTML::style('css/jquery-ui-1.10.4.custom.min.css') }}
        {{ HTML::style('css/ui.jqgrid.css') }}
        {{ HTML::script('js/jquery-1.11.0.min.js') }}
        {{ HTML::script('js/i18n/grid.locale-en.js') }}
        {{ HTML::script('js/jquery.jqGrid.min.js') }}
        {{ HTML::script('js/bootstrap.min.js') }}

    </head>
    <body>

    <div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <ul class="nav">  
            @if(!Auth::check())
                <li></li>   
            @else
                <li>{{ HTML::link('news', 'News') }}</li>
                <li>{{ HTML::link('adm/users', 'Users') }}</li>
                @if(Auth::user()->type == '0')
                <li>{{ HTML::link('destinations', 'Destinations') }}</li>
                @endif
                <li>{{ HTML::link('getaways', 'Gateways') }}</li>
                <li>{{ HTML::link('blacklist', 'Blacklists') }}</li>
                @if(Auth::user()->type == '0')
                    <li>{{ HTML::link('adm/fas', 'FAS') }}</li>
                    <!-- <li>{{ HTML::link('adm/cdr', 'CDR') }}</li> -->
                    <li>{{ HTML::link('adm/dialer', 'Dialer') }}</li>
                @endif
            @endif
            </ul> 
        </div>
    </div>
</div>

<div class="container">
    @if(Session::has('message'))
        <p class="alert">{{ Session::get('message') }}</p>
    @endif

    @yield('content')
</div>


    <div class="navbar navbar-fixed-bottom">
    <div class="navbar-inner">
        <div class="container">
            <ul class="nav">  
                <li>{{ HTML::link('/', 'switch manager v.1.3 (150421)') }}</li>
            @if(Auth::check())
                <li>{{ HTML::link('logout', 'Logout ('.Auth::user()->name.')') }}</li>
            @endif
            </ul>  
        </div>
    </div>
</div>
</body>
</html>
