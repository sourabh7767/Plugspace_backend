<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rank_Text_Master;
use App\Models\User_Media_Master;
use App\Models\Community_Master;
use App\Models\PlugspaceUser;
use App\Http\Controllers\AppBaseController;
use Flash;

class RankingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function plugspaceText()
    {
        $userDtl = Rank_Text_Master::orderBy('id','DESC')->get();
        return view('admin.plugspace_text.index',compact('userDtl'));
    }
    
    public function addText(Request $request)
    {
        Rank_Text_Master::create(['text'=>$request['text'],'rank'=>$request['rank'],'name' => $request['name']]);
        Flash::success('Characteristics create successfully.');

        return  redirect('admin/plugspaceText');
    } 

    public function editText(Request $request)
    {
        $userDtl = Rank_Text_Master::where('id',$request['id'])->first();
        
        return response()->json(
        [
            'success' => true,
            'data' => $userDtl
        ]);  
    }

    public function updateText(Request $request)
    {
        Rank_Text_Master::where('id',$request['id'])->update(['name'=>$request['name'],'rank'=>$request['rank'],'text'=>$request['text']]);
        Flash::success('Characteristics updated successfully.');
        return  redirect('admin/plugspaceText');
    }

    public function deleteText(Request $request)
    {
       $id = $request->id;
       Rank_Text_Master::where('id',$id)->delete();
       echo "Characteristics deleted successfully.";
    }
 
}
