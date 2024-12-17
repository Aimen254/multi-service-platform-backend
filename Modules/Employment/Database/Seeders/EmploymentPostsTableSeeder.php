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

class EmploymentPostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $business = Business::where('slug', 'job-crafters')->first();
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
                'name' => 'Network Security Engineer',
                'description' => "Our company is seeking a skilled and motivated Network Security Engineer to join our growing team. As a Network Security Engineer, you will play a critical role in designing, implementing, and maintaining our network security infrastructure. You will work to ensure the confidentiality, integrity, and availability of our network resources while staying ahead of evolving cyber threats.Responsibilities: Design, implement, and manage network security solutions, including firewalls, intrusion detection/prevention systems, and VPNs.Conduct security assessments and vulnerability scans to identify and mitigate risks.Collaborate with cross-functional IT teams to enhance security policies and procedures.Monitor network traffic for anomalies and respond to security incidents in a timely manner.Perform regular security audits and assessments to ensure compliance with industry standards.Stay up-to-date with emerging threats and security technologies. Requirements: Bachelor's degree in Computer Science, Information Technology, or related field.Certified Information Systems Security Professional (CISSP) or Certified Network Security Professional (CNSP) certification preferred.Proven experience in network security engineering, including firewall configuration and monitoring.Strong knowledge of networking protocols and security technologies.",
                'business_id' =>  $business->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1506399558188-acca6f8cbf41?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MzB8fG5ldHdvcmslMjBzZWN1cml0eXxlbnwwfHwwfHx8MA%3D%3D&auto=format&fit=crop&w=600&q=60',
                'L1' =>  $employment->id,
                'L2' => $technology->id,
                'L3' =>  $information_security->id,
                'L4' =>   $network_security_engineer->id,
            ],
            [
                'name' => 'Security Analyst',
                'description' => "Our company seeking a highly skilled Security Analyst to strengthen our cybersecurity defenses and protect our organization's critical assets. As a Security Analyst, you will be responsible for analyzing security data, identifying vulnerabilities, and responding to security incidents to ensure the confidentiality, integrity, and availability of our information systems.Responsibilities: Monitor security alerts and incidents, investigate root causes, and provide timely resolution. Perform regular security assessments, vulnerability scans, and penetration tests. Perform regular security assessments, vulnerability scans, and penetration tests. Collaborate with IT and DevOps teams to enhance system security.Conduct security awareness training for employees.Requirements:Bachelor's degree in Computer Science, Information Security, or a related field.Certified Information Systems Security Professional (CISSP) or Certified Information Security Manager (CISM) certification preferred. Proven experience in security analysis, incident response, and risk assessment.",
                'business_id' =>  $business->id,
                'status' => 'active',
                'image' => 'https://plus.unsplash.com/premium_photo-1661878265739-da90bc1af051?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8N3x8U2VjdXJpdHklMjBBbmFseXN0fGVufDB8fDB8fHww&auto=format&fit=crop&w=600&q=60',
                'L1' =>   $employment->id,
                'L2' => $technology->id,
                'L3' =>  $information_security->id,
                'L4' =>  $security_analyst->id,
            ],
            [
                'name' => 'Frontend Developer',
                'description' => "Our company is looking for a talented Frontend Developer to join our remote team. As a Frontend Developer, you will be responsible for creating and maintaining visually stunning and user-friendly web applications. You will collaborate closely with our design and backend development teams to deliver high-quality user interfaces that delight our customers.Responsibilities: Develop responsive web applications using HTML, CSS, and JavaScript. Collaborate with UI/UX designers to implement pixel-perfect designs. Optimize web applications for performance, scalability, and cross-browser compatibility. Debug and resolve frontend issues and ensure smooth user experiences. Stay up-to-date with the latest frontend development trends and best practices. Requirements: Bachelor's degree in Computer Science, Web Development, or a related field (or equivalent work experience). Proven experience as a Frontend Developer, including expertise in HTML, CSS, and JavaScript. Proficiency in modern frontend frameworks and libraries (e.g., React, Angular, or Vue.js). Strong understanding of responsive design principles.",
                'business_id' =>  $business->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1531493657527-6d1af0c4c593?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MjB8fGZyb250ZW5kJTIwZGV2ZWxvcGVyfGVufDB8fDB8fHww&auto=format&fit=crop&w=600&q=60',
                'L1' =>  $employment->id,
                'L2' => $technology->id,
                'L3' =>  $software_development->id,
                'L4' =>  $frontend_developer->id,
            ],
            [
                'name' => 'Backend Developer',
                'description' => "Our company is looking for a talented Backend Developer to join our innovative team. As a Backend Developer, you will be responsible for designing, developing, and maintaining the server-side logic, databases, and APIs that power our cutting-edge software applications. Responsibilities: Design, develop, and maintain scalable and efficient server-side applications. Collaborate with front-end developers to integrate user-facing elements with server-side logic. Optimize applications for maximum speed and scalability. Implement data security and user authentication measures. Work closely with the DevOps team to ensure smooth deployment and continuous integration. Troubleshoot and debug issues to ensure the reliability and performance of our applications. Requirements: Bachelor's degree in Computer Science, Software Engineering, or a related field. Proven experience as a Backend Developer or similar role. Experience with RESTful API design and implementation. Excellent problem-solving and communication skills.",
                'business_id' =>  $business->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1595617795501-9661aafda72a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8YmFja2VuZCUyMGRldmVsb3BlcnxlbnwwfHwwfHx8MA%3D%3D&auto=format&fit=crop&w=600&q=60',
                'L1' => $employment->id,
                'L2' => $technology->id,
                'L3' =>  $software_development->id,
                'L4' =>  $backend_developer->id,
            ],
            [
                'name' => 'Security Analyst (Physical Security)',
                'description' => "Our company is seeking a dedicated Security Analyst to help us maintain a safe and secure environment for our clients' facilities. As a Security Analyst, you will be responsible for overseeing physical security measures, access control systems, and emergency response protocols. Responsibilities: Monitor and manage access control systems, including issuing and revoking access badges. Conduct routine security assessments and identify vulnerabilities in physical security infrastructure. Collaborate with local law enforcement and emergency services during security incidents. Develop and implement emergency response plans and evacuation procedures. Review surveillance footage and investigate security incidents. rain staff on security protocols and conduct security awareness programs. Requirements: High school diploma or equivalent (Bachelor's degree in a related field preferred). Proven experience in physical security, preferably in a facilities management context. Familiarity with access control systems and surveillance technology. Strong communication and interpersonal skills. Ability to remain calm and composed during emergency situations.",
                'business_id' =>  $business->id,
                'status' => 'active',
                'image' => 'https://plus.unsplash.com/premium_photo-1663047716627-e0b6c878761e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8U2VjdXJpdHklMjBBbmFseXN0fGVufDB8fDB8fHww&auto=format&fit=crop&w=600&q=60',
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
