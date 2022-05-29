<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;

class ReportController extends Controller
{
    public function PdfReport(array $data){
        return response()->json($data);
        die();
                  
        $pdf = PDF::loadView('myPDF', $data);
    
        return $pdf->download(Carbon::today()->toDateTimeString()+'.pdf');

    }
}
