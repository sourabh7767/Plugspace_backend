<?php

namespace App\Http\Controllers;

use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use App\Models\User_Master;

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
    protected $redirectTo = RouteServiceProvider::ADMINHOME;
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $totalUsers = User_Master::count();
        $todayUsers = User_Master::whereDate('created_at',date('Y-m-d'))->count();
        
        $totalOther = User_Master::where('gender','Other')->count();
        $todayOther = User_Master::where('gender','Other')->whereDate('created_at',date('Y-m-d'))->count();

        $totalBiologically1 = User_Master::where('gender','Biologically Male')->count();
        $totalBiologically2 = User_Master::where('gender','Biologically Female')->count();
        $totalBiologically = $totalBiologically1 + $totalBiologically2;
        $todayBiologically1 = User_Master::where('gender','Biologically Female')->whereDate('created_at',date('Y-m-d'))->count();
        $todayBiologically2 = User_Master::where('gender','Biologically Male')->whereDate('created_at',date('Y-m-d'))->count();
        $todayBiologically = $todayBiologically1 + $todayBiologically2;

        $totalTrans1 = User_Master::where('gender','Male')->count();
        $totalTrans2 = User_Master::where('gender','Female')->count();
        $totalTrans = $totalTrans1 + $totalTrans2;
        $todayTrans1 = User_Master::where('gender','Male')->whereDate('created_at',date('Y-m-d'))->count();
        $todayTrans2 = User_Master::where('gender','Female')->whereDate('created_at',date('Y-m-d'))->count();
        $todayTrans = $todayTrans1 + $todayTrans2;
        

        return view('home',compact('totalUsers','todayUsers','totalOther','todayOther','totalBiologically','todayBiologically','totalTrans','todayTrans'));
    }
}
