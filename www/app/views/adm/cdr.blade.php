@extends('layouts.main')

@section('content')
  <div id="admin">

<h2>Call Detail Record</h2>

@if(Auth::user()->type == '0')

	{{ 
    GridRender::setGridId("CdrGrid")
            ->enablefilterToolbar()
            ->setGridOption('url',URL::to('adm/cdr/grid-data/cdr'))
            ->setGridOption('rowNum',50)
            ->setGridOption('shrinkToFit',false)
            //->setGridOption('sortname','uuid')
            //->setGridOption('caption','LaravelJqGrid example')
            ->setNavigatorOptions('navigator', array('viewtext'=>'view'))
            ->setNavigatorOptions('view',array('closeOnEscape'=>false))
            ->setFilterToolbarOptions(array('autosearch'=>true))
       //     ->setGridEvent('gridComplete', 'function(){alert("Grid complete event");}') 
        //    ->setNavigatorEvent('view', 'beforeShowForm', 'function(){alert("Before show form");}')
        //    ->setFilterToolbarEvent('beforeSearch', 'function(){alert("Before search event");}') 
           // ->addColumn(array('index'=>'uuid', 'width'=>155))
            ->addColumn(array('name'=>'caller_id_number','index'=>'caller_id_number', 'width'=>100))
            ->addColumn(array('name'=>'destination_number', 'width'=>100))
            ->addColumn(array('name'=>'duration', 'width'=>50, 'align'=>'center'))
            ->addColumn(array('name'=>'billsec', 'width'=>50, 'align'=>'center', 'sorttype'=>'integer', 'formatter'=>'integer', 'summaryType'=>'sum'))
            ->addColumn(array('name'=>'start_stamp', 'width'=>100))
            ->addColumn(array('name'=>'answer_stamp', 'width'=>100))
            ->addColumn(array('name'=>'end_stamp', 'width'=>100))
            ->addColumn(array('name'=>'hangup_cause', 'width'=>150))
            ->renderGrid(); 
}}


@endif


  </div>

@stop