<?php

namespace Modules\Blogs\Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\StandardTag;
use App\Models\TagHierarchy;
use Illuminate\Database\Seeder;
use App\Traits\ModuleSessionManager;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Database\Eloquent\Model;

class BlogsProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $blog = StandardTag::where('slug', 'blogs')->where('type', 'module')->first();
        $life = StandardTag::where('slug', 'life')->first();
        $relationships = StandardTag::where('slug', 'relationships')->first();
        $friendship = StandardTag::where('slug', 'friendship')->first();
        $self_improvement = StandardTag::where('slug', 'self-improvement')->first();
        $productivity = StandardTag::where('slug', 'productivity')->first();
        $goal_setting = StandardTag::where('slug', 'goal-setting')->first();
        $work = StandardTag::where('slug', 'work')->first();
        $business = StandardTag::where('slug', 'business')->first();
        $entrepreneurship = StandardTag::where('slug', 'entrepreneurship')->first();
        $marketing = StandardTag::where('slug', 'marketing')->first();
        $digital_marketing = StandardTag::where('slug', 'digital-marketing')->first();
        $technology = StandardTag::where('slug', 'technology')->first();
        $artificial_intelligence = StandardTag::where('slug', 'artificial-intelligence')->first();
        $ChatGPT = StandardTag::where('name', 'ChatGPT')->first();




        $businessOwner = User::whereEmail('businessOwner@interapptive.com')->first();
        $businessOwner1 = User::whereEmail('businessOwner1@interapptive.com')->first();
        $businessOwner2 = User::whereEmail('businessOwner2@interapptive.com')->first();

        $data = [
            [
                'name' => 'Friendship is a relationship of mutual affection between people.',
                'description' => 'Friendship is a relationship of mutual affection between people.It is a stronger form of interpersonal bond than an "acquaintance" or an "association", such as a classmate, neighbor, coworker, or colleague.In some cultures, the concept of friendship is restricted to a small number of very deep relationships; in others, such as the U.S. and Canada, a person could have many friends, and perhaps a more intense relationship with one or two people, who may be called good friends or best friends. Other colloquial terms include besties or Best Friends Forever (BFFs). Although there are many forms of friendship, certain features are common to many such bonds, such as choosing to be with one another, enjoying time spent together, and being able to engage in a positive and supportive role to one another.Sometimes friends are distinguished from family, as in the saying "friends and family", and sometimes from lovers (e.g., "lovers and friends"), although the line is blurred with friends with benefits. Similarly, being in the friend zone describes someone who is restricted from rising from the status of friend to that of lover.',
                'user_id' =>  $businessOwner->id,
                'status' => 'active',
                'image' => 'https://plus.unsplash.com/premium_photo-1664271267411-d2e849cc5319?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8ZnJpZW5zaGlwfGVufDB8fDB8fHww&auto=format&fit=crop&w=600&q=60',
                'L1' =>  $blog->id,
                'L2' => $life->id,
                'L3' =>  $relationships->id, 
                'L4' =>   $friendship->id,        
            ],
            [
                'name' => 'Goal setting involves the development of an action plan designed in order to motivate and guide a person or group toward a goal.',
                'description' => "Goals are more deliberate than desires and momentary intentions. Therefore, setting goals means that a person has committed thought, emotion, and behavior towards attaining the goal. In doing so, the goal setter has established a desired future state which differs from their current state thus creating a mismatch which in turn spurs future actions.Goal setting can be guided by goal-setting criteria (or rules) such as SMART criteria.Goal setting is a major component of personal-development and management literature. Studies by Edwin A. Locke and his colleagues, most notably, Gary Latham have shown that more specific and ambitious goals lead to more performance improvement than easy or general goals. The goals should be specific, time constrained and difficult. Vague goals reduce limited attention resources. Unrealistically short time limits intensify the difficulty of the goal outside the intentional level and disproportionate time limits are not encouraging.Difficult goals should be set ideally at the 90th percentile of performance,assuming that motivation and not ability is limiting attainment of that level of performance.As long as the person accepts the goal, has the ability to attain it, and does not have conflicting goals, there is a positive linear relationship between goal difficulty and task performance.",
                'user_id' =>  $businessOwner->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1506784926709-22f1ec395907?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OHx8Z29hbCUyMHNldHRpbmd8ZW58MHx8MHx8fDA%3D&auto=format&fit=crop&w=600&q=60',
                'L1' =>  $blog->id,
                'L2' => $self_improvement->id,
                'L3' =>   $productivity->id, 
                'L4' =>   $goal_setting->id,        
            ],
            [
                'name' => 'Entrepreneurship is the creation or extraction of economic value.',
                'description' => "Entrepreneurship is viewed as change, generally entailing risk beyond what is normally encountered in starting a business, which may include other values than simply economic ones.An entrepreneur is an individual who creates and/or invests in one or more businesses, bearing most of the risks and enjoying most of the rewards.The process of setting up a business is known as 'entrepreneurship'. The entrepreneur is commonly seen as an innovator, a source of new ideas, goods, services, and business/or procedures.More narrow definitions have described entrepreneurship as the process of designing, launching and running a new business, which is often similar to a small business, or as the 'capacity and willingness to develop, organize and manage a business venture along with any of its risks to make a profit.'",
                'user_id' =>  $businessOwner1->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1604933762023-7213af7ff7a7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Nnx8RW50cmVwcmVuZXVyc2hpcHxlbnwwfHwwfHx8MA%3D%3D&auto=format&fit=crop&w=600&q=60',
                'L1' =>  $blog->id,
                'L2' => $work->id,
                'L3' =>  $business->id, 
                'L4' => $entrepreneurship->id,        
            ],
            [
                'name' => 'Digital marketing is the component of marketing that uses the Internet and online-based digital technologies such as desktop computers, mobile phones and other digital media and platforms to promote products and services.',
                'description' => "Its development during the 1990s and 2000s changed the way brands and businesses use technology for marketing. As digital platforms became increasingly incorporated into marketing plans and everyday life,and as people increasingly used digital devices instead of visiting physical shops,digital marketing campaigns have become prevalent, employing combinations of search engine optimization (SEO), search engine marketing (SEM), content marketing, influencer marketing, content automation, campaign marketing, data-driven marketing, e-commerce marketing, social media marketing, social media optimization, e-mail direct marketing, display advertising, e-books, and optical disks and games have become commonplace. Digital marketing extends to non-Internet channels that provide digital media, such as television, mobile phones (SMS and MMS), callbacks, and on-hold mobile ring tones.",
                'user_id' =>  $businessOwner1->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTB8fGRpZ2l0YWwlMjBtYXJrZXRpbmd8ZW58MHx8MHx8fDA%3D&auto=format&fit=crop&w=600&q=60',
                'L1' =>  $blog->id,
                'L2' => $work->id,
                'L3' =>  $marketing->id, 
                'L4' =>  $digital_marketing->id,        
            ],
            [
                'name' => 'ChatGPT (Chat Generative Pre-Trained Transformer) is a large language model-based chatbot developed by OpenAI and launched on November 30, 2022',
                'description' => "ChatGPT (Chat Generative Pre-Trained Transformer) is a large language model-based chatbot developed by OpenAI and launched on November 30, 2022, notable for enabling users to refine and steer a conversation towards a desired length, format, style, level of detail, and language used. Successive prompts and replies, known as prompt engineering, are taken into account at each stage of the conversation as a context.ChatGPT is built upon GPT-3.5 and GPT-4, from OpenAI's proprietary series of foundational GPT models, fine-tuned for conversational applications using a combination of supervised and reinforcement learning techniques. ChatGPT was released as a freely available research preview, but due to its popularity, OpenAI now operates the service on a freemium model. It allows users on its free tier to access the GPT-3.5 based version, while the more advanced GPT-4 based version, as well as priority access to newer features, are provided to paid subscribers under the commercial name 'ChatGPT Plus'.By January 2023, it had become what was then the fastest-growing consumer software application in history, gaining over 100 million users and contributing to OpenAI's valuation growing to US$29 billion.The fine-tuning was accomplished using human trainers to improve the model's performance and, in the case of supervised learning, the trainers played both sides: the user and the AI assistant. In the reinforcement learning stage, human trainers first ranked responses that the model had created in a previous conversation.These rankings were used to create 'reward models' that were used to fine-tune the model further by using several iterations of Proximal Policy Optimization (PPO).",
                'user_id' =>  $businessOwner2->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1684493735679-359868df0e18?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OHx8Y2hhdCUyMGdwdHxlbnwwfHwwfHx8MA%3D%3D&auto=format&fit=crop&w=600&q=60',
                'L1' =>  $blog->id,
                'L2' =>  $technology->id,
                'L3' =>  $artificial_intelligence->id, 
                'L4' =>    $ChatGPT->id,        
            ],
        ];

        foreach($data as $item) {
            ModuleSessionManager::setModule('blogs');
            $product = Product::updateOrCreate(['name' => $item['name']], [
                'description' => $item['description'],
                'user_id' => $item['user_id'],
                'status' => $item['status'],
            ]);

            $product->media()->where('type', 'image')->delete();
            $product->media()->create([
                'path' => $item['image'],
                'type' => 'image',
                'is_external' => 1
            ]);

            $product->standardTags()->syncWithoutDetaching([$item['L1'], $item['L2'], $item['L3'], $item['L4']]);
            ProductTagsLevelManager::checkProductTagsLevel($product);
        }

    }
}
