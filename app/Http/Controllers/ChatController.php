<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\Message;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    use ApiResponseTrait;

    public function getUserChats()
    {
        $user = auth()->user();

        $chats = Chat::where('user_one_id', $user->id)
            ->orWhere('user_two_id', $user->id)
            ->with(['userOne', 'userTwo', 'latestMessage'])
            ->orderByDesc(
                Message::select('created_at')
                    ->whereColumn('chat_id', 'chats.id')
                    ->latest()
                    ->limit(1)
            )
            ->get();

        $chats = $chats->map(function ($chat) use ($user) {
            return [
                'chat_id' => $chat->id,
                'other_user' => $chat->otherUser($user->id),
                'latest_message' => $chat->latestMessage,
                'unseen_count' => $chat->messages()
                    ->where('is_seen', false)
                    ->where('sender_id', '!=', $user->id)
                    ->count(),
            ];
        });

        return $this->successResponse($chats, 'chats', 'user_chats_retrieved_successfully');
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_one_id' => 'required|exists:users,id',
            'user_two_id' => 'required|exists:users,id|different:user_one_id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(__('messages.validation_failed'), 'messages', 422, [
                'errors' => $validator->errors(),
            ]);
        }

        $chat = Chat::where(function ($query) use ($request) {
            $query->where('user_one_id', $request->user_one_id)
                ->where('user_two_id', $request->user_two_id);
        })->orWhere(function ($query) use ($request) {
            $query->where('user_one_id', $request->user_two_id)
                ->where('user_two_id', $request->user_one_id);
        })->first();

        if (! $chat) {
            $chat = Chat::create([
                'user_one_id' => $request->user_one_id,
                'user_two_id' => $request->user_two_id,
            ]);
        }

        return $this->successResponse($chat, 'messages', 'chatـretrievedـsuccessfully');
    }

    public function checkOrCreateChat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'second_user' => 'required|exists:users,id|different:'.auth()->id(),
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(__('messages.validation_failed'), 'messages', 422, [
                'errors' => $validator->errors(),
            ]);
        }

        $authUserId = auth()->id();
        $otherUserId = $request->second_user;

        $chat = Chat::where(function ($query) use ($authUserId, $otherUserId) {
            $query->where('user_one_id', $authUserId)
                ->where('user_two_id', $otherUserId);
        })->orWhere(function ($query) use ($authUserId, $otherUserId) {
            $query->where('user_one_id', $otherUserId)
                ->where('user_two_id', $authUserId);
        })->first();

        if (! $chat) {
            $chat = Chat::create([
                'user_one_id' => $authUserId,
                'user_two_id' => $otherUserId,
            ]);
            $messages = [];
        } else {
            $messages = $chat->messages()->orderBy('created_at', 'asc')->get();
        }

        $data = [
            'chat' => $chat,
            'messages' => $messages,
        ];

        return $this->successResponse($data, 'messages', 'chatـretrievedـsuccessfully');
    }

    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'chat_id' => 'required|exists:chats,id',
            'message' => 'required|string',
        ]);

        $chat = Chat::find($request->chat_id);
        if (! $chat) {
            return $this->errorResponse('Chat not found', 'messages', 404);
        }

        if ($chat->user_one_id !== auth()->id() && $chat->user_two_id !== auth()->id()) {
            return $this->errorResponse('Unauthorized', 'messages', 403);
        }

        if ($validator->fails()) {
            return $this->errorResponse(__('messages.validation_failed'), 'messages', 422, [
                'errors' => $validator->errors(),
            ]);
        }

        $message = Message::create([
            'chat_id' => $request->chat_id,
            'sender_id' => auth()->id(),
            'message' => $request->message,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return $this->successResponse($message, 'messages', 'messageـsendedـsuccessfully');
    }

    public function getMessages($chatId)
    {
        $chat = Chat::with(['messages.sender'])->findOrFail($chatId);

        $messages = $chat->messages->sortBy('created_at')->values();

        Message::where('chat_id', $chatId)
            ->where('sender_id', '!=', auth()->id())
            ->where('is_seen', false)
            ->update([
                'is_seen' => true,
                'read_at' => now(),
            ]);

        return $this->successResponse($messages, 'messages', 'chat_with_messages_retrieved_successfully');
    }
}
