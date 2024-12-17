<?php

namespace App\Transformers;

use App\Transformers\Transformer;

class ConversationTransformer extends Transformer
{
    public function transform($conversation, $options = null)
    {
        $user = $conversation->sender ? $conversation->sender : $conversation->receiver;
        $data = [
            'id' => (int) $conversation->id,
            'sender_id' => (int) $conversation->sender_id,
            'reciever_id' => (int) $conversation->reciever_id,
            'module_id' => (int) $conversation->module_id,
            'last_message' => $conversation->message,
            'user' => (new UserTransformer)->transform($user),
            'updated_at' => $conversation->updated_at,
        ];

        if($conversation?->product) {
            $data['product'] = [
                'id' => $conversation->product?->id,
                'uuid' => $conversation->product?->uuid,
                'name' => $conversation->product?->name,
            ];
        }

        return $data;
    }
}
