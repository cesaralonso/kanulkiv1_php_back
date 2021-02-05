<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Message;
use App\Condo;

class MessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $messages = Message::all();

        return response()->json(["messages"=>$messages], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $message = new Message;
        $message->tenant_id = $request->tenant_id;
        $message->message = $request->message;
        $message->read = 0;
        $message->save();

        return response()->json(["message"=>$message],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $message = Message::find($id);
        $message->read = 1;
        $message->save();

        return response()->json(["message"=>$message],200);
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
        Message::destroy($id);

        $message = 'Message successfully deleted.';

        return response()->json(['message' => $message], 200);
    }

    public function readMessagesByCondoId($condo_id){
        $read_messages = Condo::find($condo_id)->messages->where('read',1);

        return response()->json(["read_messages"=>$read_messages], 200);
    }

    public function unreadMessagesByCondoId($condo_id){
        $unread_messages = Condo::find($condo_id)->messages->where('read',0);

        return response()->json(["unread_messages"=>$unread_messages], 200);
    }
}
