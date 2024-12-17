<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use App\Models\Conversation;
use Illuminate\Support\Facades\Log;

class UpdateConversations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $conversations = [];
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($conversations)
    {
        $this->conversations = $conversations;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->conversations as $value){
           $conversation = Conversation::where('module_id', $value['module_id'])->where(function($query) use($value) {
            $query->whereIn('sender_id', [$value['sender_id'], $value['reciever_id']]);
           })->where(function($query) use ($value) {
            $query->whereIn('reciever_id', [$value['sender_id'], $value['reciever_id']]);
           })->first();
           if($conversation) {
            $conversation->update([
                'sender_id' => $value['sender_id'],
                'reciever_id' => $value['reciever_id'],
                'message' => $value['message'],
                'module_id' => $value['module_id'],
                'updated_at' => Carbon::parse($value['created_at'])
            ]);
           } else {
            Conversation::create([
                'sender_id' => $value['sender_id'],
                'reciever_id' => $value['reciever_id'],
                'message' => $value['message'],
                'module_id' => $value['module_id'],
                'updated_at' => Carbon::parse($value['created_at'])
            ]);
           }
        }
    }
}
