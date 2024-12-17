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

class CareerCrafterPostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $business = Business::where('slug', 'careercrafters-inc')->first();
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
                'name' => ' Frontend Developer - AngularJS',
                'description' => "We are looking for a talented Frontend Developer with strong expertise in AngularJS to join our team. As a Frontend Developer, you will be responsible for designing and implementing user interfaces for our web applications.Responsibilities: Develop user interfaces using AngularJS, HTML, CSS, and JavaScript.Collaborate with the design team to create responsive and visually appealing web applications.Optimize web applications for maximum performance and scalability.Ensure the technical feasibility of UI/UX designs.Collaborate with backend developers to integrate frontend code with server-side logic.Qualifications: Proven experience as a Frontend Developer with expertise in AngularJS.Proficient in HTML5, CSS3, and JavaScript.Strong understanding of web standards and responsive design principles.Experience with RESTful APIs and AJAX.Knowledge of version control systems (e.g., Git).",
                'business_id' =>  $business->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1561736778-92e52a7769ef?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8M3x8YW5ndWxhciUyMCUyMGpzfGVufDB8fDB8fHww&auto=format&fit=crop&w=600&q=60',
                'L1' =>  $employment->id,
                'L2' => $technology->id,
                'L3' =>  $software_development->id,
                'L4' =>  $frontend_developer->id,
            ],
            [
                'name' => 'Backend Developer (Python)',
                'description' => "We are seeking a skilled Backend Developer with expertise in Python to join our dynamic team. As a Backend Developer, you will be responsible for designing, implementing, and maintaining the server-side components of our web applications and services. You will work closely with our front-end developers, product managers, and other stakeholders to deliver high-quality software solutions.Responsibilities: Develop, test, and deploy server-side applications using Python.Collaborate with front-end developers to integrate user-facing elements with server-side logic.Design and implement data storage solutions and databases.Create and maintain RESTful APIs for communication with front-end applications.Optimize applications for maximum speed and scalability.Collaborate with cross-functional teams to define, design, and ship new features.Debug and resolve issues reported by users or quality assurance teams.Requirements: Bachelor's degree in Computer Science, Engineering, or a related field (or equivalent work experience).Proven experience as a Backend Developer with a focus on Python.trong understanding of web development principles and best practices.Proficiency in Python and related frameworks (e.g., Django, Flask).Experience with relational and non-relational databases.Knowledge of RESTful web services and API development.Familiarity with version control systems (e.g., Git).",
                'business_id' =>  $business->id,
                'status' => 'active',
                'image' => 'https://plus.unsplash.com/premium_photo-1661369089329-d89031837cdf?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8cHl0aG9uJTIwbGFuZ3VhZ2V8ZW58MHx8MHx8fDA%3D&auto=format&fit=crop&w=600&q=60',
                'L1' =>   $employment->id,
                'L2' => $technology->id,
                'L3' =>  $software_development->id,
                'L4' =>  $backend_developer->id,
            ],
            [
                'name' => 'Expert Network Security Engineer',
                'description' => "CareerCrafters Inc seeking a talented and motivated Network Security Engineer to join our cybersecurity team. As a Network Security Engineer, you will play a crucial role in protecting our organization's network infrastructure from cyber threats and ensuring the confidentiality, integrity, and availability of our data.Responsibilities: Design, implement, and maintain network security solutions, including firewalls, intrusion detection systems, and VPNs.Monitor network traffic for signs of security threats and vulnerabilities.Conduct security assessments and penetration testing to identify weaknesses in our network.Collaborate with cross-functional teams to develop and implement security policies and procedures.Investigate and respond to security incidents and breaches.Stay up-to-date with the latest cybersecurity threats and technologies to proactively address emerging risks.Provide guidance and support to junior members of the team.",
                'business_id' =>  $business->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1573495612890-430e48b164df?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8bmV0d29yayUyMHNlY3VyaXR5JTIwZW5naW5lZXJ8ZW58MHx8MHx8fDA%3D&auto=format&fit=crop&w=600&q=60',
                'L1' =>  $employment->id,
                'L2' => $technology->id,
                'L3' =>  $information_security->id,
                'L4' =>  $network_security_engineer->id,
            ],
            [
                'name' => 'Junior Network Security Engineer',
                'description' => "As a Junior Network Security Engineer, you will play a crucial role in protecting our organization's network infrastructure and ensuring the confidentiality, integrity, and availability of our data. You will work closely with senior engineers and IT professionals to monitor network security, respond to incidents, and implement security best practices.Responsibilities: Continuously monitor network traffic for suspicious activity and security threats.Assist in responding to security incidents, including analyzing logs, identifying vulnerabilities, and mitigating threats.Configure and maintain firewall rules and intrusion detection/prevention systems to safeguard the network.Manage user access and permissions, ensuring only authorized individuals have access to critical systems and data.Implement and enforce security policies and standards across the network infrastructure.Conduct regular vulnerability assessments and penetration testing to identify weaknesses and recommend remediation actions.",
                'business_id' =>  $business->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTh8fHRlY2hub2xvZ3l8ZW58MHx8MHx8fDA%3D&auto=format&fit=crop&w=600&q=60',
                'L1' =>  $employment->id,
                'L2' => $technology->id,
                'L3' =>  $information_security->id,
                'L4' =>  $network_security_engineer->id,
            ],
            [
            'name' => ' Security Analyst - Entry Level',
            'description' => "CareerCrafters Inc is seeking a motivated and talented individual to join our team as an Entry-Level Security Analyst. In this role, you will work closely with our experienced security professionals to monitor, analyze, and respond to security threats. You will have the opportunity to learn and grow in a dynamic cybersecurity environment.Responsibilities:Monitor security alerts and incidents using SIEM tools.Conduct security investigations and root cause analysis.Assist in vulnerability assessments and penetration testing.Collaborate with the incident response team to mitigate security incidents.Stay up-to-date with the latest cybersecurity threats and trends.Participate in security awareness and training programs.Qualifications:Bachelor's degree in a related field (e.g., Cybersecurity, Computer Science).Strong analytical and problem-solving skills.Basic understanding of networking and operating systems.Excellent communication and teamwork skills.Ability to obtain relevant security certifications (e.g., CompTIA Security+, Certified Information Systems Security Professional - CISSP).",
            'business_id' =>  $business->id,
            'status' => 'active',
            'image' => 'https://images.unsplash.com/photo-1573164713988-8665fc963095?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OHx8bmV0d29yayUyMHNlY3VyaXR5JTIwZW5naW5lZXJ8ZW58MHx8MHx8fDA%3D&auto=format&fit=crop&w=600&q=60',
            'L1' => $employment->id,
            'L2' => $technology->id,
            'L3' =>  $information_security->id,
            'L4' =>  $security_analyst->id,
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
