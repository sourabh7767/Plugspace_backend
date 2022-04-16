<?php

namespace App\Http\Controllers\Admin;

use App\Providers\RouteServiceProvider;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Flash;
use Illuminate\Support\Facades\Hash;


class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $redirectTo = RouteServiceProvider::ADMINHOME;
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $admins = User::all();
        // dd($admins);
        return view('admin.alladmin.index', compact('admins'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.alladmin.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required'
        ]);
        $ins = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->userType
        ];

        User::create($ins);
        Flash::success('Admin created successfully.');

        return redirect()->route('alladmin.index');
    
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // return view('alladmin.show', compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $admindtl = User::where('id',$id)->first();

        if (empty($admindtl)) {
            Flash::error('Admin not found');

            return redirect(route('alladmin.index'));
        }

        return view('admin.alladmin.edit')->with('admindtl', $admindtl);
        // return view('alladmin.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // dd('test');
        $request->validate([
            'name' => 'required',
            'email' => 'required'
        ]);

        $up = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ];

        User::where('id',$id)->update($up);

        Flash::success('Admin updated successfully.');

        return redirect()->route('alladmin.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::where('id',$id)->delete();

        return redirect()->route('alladmin.index')
            ->with('success', 'Product deleted successfully');
    }
}
