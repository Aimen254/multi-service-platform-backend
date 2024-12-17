<?php

namespace Modules\Employment\Database\Seeders;

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

class TalentHubPostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $business = Business::where('slug', 'talenthub-solutions')->first();
        $employment = StandardTag::where('slug', 'employment')->where('type', 'module')->first();

        $technology = StandardTag::where('slug', 'technology')->first();


        $software_development = StandardTag::where('slug', 'software-development')->first();
        $information_security = StandardTag::where('slug', 'information-security')->first();

        $frontend_developer = StandardTag::where('slug', 'frontend-developer')->first();
        $backend_developer = StandardTag::where('slug', 'backend-developer')->first();
        $security_analyst = StandardTag::where('slug', 'security-analyst')->first();
        $network_security_engineer = StandardTag::where('slug', 'network-security-engineer')->first();

        $data = [
            [
                'name' => 'Frontend Developer (Vue.js)',
                'description' => "TalentHub Solutions is seeking a talented Frontend Developer with expertise in Vue.js to join our dynamic team. As a Frontend Developer, you will be responsible for creating and implementing innovative web applications, collaborating with our design and backend teams to deliver exceptional user experiences.Responsibilities: Develop responsive web applications using Vue.js and other frontend technologies. Collaborate with designers and backend developers to implement user-friendly interfaces. Optimize web applications for performance and scalability. Debug and resolve frontend issues and bugs. Qualifications: 2+ years of experience in frontend development.Proficiency in Vue.js and its ecosystem.Strong HTML5, CSS3, and JavaScript skills.Familiarity with RESTful APIs and AJAX.Experience with version control systems (e.g., Git).",
                'business_id' =>  $business->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1644938297138-fde22c59b32c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OHx8dnVlJTIwanMlMjBmcm9udGVuZHxlbnwwfHwwfHx8MA%3D%3D&auto=format&fit=crop&w=600&q=60',
                'L1' =>  $employment->id,
                'L2' => $technology->id,
                'L3' =>  $software_development->id,
                'L4' =>  $frontend_developer->id,
            ],
            [
                'name' => 'Frontend Developer (React.js)',
                'description' => "Are you a passionate frontend developer with expertise in React.js? We're looking for a talented individual to join our team and help us build cutting-edge web applications. As a Frontend Developer at TalentHub Solutions, you will collaborate with our design and backend teams to create seamless user experiences. If you're excited about working on innovative projects and pushing the boundaries of web development, we'd love to hear from you!.Responsibilities:Develop responsive web applications using React.js.Collaborate with designers to implement UI/UX designs.Optimize web applications for maximum speed and scalability.Ensure cross-browser compatibility and mobile responsiveness.Troubleshoot and debug issues as they arise",
                'business_id' =>  $business->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1566837945700-30057527ade0?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Nnx8cmVhY3QlMjBqc3xlbnwwfHwwfHx8MA%3D%3D&auto=format&fit=crop&w=600&q=60',
                'L1' =>   $employment->id,
                'L2' => $technology->id,
                'L3' =>  $software_development->id,
                'L4' =>  $frontend_developer->id,
            ],
            [
                'name' => 'Network Security Engineer',
                'description' => "A Network Security Engineer is responsible for designing, implementing, and maintaining an organization's network security infrastructure to protect against cyber threats and ensure the confidentiality, integrity, and availability of data and resources.Key Responsibilities:Design and implement network security solutions such as firewalls, intrusion detection/prevention systems, VPNs, and access control lists.Develop and enforce network security policies and procedures to ensure compliance with industry standards and regulatory requirements.Monitor network traffic for suspicious activities, investigate security incidents, and respond to security breaches.Conduct regular vulnerability assessments and penetration testing to identify and mitigate security weaknesses.Monitor network traffic, logs, and events to detect and respond to security threats in real-time.Ensure timely application of security patches and updates to network devices and software.",
                'business_id' =>  $business->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1584433144859-1fc3ab64a957?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTJ8fG5ldHdvcmslMjBzZWN1cml0eXxlbnwwfHwwfHx8MA%3D%3D&auto=format&fit=crop&w=600&q=60',
                'L1' =>  $employment->id,
                'L2' => $technology->id,
                'L3' =>  $information_security->id,
                'L4' =>  $network_security_engineer->id,
            ],
            [
                'name' => 'Backend Developer (PHP/Laravel)',
                'description' => "We are seeking a talented Backend Developer with a strong background in PHP and Laravel to join our growing team. As a Backend Developer, you will be responsible for designing, developing, and maintaining the server-side logic of our web applications. You will work closely with the front-end developers and other stakeholders to ensure a seamless user experience.Responsibilities: Design and develop server-side applications using PHP and Laravel.Collaborate with the front-end development team to integrate user-facing elements with server-side logic.Optimize and maintain the performance of the web applications.Debug and resolve technical issues as they arise.Collaborate with cross-functional teams to define and implement new features.Qualifications:Bachelor's degree in Computer Science or related field.Proven experience as a Backend Developer with expertise in PHP and Laravel.Strong knowledge of database design and SQL.Familiarity with front-end technologies (HTML, CSS, JavaScript) is a plus.Excellent problem-solving skills and attention to detail.",
                'business_id' =>  $business->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1488590528505-98d2b5aba04b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8UEhQJTJGTGFyYXZlbHxlbnwwfHwwfHx8MA%3D%3D&auto=format&fit=crop&w=600&q=60',
                'L1' => $employment->id,
                'L2' => $technology->id,
                'L3' =>  $software_development->id,
                'L4' =>  $backend_developer->id,
            ],
            [
            'name' => 'Backend Developer (Node.js)',
            'description' => "TalentHub Solutions is a dynamic and innovative tech company dedicated to revolutionizing [brief description of your industry]. We are seeking a talented Backend Developer with expertise in Node.js to join our growing team. If you are passionate about building scalable and efficient backend systems, we want to hear from you!.Job Description: As a Backend Developer at [Your Company Name], you will play a key role in designing, developing, and maintaining our backend systems. You will work closely with our cross-functional teams to ensure the seamless integration of our applications and services.Responsibilities:Develop and maintain backend services using Node.js. Collaborate with front-end developers to design and implement APIs. Optimize the performance and scalability of our backend systems. Troubleshoot and resolve bugs and technical issues. Participate in code reviews and provide constructive feedback",
            'business_id' =>  $business->id,
            'status' => 'active',
            'image' => 'https://images.unsplash.com/photo-1592609931095-54a2168ae893?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8bm9kZSUyMGpzfGVufDB8fDB8fHww&auto=format&fit=crop&w=600&q=60',
            'L1' => $employment->id,
            'L2' => $technology->id,
            'L3' =>  $software_development->id,
            'L4' =>  $backend_developer->id,
            ],
        ];

        foreach($data as $item) {
            ModuleSessionManager::setModule('employment');
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

            $product->standardTags()->syncWithoutDetaching([$item['L1'], $item['L2'], $item['L3'], $item['L4']]);
            ProductTagsLevelManager::checkProductTagsLevel($product);
        }
    }
}
