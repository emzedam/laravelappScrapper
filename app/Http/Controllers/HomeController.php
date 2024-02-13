<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\BrowserKit\HttpBrowser;
use Illuminate\Support\Facades\DB;
use App\Models\DomainTrafic;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $trafics = DomainTrafic::orderBy('id' , 'DESC')->get();
        return view('home' , compact("trafics"));

    }


    public function getExcel(Request $request){
        //give a filename
        $filename = "report.csv";
        //set headers to download file
        header( 'Content-Type: text/csv' );
        header( 'Content-Disposition: attachment;filename='.$filename);
        $file = fopen('php://output', 'w');
        //set the column names
        $cells[] = ["id","Server","User","Name","Size","TodayBw","Date"];
        //pass all the form values
        foreach (DomainTrafic::orderBy('id' , 'DESC')->get() as $value) {
            $cells[] = [$value->id , $value->Server , $value->User , $value->Name, $value->Size, $value->TodayBwDicresed, $value->j_created_at];
        }

        foreach($cells as $cell){
            fputcsv($file,$cell);
        }
        fclose($file);
    }


}
