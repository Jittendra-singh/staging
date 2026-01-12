<?php

namespace App\Imports;

use App\Models\FuelConsumptionReading;
use Maatwebsite\Excel\Concerns\ToModel;
use Carbon\Carbon;

class ImportFuelReading implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        
        return new FuelConsumptionReading([
            'machine_id'     => request()->machine_id , 
            'user_id'        => auth()->user()->id , 
            'reading_number' => $row[0],
            'fuel_in_liters' => $row[1],
            'entry_source'   => 2,
            'created_at'     => Carbon::instance( \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[2]) ),
        ]);
    }
}
