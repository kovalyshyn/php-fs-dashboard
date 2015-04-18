<?php 

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Mgallegos\LaravelJqgrid\Encoders\RequestedDataInterface;
use Illuminate\Database\Eloquent\Model;
use Mgallegos\LaravelJqgrid\Repositories\EloquentRepositoryAbstract;

class CdrRepository extends EloquentRepositoryAbstract {

    public function __construct()
    {
        $this->Database = new CDR;
        $this->visibleColumns = array('caller_id_number','destination_number', 'duration', 'billsec', 'start_stamp', 'answer_stamp', 'end_stamp', 'hangup_cause', 'gw_id' );
        $this->orderBy = array(array('start_stamp', 'asc'), array('caller_id_number','desc'));
    }

}

class CdrController extends BaseController {

    protected $GridEncoder;

    public function __construct(RequestedDataInterface $GridEncoder)
    {
        $this->GridEncoder = $GridEncoder;
    }

    public function getIndex()
    {

        return View::make('adm.cdr');

    }

    public function postGridData()
    {
        $this->GridEncoder->encodeRequestedData(new CDRRepository(), Input::all());
    }

}