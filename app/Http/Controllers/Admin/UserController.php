<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User_Master;
use App\Models\User_Media_Master;
use App\Models\Community_Master;
use App\Models\Feed_Master;
use App\Models\Likes_Master;
use App\Models\ViewProfile;
use App\Models\PlugspaceUser;
use App\Models\Story_Master;
use App\Models\Story_Media_Master;
use App\Http\Controllers\AppBaseController;

use App\Models\Notification_Master;
use App\Models\ViewStory;
use App\Models\Music_Master;
use App\Models\Music_Likes_Master;
use App\Models\Story_Comment_Master;
use App\Models\Report_Master;
use Flash;
use DateTime;

class UserController extends Controller
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
    public function index_test(){
        echo "hello";die;
    }
    public function index()
    {

        $users = User_Master::select("*")->orderBy('user_id', 'desc')->get();
        // dd($users->toArray());

        foreach ($users as $key => $value) {
            $userDtl = User_Media_Master::where('user_id',$value->user_id)->first();
            
            if(!empty($userDtl)){
                $value->profile = $userDtl->profile; 
            }else{
                $value->profile = env('PUBLIC_PATH').'images/no-images.jpg'; 
            }
        }
        return view('admin.user.index', compact('users'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function allUser(Request $request)
    {
        $type = $request['type'];

        if($type == '1'){
            $users = User_Master::select("*")->orderBy('user_id', 'desc')->get();

        }elseif($type == '2'){

            $users1 = User_Master::where('gender','Biologically Female')->orderBy('user_id', 'desc')->get();
              $users2 = User_Master::where('gender','Biologically Male')->orderBy('user_id', 'desc')->get();
           $users = $users1->toBase()->merge($users2->toBase());
            
        }elseif($type == '3'){
            $users1 = User_Master::where('gender','Female')->orderBy('user_id', 'desc')->get();
            $users2 = User_Master::where('gender','Male')->orderBy('user_id', 'desc')->get();
           $users = $users1->toBase()->merge($users2->toBase());

        }elseif($type == '4'){
            $users = User_Master::select("*")->where('gender','Other')->orderBy('user_id', 'desc')->get();

        }elseif($type == '5'){
            $users = User_Master::select("*")->whereDate('created_at',date('Y-m-d'))->orderBy('user_id', 'desc')->get();

        }elseif($type == '6'){
            $users1 = User_Master::where('gender','Biologically Female')->whereDate('created_at',date('Y-m-d'))->orderBy('user_id', 'desc')->get();
              $users2 = User_Master::where('gender','Biologically Male')->whereDate('created_at',date('Y-m-d'))->orderBy('user_id', 'desc')->get();
           $users = $users1->toBase()->merge($users2->toBase());


        }elseif($type == '7'){
            $users1 = User_Master::where('gender','Female')->whereDate('created_at',date('Y-m-d'))->orderBy('user_id', 'desc')->get();
            $users2 = User_Master::where('gender','Male')->whereDate('created_at',date('Y-m-d'))->orderBy('user_id', 'desc')->get();
           $users = $users1->toBase()->merge($users2->toBase());


        }elseif($type == '8'){
            $users = User_Master::select("*")->where('gender','Other')->whereDate('created_at',date('Y-m-d'))->orderBy('user_id', 'desc')->get();

        }else{
            $users = User_Master::select("*")->orderBy('user_id', 'desc')->get();
        }

        foreach ($users as $key => $value) {
            $userDtl = User_Media_Master::where('user_id',$value->user_id)->first();
            
            if(!empty($userDtl)){
                $value->profile = $userDtl->profile; 
            }else{
                $value->profile = env('PUBLIC_PATH').'images/no-images.jpg'; 
            }
        }

        return view('admin.user.index', compact('users'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.create');
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
            'description' => 'required',
            'price' => 'required'
        ]);

        Product::create($request->all());

        return redirect()->route('user.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('user.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('user.edit', compact('product'));
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
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required'
        ]);
        $product->update($request->all());

        return redirect()->route('user.index')
            ->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         $product->delete();

        return redirect()->route('user.index')
            ->with('success', 'Product deleted successfully');
    }

    public function plugspaceRank(Request $request)
    {
        $user_id = $request['user_id'];
        $plugspace_rank = $request['plugspace_rank'];
        User_Master::where('user_id',$user_id)->update(['plugspace_rank'=>$plugspace_rank]);
        echo "PlugSpace rank has been allocated successfully!";
    }
    
    public function plugspaceUser()
    {
        $userDtl = plugspaceUser::orderBy('id','DESC')->get();
        return view('admin.plugspace_user.index',compact('userDtl'));
    }
    
    public function addUser(Request $request)
    {
       
        $name = count($request['name']);
        $countDtl = PlugspaceUser::where(['rank'=>$request['rank'],'gender'=>$request['gender']])->count();
            
        if($countDtl > 10){
            Flash::info('You can create only 10 users.');
            return  redirect('admin/plugspaceUser');
        }
        
        for($i=0;$i<$name;$i++){
            $countDtl = PlugspaceUser::where(['rank'=>$request['rank'],'gender'=>$request['gender']])->count();
            if($countDtl < 10){
                PlugspaceUser::create(['name'=>$request['name'][$i],'rank'=>$request['rank'],'gender'=>$request['gender']]);
            }
        }
        
        Flash::success('User create successfully.');

        return  redirect('admin/plugspaceUser');
    } 

    public function editUser(Request $request)
    {
        $userDtl = PlugspaceUser::where('id',$request['id'])->first();
        
        return response()->json(
        [
            'success' => true,
            'data' => $userDtl
        ]);  
    }

    public function updateUser(Request $request)
    {
        PlugspaceUser::where('id',$request['id'])->update(['name'=>$request['name'],'rank'=>$request['rank'],'gender'=>$request['gender']]);
        Flash::success('User updated successfully.');
        return  redirect('admin/plugspaceUser');
    }

    public function deleteUser(Request $request)
    {
       $id = $request->id;
       PlugspaceUser::where('id',$id)->delete();
       echo "User deleted successfully.";
    }

    public function deleteUsers(Request $request)
    {
       $user_id = $request->user_id;
       User_Master::where('user_id',$user_id)->delete();
       Story_Master::where('user_id',$user_id)->delete();
       Feed_Master::where('user_id',$user_id)->delete();
       Likes_Master::where('user_id',$user_id)->delete();
       Likes_Master::where('like_user_id',$user_id)->delete();
       User_Media_Master::where('user_id',$user_id)->delete();
       ViewProfile::where('user_id',$user_id)->delete();
       ViewProfile::where('view_user_id',$user_id)->delete();
       ViewStory::where('view_user_id',$user_id)->delete();
       
       echo "User deleted successfully.";
    }

    public function userDetails(Request $request)
    {
        $user_id = $request['user_id'];
        $userDtl = User_Master::where('user_id',$user_id)->first();
        $dob = $userDtl->dob;
        $condate = date("Y-m-d");
        $birthdate = new DateTime(date("Y-m-d",  strtotime(implode('-', array_reverse(explode('/', $dob))))));
        $today= new DateTime(date("Y-m-d",  strtotime(implode('-', array_reverse(explode('/', $condate))))));           
        $age = $birthdate->diff($today)->y;
        $userDtl['age'] = (string)$age;
                
        $mediaDtl = User_Media_Master::where('user_id',$user_id)->get();
        foreach($mediaDtl as $key => $value){
            $value->description = '';  
        }
        $feedDtl = Feed_Master::where('user_id',$user_id)->get();
        foreach($feedDtl as $key => $value){
          $value->type = 'feed';
        }
        
        $collection = collect($mediaDtl);
        $merged     = $collection->merge($feedDtl);
        $userMediaDtl   = $merged->all();
        $userDtl['media_detail']  = $userMediaDtl; 
        
        $storyDtl = Story_Master::where('user_id',$user_id)->pluck('story_id')->first();
        
        if($storyDtl != ''){
            $userStoryDtl = Story_Media_Master::where('story_id',$storyDtl)->get(); 
            $userDtl['story_detail']  = $userStoryDtl; 
        } else{
            $userDtl['story_detail']  = []; 
        }
       
        $userDtl['view'] = ViewProfile::where('view_user_id',$user_id)->count();   
        $userDtl['likes'] = Likes_Master::where('like_user_id',$user_id)->where('like_type','1')->count(); 
           
        return view('admin.user.view',compact('userDtl'));
    }

    public function userStatus(Request $request)
    {
        $user_id = $request['user_id'];
        User_Master::where('user_id',$user_id)->update(['status'=>$request['status']]);
        
        echo "User status change successfully.";
    }

    public function removeMedia(Request $request)
    {
       $type = $request['type'];
       $id = $request['id'];

       if($type == 'feed'){
         Feed_Master::where('feed_id',$id)->delete();  
       }elseif($type == 'profile'){
         User_Media_Master::where('media_id',$id)->delete();  
       }elseif($type == 'story'){
         Story_Media_Master::where('story_media_id',$id)->delete();  
       }  


       echo "Media remove successfully.";
    }
    
    public function removeUsers(Request $request)
    {
        $id = $request['id'];
        PlugspaceUser::where('id',$id)->delete();
        
       echo "User deleted successfully.";
    }
    
    
}
