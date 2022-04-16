<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SampleMessage;

class MessagesController extends Controller
{
    public function listMessage()
    {
        $messages = SampleMessage::orderBy('id','DESC')->get();
        return view('admin.messages.index',compact('messages'))->with('i', (request()->input('page', 1) - 1) * 5);
    }
    
    public function addMessage(Request $request)
    {
        SampleMessage::create(['message'=>$request['message']]);
        Flash::success('Message create successfully.');

        return  redirect('admin/listMessage');
    } 

    public function editMessage(Request $request)
    {
        $message = SampleMessage::where('id',$request['id'])->first();
        
        return response()->json(
        [
            'success' => true,
            'data' => $message
        ]);  
    }

    public function updateMessage(Request $request)
    {
        SampleMessage::where('id',$request['id'])->update(['message'=>$request['text']]);
        //Flash::success('Message updated successfully.');
        echo "Message updated successfully.";
        return redirect('admin/listMessage');
    }

    public function deleteMessage(Request $request)
    {
       $id = $request->id;
       SampleMessage::where('id',$id)->delete();
       echo "Message deleted successfully.";
    }
}
