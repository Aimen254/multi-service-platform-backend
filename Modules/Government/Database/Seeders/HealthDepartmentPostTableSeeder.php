<?php

namespace Modules\Government\Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Product;
use App\Models\Business;
use Illuminate\Support\Str;
use App\Models\StandardTag;
use App\Models\TagHierarchy;
use Illuminate\Database\Seeder;
use App\Traits\ModuleSessionManager;
use App\Traits\ProductTagsLevelManager;
use Illuminate\Database\Eloquent\Model;

class HealthDepartmentPostTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $business = Business::where('slug', 'health-department')->first();
        $government = StandardTag::where('slug', 'government')->where('type', 'module')->first();

        // level two
        $health = StandardTag::where('slug', 'health')->whereRelation('businesses', 'id', $business?->id)->first();

        // level three
        $mental_heatlh = StandardTag::where('slug', 'mental-health')->whereRelation('businesses', 'id', $business?->id)->first();

        // level four
        $anxiety_disorders = StandardTag::where('slug', 'anxiety-disorders')->first();
        $eating_disorders = StandardTag::where('slug', 'eating-disorders')->first();

        // level three
        $addiction = StandardTag::where('slug', 'addiction')->whereRelation('businesses', 'id', $business?->id)->first();

        // level four
        $drug_addiction = StandardTag::where('slug', 'drug-addiction')->first();
        $tobacco_addiction = StandardTag::where('slug', 'tobacco-addiction')->first();

        $data = [
            [
                'name' => 'Anxiety disorders are a group of mental health conditions characterized by excessive worry, fear, or anxiety.',
                'description' => "People with Generalized Anxiety Disorder experience excessive, persistent worry about various aspects of their life, such as work, health, or family, even when there is no apparent reason for concern. This chronic worry can lead to physical symptoms like restlessness, muscle tension, and difficulty concentrating.Panic disorder involves recurrent and unexpected panic attacks, which are intense episodes of fear or discomfort that may be accompanied by physical symptoms such as a racing heart, shortness of breath, and sweating. People with panic disorder often worry about when the next attack will occur.Social anxiety disorder is characterized by an intense fear of social situations and scrutiny by others. Individuals with this disorder may avoid social gatherings, public speaking, or other situations where they feel they could be judged or embarrassed.Specific phobias involve a strong and irrational fear of a particular object, situation, or activity. Common phobias include fear of heights (acrophobia), spiders (arachnophobia), and flying (aviophobia). These fears can lead to avoidance behavior.Obsessive-Compulsive Disorder is characterized by intrusive, unwanted thoughts (obsessions) and repetitive behaviors or mental acts (compulsions) performed to reduce anxiety or prevent a feared event. Individuals with OCD often recognize that their thoughts and behaviors are excessive but have difficulty controlling them.",
                'business_id' =>  $business->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1523495338267-31cbca7759e2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8N3x8QW54aWV0eXxlbnwwfHwwfHx8MA%3D%3D&auto=format&fit=crop&w=600&q=60',
                'L1' =>  $government->id,
                'L2' => $health?->id,
                'L3' =>  $mental_heatlh?->id,
                'L4' =>  $anxiety_disorders->id,
            ],
            [
                'name' => 'Eating disorders are complex mental health conditions characterized by abnormal eating habits and a preoccupation with food, body weight, and body shape.',
                'description' => "These disorders can have serious physical, emotional, and social consequences.  Individuals with anorexia nervosa have an intense fear of gaining weight and a distorted body image. They often restrict their food intake, leading to significant weight loss and malnutrition. Other symptoms may include excessive exercise, a preoccupation with calorie counting, and a relentless pursuit of thinness.People with bulimia nervosa engage in recurrent episodes of binge eating, during which they consume large amounts of food in a short period. They then engage in compensatory behaviors, such as vomiting, laxative use, or excessive exercise, to rid their bodies of the calories consumed during the binge. Like anorexia, individuals with bulimia often have a distorted body image.Eating disorders can affect people of all ages, genders, and backgrounds. They often co-occur with other mental health conditions like depression, anxiety, and self-esteem issues. While the exact causes of eating disorders are not fully understood, they are believed to result from a combination of genetic, environmental, psychological, and societal factors.Treatment for eating disorders typically involves a multidisciplinary approach, including medical, nutritional, and psychological interventions. Therapy, such as cognitive-behavioral therapy (CBT), is often a crucial component of treatment, helping individuals address the underlying emotional and psychological issues driving their disordered eating.",
                'business_id' =>  $business->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1424847651672-bf20a4b0982b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8ZWF0aW5nfGVufDB8fDB8fHww&auto=format&fit=crop&w=600&q=60',
                'L1' =>  $government->id,
                'L2' => $health?->id,
                'L3' =>  $mental_heatlh?->id,
                'L4' =>  $eating_disorders->id,
            ],
            [
                'name' => 'Drug addiction, also known as substance use disorder (SUD), is a chronic and complex medical condition characterized by the compulsive use of drugs or substances despite negative consequences.',
                'description' => "It is considered a brain disorder because it often involves changes in the brain's structure and function, which can lead to intense cravings and difficulty controlling drug use. Substance use disorders can affect various aspects of a person's life, including their physical health, mental health, relationships, and overall well-being.Drug addiction can involve a wide range of substances, including but not limited to alcohol, prescription medications, illegal drugs.Several factors can contribute to the development of addiction, including genetic, environmental, and psychological factors. A family history of addiction, exposure to drugs at an early age, and underlying mental health issues can increase the risk. People struggling with addiction deserve understanding, compassion, and access to appropriate treatment and support.",
                'business_id' =>  $business->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1573883429746-084be9b5cfca?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTh8fGRydWd8ZW58MHx8MHx8fDA%3D&auto=format&fit=crop&w=600&q=60',
                'L1' =>  $government->id,
                'L2' =>  $health?->id,
                'L3' =>  $addiction?->id,
                'L4' =>  $drug_addiction->id,
            ],
            [
                'name' => 'Tobacco addiction, also known as nicotine addiction, is a complex and chronic condition characterized by the compulsive use of tobacco products.',
                'description' => "The primary addictive substance in tobacco is nicotine, which is a highly addictive stimulant. When individuals use tobacco products, nicotine rapidly enters the bloodstream and affects the brain, leading to pleasurable sensations and, over time, the development of dependence. Nicotine stimulates the release of neurotransmitters, such as dopamine, which are associated with feelings of pleasure and reward. This reinforcement makes individuals want to use tobacco products repeatedly, leading to dependence. When someone addicted to nicotine tries to quit or reduce their tobacco use, they may experience withdrawal symptoms. These can include irritability, anxiety, difficulty concentrating, and mood swings.Treatment for tobacco addiction typically involves a combination of behavioral therapies and medications. Behavioral therapies can help individuals develop coping strategies and change their smoking habits.Quitting tobacco addiction is a critical step toward improving one's health and reducing the risk of associated diseases. It can be challenging, but with the right support and resources, many people successfully quit smoking and overcome tobacco addiction. It's never too late to seek help and make a positive change for your health.",
                'business_id' =>  $business->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1632094183960-266df564e674?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OHx8dG9iYWNjb3xlbnwwfHwwfHx8MA%3D%3D&auto=format&fit=crop&w=600&q=60',
                'L1' =>  $government->id,
                'L2' =>  $health?->id,
                'L3' =>  $addiction?->id,
                'L4' =>  $tobacco_addiction->id,
            ],
            [
                'name' => 'Panic disorder is a type of anxiety disorder characterized by recurrent and unexpected panic attacks.',
                'description' => "Panic disorder is characterized by recurrent and unexpected panic attacks, which are intense episodes of fear and anxiety. Panic attacks can be accompanied by physical symptoms like a racing heart, shortness of breath, sweating, and trembling. Individuals with panic disorder often worry about having more panic attacks.Panic attacks are sudden, intense periods of fear or discomfort that peak within minutes and often include physical and psychological symptoms. These symptoms can be so severe that individuals may believe they are experiencing a life-threatening emergency, such as a heart attack.",
                'business_id' =>  $business->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1620302044615-3883082d075a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTZ8fGFueGlldHl8ZW58MHx8MHx8fDA%3D&auto=format&fit=crop&w=600&q=60',
                'L1' =>  $government->id,
                'L2' => $health?->id,
                'L3' =>  $mental_heatlh?->id,
                'L4' =>  $anxiety_disorders->id,
            ],
        ];

        foreach ($data as $item) {
            ModuleSessionManager::setModule('government');
            $product = Product::updateOrCreate(['name' => $item['name']], [
                'description' => $item['description'],
                'business_id' => $item['business_id'],
                'status' => $item['status'],
            ]);

            $product->media()->where('type', 'image')->delete();
            $product->media()->create([
                'path' => $item['image'],
                'type' => 'image',
                'is_external' => 1
            ]);

            $product->standardTags()->syncWithoutDetaching([$item['L1']]);
            if ($item['L2']) {
                $product->standardTags()->syncWithoutDetaching([$item['L2']]);
            }
            if ($item['L2'] && $item['L3']) {
                $product->standardTags()->syncWithoutDetaching([$item['L3'], $item['L4']]);
            }
            ProductTagsLevelManager::checkProductTagsLevel($product);
        }
    }
}
