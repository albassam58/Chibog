<?php

namespace App\Http\Controllers\API\v1;

use App\Events\Chat as ChatNotification;
use App\Http\Controllers\API\v1\BaseController;
use App\Models\Chat;
use Illuminate\Http\Request;

class ChatController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $params = $request->all();

            $query = new Chat();
            $query = $query->where('vendor_id', auth('sanctum')->user()->id)->with(['vendor', 'user']);

            $chats = $this->applySearch($query, $params);

            // read all unread messages
            $filters = json_decode($request->filters);
            Chat::where('vendor_id', auth('sanctum')->user()->id)->where('user_id', $filters->user_id)->where('model', 'User')->where('status', 1)->update([
                'status' => 2
            ]);

            return $this->sendResponse($chats);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function customer(Request $request)
    {
        try {
            $params = $request->all();

            $query = new Chat();
            $query = $query->where('vendor_id', auth('sanctum')->user()->id)
                        ->whereRaw('id in (SELECT max(id) FROM chats WHERE model = "User" GROUP BY user_id )')
                        ->with(['vendor', 'user']);

            $customers = $this->applySearch($query, $params);

            return $this->sendResponse($customers);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
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
        try {
            $userId = $request->userId;

            // save notification before broadcast on socket
            $data = [
                'channel'   => "chat-from-vendor-$userId",
                'vendor_id' => auth('sanctum')->user()->id,
                'user_id'   => $userId,
                'model'     => 'Vendor',
                'message'   => $request->message,
                'payload'   => json_encode($request->all()),
                'status'    => 1 // not yet read
            ];

            $chat = Chat::create($data);

            $notification = [
                'message' => $request->message,
                'data' => $chat->load(['vendor', 'user'])
            ];

            // broadcast an event to notify the consumer
            // this will go to socket.js
            event(new ChatNotification($chat->channel, $notification));

            return $this->sendResponse($chat);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\chat  $chat
     * @return \Illuminate\Http\Response
     */
    public function show(chat $chat)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\chat  $chat
     * @return \Illuminate\Http\Response
     */
    public function edit(chat $chat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\chat  $chat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $chat = Chat::where('vendor_id', auth('sanctum')->user()->id)->where('status', 1)->findOrFail($id);
            $chat->update($request->except('id'));

            return $this->sendResponse($chat);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\chat  $chat
     * @return \Illuminate\Http\Response
     */
    public function destroy(chat $chat)
    {
        //
    }
}
