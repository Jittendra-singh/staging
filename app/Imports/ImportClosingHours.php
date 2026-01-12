<?php

namespace App\Imports;

use App\Models\MachineWorkingHour;
use App\Models\RejectedClosingHour;
use Maatwebsite\Excel\Concerns\ToModel;
use Carbon\Carbon;

class ImportClosingHours implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $record_date = Carbon::instance( \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[2]) );
        
        $machine_id = request()->machine_id;

        $does_record_exists = MachineWorkingHour::whereDate('created_at' , $record_date)->where('machine_id' , $machine_id)->count();

        $last_inserted_record = MachineWorkingHour::where('machine_id' , $machine_id)->latest()->first();

        if($does_record_exists){
                
                return new RejectedClosingHour([

                    'user_id' => auth()->user()->id,
                    'rejected_data' => json_encode($row),
                    'reason' => 'duplicate_date',

                ]);

        }elseif($last_inserted_record && ($row[0] != $last_inserted_record->closing_hours) ){

            return new RejectedClosingHour([

                'user_id' => auth()->user()->id,
                'rejected_data' => json_encode($row),
                'reason' => 'invalid_closing_hour',

            ]);

        }else{

            return new MachineWorkingHour([
                'machine_id'    => $machine_id , 
                'user_id'       => auth()->user()->id , 
                'opening_hours' => $row[0],
                'closing_hours' => $row[1],
                'entry_source'  => 2,
                'created_at'    => $record_date,
            ]);

        }

    }
}
