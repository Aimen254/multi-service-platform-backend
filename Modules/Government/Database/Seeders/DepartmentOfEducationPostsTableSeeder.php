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

class DepartmentOfEducationPostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $business = Business::where('slug', 'department-of-education')->first();
        $government = StandardTag::where('slug', 'government')->where('type', 'module')->first();

        // level two
        $education = StandardTag::where('slug', 'education')->whereRelation('businesses', 'id', $business?->id)->first();

        // level three
        $higher_education = StandardTag::where('slug', 'higher-education')->whereRelation('businesses', 'id', $business?->id)->first();

        // level four
        $university_affairs = StandardTag::where('slug', 'university-affairs')->first();
        $university_scholarships = StandardTag::where('slug', 'university-scholarships')->first();

        // level three
        $elementary_education = StandardTag::where('slug', 'elementary-education')->whereRelation('businesses', 'id', $business?->id)->first();

        // level four
        $curriculum_development = StandardTag::where('slug', 'curriculum-development')->first();
        $teacher_training = StandardTag::where('slug', 'teacher-training')->first();

        $data = [
            [
                'name' => ' Future Educators Scholarship',
                'description' => " These scholarships are designed for undergraduate students majoring in education with a commitment to becoming future educators. It may require a minimum GPA and involvement in education-related extracurricular activities.These scholarships aims to support students from diverse backgrounds who are pursuing a career in education. It might have specific criteria related to underrepresented groups and a commitment to promoting diversity and inclusion in the field.Some universities offer scholarships specifically for students pursuing degrees in STEM (Science, Technology, Engineering, and Mathematics) education.Scholarships for students majoring in special education can be available, particularly for those who plan to work with children with disabilities. These scholarships may require a passion for inclusive education and may involve service commitments.Scholarships for students interested in educational leadership roles, such as principals or administrators, may be available. These often require a commitment to improving educational systems.",
                'business_id' =>  $business->id,
                'status' => 'active',
                'image' => 'https://plus.unsplash.com/premium_photo-1683535508596-9216de2ad64b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTl8fHNjaG9sYXJzaGlwfGVufDB8fDB8fHww&auto=format&fit=crop&w=600&q=60',
                'L1' =>  $government->id,
                'L2' => $education?->id,
                'L3' =>  $higher_education?->id,
                'L4' =>  $university_scholarships->id,
            ],
            [
                'name' => ' Scholarship Opportunity for IT Students',
                'description' => "We are excited to announce a scholarship opportunity for aspiring IT students like you. We are committed to supporting the next generation of IT professionals and innovators, and we believe that education is the key to unlocking your full potential.To be eligible for this scholarship, you must meet the following criteria:Currently enrolled or accepted into an accredited IT-related program at a college or university.Maintain a minimum GPA of 4. Submit a completed scholarship application form. To apply for this scholarship, please submit the following:An updated resume or curriculum vitae (CV) outlining your academic achievements, extracurricular activities, and any relevant work experience.An official or unofficial transcript from your current institution.Please visit our website to access the scholarship application form and submit your application materials.Selection Process:Our scholarship committee will carefully review all applications, considering academic performance, essay quality, and overall commitment to the field of IT. Finalists may be invited for an interview.",
                'business_id' =>  $business->id,
                'status' => 'active',
                'image' => 'https://plus.unsplash.com/premium_photo-1664372145537-bd55c09328fb?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8c2Nob2xhcnNoaXB8ZW58MHx8MHx8fDA%3D&auto=format&fit=crop&w=600&q=60',
                'L1' =>  $government->id,
                'L2' => $education?->id,
                'L3' =>  $higher_education?->id,
                'L4' =>  $university_scholarships->id,
            ],
            [
                'name' => 'University Affairs, matters and issues related to higher education institutions, such as universities.',
                'description' => "Academic Affairs includes curriculum development, academic programs, faculty recruitment and promotion, academic policies, and accreditationStudent Affairs nvolves services and programs that support students outside of the classroom, including housing, counseling, student organizations, and extracurricular activities.  Universities are hubs of research and innovation, and university affairs may encompass discussions about research funding, collaborations, and technology transfer.Campus Development Matters related to the physical infrastructure of the university, such as building construction, maintenance, and campus planning, fall under this category.University affairs often include budgeting and financial decisions, tuition rates, fundraising, and endowment management.Universities have governance structures that involve boards of trustees, administrators, and faculty. University affairs may involve discussions about governance policies and decisions.How universities communicate with the public, including prospective students, alumni, and the broader community, is essential in university affairs.",
                'business_id' =>  $business->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1606761568499-6d2451b23c66?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8dW5pdmVyc2l0eXxlbnwwfHwwfHx8MA%3D%3D&auto=format&fit=crop&w=600&q=60',
                'L1' =>  $government->id,
                'L2' => $education?->id,
                'L3' =>  $higher_education?->id,
                'L4' =>  $university_affairs->id,
            ],
            [
                'name' => 'Curriculum development,process of designing, creating, and organizing an educational program or course of study.',
                'description' => "Curriculum development making decisions about what content to teach, how to teach it, and how to assess student learning.Curriculum development is a complex and multifaceted process that is essential for providing effective and meaningful education.Identify the educational needs of the learners and the goals of the educational program. Consider factors such as the learners' age, background, prior knowledge, and future needs.Define clear and measurable learning objectives or outcomes. These objectives should specify what students should know, understand, or be able to do by the end of the course or program.Determine what topics, concepts, and skills should be included in the curriculum. This involves deciding on the scope and sequence of the content.Choose appropriate teaching methods, strategies, and instructional materials that align with the learning objectives. Consider a variety of instructional techniques, including lectures, discussions, hands-on activities, and technology-enhanced learning.Develop assessment methods and tools to measure student progress and achievement of the learning objectives. These may include quizzes, exams, projects, presentations, and performance assessments.Ensure alignment between the learning objectives, instructional activities, and assessments. All components of the curriculum should work together to support the intended learning outcomes. Be prepared to adapt the curriculum based on ongoing assessment and feedback from both students and instructors. Flexibility is key to addressing changing needs and improving the curriculum over time.Consider the diverse backgrounds and needs of learners. Develop a curriculum that is inclusive and culturally responsive, addressing the needs of all students.Incorporate technology where appropriate to enhance learning experiences and prepare students for the digital age.Provide training and support for instructors to effectively deliver the curriculum. Continuous professional development is important for keeping educators up to date with best practices.Seek input from various stakeholders, including students, parents, teachers, and community members, to ensure that the curriculum meets their expectations and needs.Ensure that the curriculum complies with legal requirements and ethical standards, such as copyright and intellectual property laws.Roll out the curriculum in the classroom or educational setting. Monitor its effectiveness and make adjustments as necessary. Conduct a comprehensive evaluation of the curriculum's impact on student learning and outcomes. Use the findings to make improvements and inform future curriculum development efforts.",
                'business_id' =>  $business->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8Y3VycmljdWx1bSUyMGRldmVsb3BtZW50fGVufDB8fDB8fHww&auto=format&fit=crop&w=600&q=60',
                'L1' =>  $government->id,
                'L2' => $education?->id,
                'L3' =>  $elementary_education?->id,
                'L4' =>  $curriculum_development->id,
            ],
            [
                'name' => "Teacher training, process of preparing individuals to become effective educators in schools, colleges, and other educational settings.",
                'description' => "Teacher training involves a combination of academic coursework, practical classroom experience, and professional development to equip teachers with the knowledge, skills, and strategies they need to excel in their roles.Teacher training programs typically include coursework in education theory, pedagogy, curriculum development, and classroom management. This academic component provides prospective teachers with a foundation of knowledge about educational principles and practices.Practical experience is a crucial aspect of teacher training. Prospective teachers often spend time in actual classrooms, working alongside experienced educators to gain hands-on experience. This allows them to apply what they've learned in the classroom setting. Teacher candidates are often observed by mentor teachers or supervisors during their student teaching or practicum experiences. They receive constructive feedback on their teaching methods, allowing them to improve and refine their skills.Teacher training programs teach a variety of pedagogical techniques and strategies to engage students and facilitate effective learning. This includes methods for differentiated instruction, classroom assessment, and adapting teaching approaches to diverse learners. Depending on the level and subject area they plan to teach, prospective teachers need to have a strong foundation in the content they will be instructing. This may involve additional coursework in specific subjects.In today's digital age, teacher training often includes instruction on how to effectively integrate technology into the classroom to enhance teaching and learning.Many teacher training programs incorporate training on working with students with special needs and promoting inclusive education practices.Teachers need to be culturally competent and aware of the diverse backgrounds and needs of their students. Training in cultural sensitivity and equity is becoming increasingly important.Teacher training is not a one-time event; it's an ongoing process. Teachers are encouraged to engage in continuous professional development to stay up-to-date with the latest research, teaching methods, and educational trends.In many countries, teachers must obtain certification or licensing to teach in public schools. Teacher training programs often prepare candidates for the exams and requirements necessary for certification.",
                'business_id' =>  $business->id,
                'status' => 'active',
                'image' => 'https://plus.unsplash.com/premium_photo-1661964320064-ca1bfb994d11?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8dGVhY2hlcnxlbnwwfHwwfHx8MA%3D%3D&auto=format&fit=crop&w=600&q=60',
                'L1' =>  $government->id,
                'L2' => $education?->id,
                'L3' =>  $elementary_education?->id,
                'L4' =>  $teacher_training->id,
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
