<?php

namespace App\Http\Controllers\Api\CSV;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Imports\CustomerImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;



class CustomerCSVImportController extends Controller
{
    //csv file import 
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,xlsx,xls',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $import = new CustomerImport();
        Excel::import($import, $request->file('csv_file'));

        return Helper::jsonResponse(true, 'Customers CSV  imported successfully!', 201, $import->insertedCustomers);
    }
}
