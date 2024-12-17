<?php

namespace Modules\Automotive\Database\Seeders;

use App\Models\User;
use App\Models\StandardTag;
use App\Models\Conversation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;

class AutomotiveConversationSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $businessOwner = User::whereEmail('businessOwner@interapptive.com')->first();
        $customer1 = User::whereEmail('customer1@interapptive.com')->first();
        $customer2 = User::whereEmail('customer2@interapptive.com')->first();
        $customer3 = User::whereEmail('customer3@interapptive.com')->first();
        $customer4 = User::whereEmail('customer4@interapptive.com')->first();
        $customer5 = User::whereEmail('customer5@interapptive.com')->first();

        $customers = [$customer1, $customer2, $customer3, $customer4, $customer5];

        $module = StandardTag::where('slug', 'automotive')->firstOrFail();

        foreach ($customers as $customer) {
            $conversation = Conversation::updateOrCreate([
                'sender_id' => $customer->id,
                'reciever_id' => $businessOwner->id,
                'module_id' => $module->id,

            ], [
                'message' => $this->generateRandomMessage(),
            ]);
            // call chat api to start conversation
            $response = Http::post(env('CHAT_API_URL') . '/chat', [
                'sender_id' => $conversation?->sender_id,
                'reciever_id' => $conversation?->reciever_id,
                'module_id' => $conversation?->module_id,
                'message' => $conversation->message,
                'room_id' => $conversation->id,
            ]);
            if ($response->successful()) {
                Log::info('success');
            }
            // call api to send conversation to node server
        }
    }

    private function generateRandomMessage()
    {
        $messages = [
            "Hello, I have a question.",
            "Can you help me with this?",
            "I need assistance regarding your product/service.",
            "Is there someone available to chat?",
            "Just checking in.",
        ];

        return $messages[array_rand($messages)];
    }
}
