<?php

namespace App\Http\Controllers;

use App\Events\ChatEvent;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{

    public function index()
    {
        return view('chat.chat');
    }

    public function authId(){
        $user = Auth::user();
        return response()->json($user);
    }
    public function get_chat(Request $request){
        $friend_id = $request->user_id;
        $chat = Chat::where('sender_id',Auth::id())->where('receiver_id',$friend_id)
            ->orWhere('sender_id',$friend_id)->where('receiver_id',Auth::id())
            ->with('user')->get();
        return response()->json($chat);
    }

    public function all_user(){
        $user = User::where('id','<>',Auth::id())->get();
        return response()->json($user);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function send(Request $request)
    {
        $msg = $request->msg;
        $user = User::where('id',Auth::id())->first();
        event(new ChatEvent($msg,$user));

    }


    public function store(Request $request)
    {
        $request->validate([
            'sender_id' => 'required|integer|min:1',
            'receiver_id' => 'required|integer|min:1',
            'message' => 'required'
        ]);

        $chat = new Chat();
        $chat->sender_id = $request->sender_id;
        $chat->receiver_id = $request->receiver_id;
        $chat->message = $request->message;
        if($request->file('attachment')){
            $file =  $request->attachment;
            $file_new_name = time().$file->getClientOriginalName();
            $file->move('/uploads/chat/attachments/',$file_new_name);
        }
        $chat->message = $request->message;
        $chat->save();

        return response()->json(['success'=>'Save Successfully'],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
