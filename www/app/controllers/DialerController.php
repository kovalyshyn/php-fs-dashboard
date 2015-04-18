<?php

class DialerController extends BaseController
{

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on'=>'post'));
    }

    public function getIndex()
    {
        return View::make('adm.dialer_list')
            ->with('dialer', Dialer::orderBy('created_at', 'DESC')->paginate(40));
    }

      public function getNew()
    {
        return View::make('adm.dialer_create');
    }

    public function postCreate()
    {
        $dialer = new Dialer;
        $dialer->concurrent_calls = Input::get('concurrent_calls');
        $dialer->total_calls = Input::get('total_calls');
        $dialer->durations = Input::get('durations');
        $dialer->destination_srv = Input::get('destination_srv');
        $dialer->source_num = Input::get('source_num');
        $dialer->pause_between_rounds = Input::get('pause_between_rounds');
        $dialer->wait_answer = (!Input::get('wait_answer') ? false : true);
        $dialer->done = false;
        $dialer->save();

        if (Input::hasFile('b_numbers_file')) {
            $file = Input::file('b_numbers_file');
            $name = time();
            $file->move(public_path() . '/tmp', $name);

            $lines = file(public_path() . '/tmp/' . $name);
            $csv = public_path() . '/tmp/' . $name. '.csv';

            $fp = fopen($csv, 'w');
            foreach ($lines as $line_num => $line) {
                fputcsv($fp, array($dialer->id, trim(preg_replace('/\s+/', ' ', $line)) ));
            }
            fclose($fp);

            // for docker only :)
            $csv = '/var/opt/' . $name. '.csv';
            $query = sprintf("COPY \"DialerB\" (dialer_id, number) FROM '%s' DELIMITER ',' CSV", addslashes($csv));
            DB::connection()->getpdo()->exec($query);
        }

        if (Input::get('b_numbers_mask') and Input::get('b_numbers_count')) {
            $i = 0;
            function randomNum() { return rand(0, 9); }
            while ($i < Input::get('b_numbers_count')) {
                $iq = "INSERT INTO \"DialerB\" (dialer_id, number) VALUES (".$dialer->id.", ".preg_replace_callback("/X/", 'randomNum', Input::get('b_numbers_mask')).") ";
                DB::connection()->getpdo()->exec($iq);
                $i++;
            }
        }

        return Redirect::to('adm/dialer');
    }

    public function getCancel($id)
    {
    	$dialer = Dialer::find($id);
    	$dialer->done = true;
        $dialer->save();
        return Redirect::to('adm/dialer');
    }


}

?>
