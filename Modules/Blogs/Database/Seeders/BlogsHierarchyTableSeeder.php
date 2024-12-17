<?php

namespace Modules\Blogs\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\StandardTag;
use App\Models\TagHierarchy;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class BlogsHierarchyTableSeeder extends Seeder
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

        $data = [
            [
                'name' => 'Life',
                'children' => [
                    [
                        'name' => 'Family',
                        'children' => [
                            [
                                'name' => 'Adoption',
                            ],
                            [
                                'name' => 'Children',
                            ],
                            [
                                'name' => 'Elder Care',
                            ],
                            [
                                'name' => 'Fatherhood',
                            ],
                            [
                                'name' => 'Motherhood',
                            ],
                            [
                                'name' => 'Parenting',
                            ],
                            [
                                'name' => 'Pregnancy',
                            ],
                            [
                                'name' => 'Seniors',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Health',
                        'children' => [
                            [
                                'name' => 'Aging',
                            ],
                            [
                                'name' => 'Coronavirus',
                            ],
                            [
                                'name' => 'Covid-19',
                            ],
                            [
                                'name' => 'Death and Dying',
                            ],
                            [
                                'name' => 'Disease',
                            ],
                            [
                                'name' => 'Fitness',
                            ],
                            [
                                'name' => 'Mens Health',
                            ],
                            [
                                'name' => 'Nutrition',
                            ],
                            [
                                'name' => 'Sleep',
                            ],
                            [
                                'name' => 'Trans Healthcare',
                            ],
                            [
                                'name' => 'Vaccines',
                            ],
                            [
                                'name' => 'Weight Loss',
                            ],
                            [
                                'name' => 'Womens Health',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Relationships',
                        'children' => [
                            [
                                'name' => 'Dating',
                            ],
                            [
                                'name' => 'Divorce',
                            ],
                            [
                                'name' => 'Friendship',
                            ],
                            [
                                'name' => 'Love',
                            ],
                            [
                                'name' => 'Marriage',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Sexuality',
                        'children' => [
                            [
                                'name' => 'Sexual Health',
                            ],
                            [
                                'name' => 'HIV',
                            ]
                        ]
                    ],
                    [
                        'name' => 'Home',
                        'children' => [
                            [
                                'name' => 'Architecture',
                            ],
                            [
                                'name' => 'Home Improvement',
                            ],
                            [
                                'name' => 'Homeownership',
                            ],
                            [
                                'name' => 'Interior Design',
                            ],
                            [
                                'name' => 'Rental Property',
                            ],
                            [
                                'name' => 'Vacation Rentals',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Food',
                        'children' => [
                            [
                                'name' => 'Baking',
                            ],
                            [
                                'name' => 'Coffee',
                            ],
                            [
                                'name' => 'Cooking',
                            ],
                            [
                                'name' => 'Foodies',
                            ],
                            [
                                'name' => 'Restaurants',
                            ],
                            [
                                'name' => 'Tea',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Pets',
                        'children' => [
                            [
                                'name' => 'Cats',
                            ],
                            [
                                'name' => 'Dogs',
                            ],
                            [
                                'name' => 'Pet Training',
                            ],
                            [
                                'name' => 'Hamsters',
                            ],
                            [
                                'name' => 'Horses',
                            ],
                            [
                                'name' => 'Pet Care',
                            ],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Self Improvement',
                'children' => [
                    [
                        'name' => 'Mental Health',
                        'children' => [
                            [
                                'name' => 'Anxiety',
                            ],
                            [
                                'name' => 'Counseling',
                            ],
                            [
                                'name' => 'Grief',
                            ],
                            [
                                'name' => 'Life Lessons',
                            ],
                            [
                                'name' => 'Self-Awareness',
                            ],
                            [
                                'name' => 'Stress',
                            ],
                            [
                                'name' => 'Therapy',
                            ],
                            [
                                'name' => 'Trauma',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Productivity',
                        'children' => [
                            [
                                'name' => 'Career Advice',
                            ],
                            [
                                'name' => 'Coaching',
                            ],
                            [
                                'name' => 'Goal Setting',
                            ],
                            [
                                'name' => 'Morning Routines',
                            ],
                            [
                                'name' => 'Pomodoro Techniques',
                            ],
                            [
                                'name' => 'Time Management',
                            ],
                            [
                                'name' => 'Work Life Balance',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Mindfulness',
                        'children' => [
                            [
                                'name' => 'Meditation',
                            ],
                            [
                                'name' => 'Journaling',
                            ],
                            [
                                'name' => 'Yoga',
                            ],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Work',
                'children' => [
                    [
                        'name' => 'Business',
                        'children' => [
                            [
                                'name' => 'Entrepreneurship',
                            ],
                            [
                                'name' => 'Freelancing',
                            ],
                            [
                                'name' => 'Small Business',
                            ],
                            [
                                'name' => 'Startups',
                            ],
                            [
                                'name' => 'Venture Capital',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Marketing',
                        'children' => [
                            [
                                'name' => 'Advertising',
                            ],
                            [
                                'name' => 'Branding',
                            ],
                            [
                                'name' => 'Content Marketing',
                            ],
                            [
                                'name' => 'Content Strategy',
                            ],
                            [
                                'name' => 'Digital Marketing',
                            ],
                            [
                                'name' => 'SEO',
                            ],
                            [
                                'name' => 'Social Media Marketing',
                            ],
                            [
                                'name' => 'Storytelling',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Leadership',
                        'children' => [
                            [
                                'name' => 'Employee Engagement',
                            ],
                            [
                                'name' => 'Leadership Coaching',
                            ],
                            [
                                'name' => 'Leadership Development',
                            ],
                            [
                                'name' => 'Management',
                            ],
                            [
                                'name' => 'Meetings',
                            ],
                            [
                                'name' => 'Org Charts',
                            ],
                            [
                                'name' => 'Thought Leadership',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Remote Work',
                        'children' => [
                            [
                                'name' => 'Company Retreats',
                            ],
                            [
                                'name' => 'Digital Nomads',
                            ],
                            [
                                'name' => 'Distributed Teams',
                            ],
                            [
                                'name' => 'Future of Work',
                            ],
                            [
                                'name' => 'Work from Home',
                            ],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Technology',
                'children' => [
                    [
                        'name' => 'Artificial Intelligence',
                        'children' => [
                            [
                                'name' => 'ChatGPT',
                            ],
                            [
                                'name' => 'Conversational AI',
                            ],
                            [
                                'name' => 'Deep Learning',
                            ],
                            [
                                'name' => 'Large Language Models',
                            ],
                            [
                                'name' => 'Machine Learning',
                            ],
                            [
                                'name' => 'NLP',
                            ],
                            [
                                'name' => 'Voice Assistant',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Blockchain',
                        'children' => [
                            [
                                'name' => 'Bitcoin',
                            ],
                            [
                                'name' => 'Cryptocurrency',
                            ],
                            [
                                'name' => 'Decentralized Finance',
                            ],
                            [
                                'name' => 'Ethereum',
                            ],
                            [
                                'name' => 'NFT',
                            ],
                            [
                                'name' => 'Web3.0',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Data Science',
                        'children' => [
                            [
                                'name' => 'Analytics',
                            ],
                            [
                                'name' => 'Data Engineering',
                            ],
                            [
                                'name' => 'Data Visualization',
                            ],
                            [
                                'name' => 'Database Design',
                            ],
                            [
                                'name' => 'SQL',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Gadgets',
                        'children' => [
                            [
                                'name' => 'eBook',
                            ],
                            [
                                'name' => 'Internet of Things',
                            ],
                            [
                                'name' => 'iPad',
                            ],
                            [
                                'name' => 'Smart Homes',
                            ],
                            [
                                'name' => 'Smartphones',
                            ],
                            [
                                'name' => 'Wearables',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Makers',
                        'children' => [
                            [
                                'name' => '3D Printing',
                            ],
                            [
                                'name' => 'Arduino',
                            ],
                            [
                                'name' => 'DIY',
                            ],
                            [
                                'name' => 'Raspberry Pi',
                            ],
                            [
                                'name' => 'Robotics',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Security',
                        'children' => [
                            [
                                'name' => 'Cybersecurity',
                            ],
                            [
                                'name' => 'Data Security',
                            ],
                            [
                                'name' => 'Encryption',
                            ],
                            [
                                'name' => 'Infosec',
                            ],
                            [
                                'name' => 'Passwords',
                            ],
                            [
                                'name' => 'Privacy',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Tech Companies',
                        'children' => [
                            [
                                'name' => 'Amazon',
                            ],
                            [
                                'name' => 'Apple',
                            ],
                            [
                                'name' => 'Google',
                            ],
                            [
                                'name' => 'Mastodon',
                            ],
                            [
                                'name' => 'Meta',
                            ],
                            [
                                'name' => 'Microsoft',
                            ],
                            [
                                'name' => 'Tiktok',
                            ],
                            [
                                'name' => 'Twitter',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Design',
                        'children' => [
                            [
                                'name' => 'Accessibility',
                            ],
                            [
                                'name' => 'Design Systems',
                            ],
                            [
                                'name' => 'Design Thinking',
                            ],
                            [
                                'name' => 'Graphic Design',
                            ],
                            [
                                'name' => 'Icon Design',
                            ],
                            [
                                'name' => 'Inclusive Design',
                            ],
                            [
                                'name' => 'Product Design',
                            ],
                            [
                                'name' => 'Typography',
                            ],
                            [
                                'name' => 'UX Design',
                            ],
                            [
                                'name' => 'UX Research',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Product Management',
                        'children' => [
                            [
                                'name' => 'Agile',
                            ],
                            [
                                'name' => 'Innovation',
                            ],
                            [
                                'name' => 'Kanban',
                            ],
                            [
                                'name' => 'Lean Startup',
                            ],
                            [
                                'name' => 'MVP',
                            ],
                            [
                                'name' => 'Product',
                            ],
                            [
                                'name' => 'Strategy',
                            ],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Software Development',
                'children' => [
                    [
                        'name' => 'Programming',
                        'children' => [
                            [
                                'name' => 'Android',
                            ],
                            [
                                'name' => 'Coding',
                            ],
                            [
                                'name' => 'Flutter',
                            ],
                            [
                                'name' => 'Frontend Engineering',
                            ],
                            [
                                'name' => 'iOS Development',
                            ],
                            [
                                'name' => 'Mobile Development',
                            ],
                            [
                                'name' => 'Software Engineering',
                            ],
                            [
                                'name' => 'Web Development',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Languages',
                        'children' => [
                            [
                                'name' => 'Angular',
                            ],
                            [
                                'name' => 'CSS',
                            ],
                            [
                                'name' => 'HTML',
                            ],
                            [
                                'name' => 'Java',
                            ],
                            [
                                'name' => 'JavaScript',
                            ],
                            [
                                'name' => 'Nodejs',
                            ],
                            [
                                'name' => 'Python',
                            ],
                            [
                                'name' => 'React',
                            ],
                            [
                                'name' => 'Ruby',
                            ],
                            [
                                'name' => 'Typescript',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Dev Ops',
                        'children' => [
                            [
                                'name' => 'AWS',
                            ],
                            [
                                'name' => 'Databricks',
                            ],
                            [
                                'name' => 'Docker',
                            ],
                            [
                                'name' => 'Kubernetes',
                            ],
                            [
                                'name' => 'Terraform',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Operating Systems',
                        'children' => [
                            [
                                'name' => 'Android',
                            ],
                            [
                                'name' => 'iOS',
                            ],
                            [
                                'name' => 'Linux',
                            ],
                            [
                                'name' => 'Macos',
                            ],
                            [
                                'name' => 'Windows',
                            ],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Media',
                'children' => [
                    [
                        'name' => 'Writing',
                        'children' => [
                            [
                                'name' => '30 Day Challenge',
                            ],
                            [
                                'name' => 'Book Reviews',
                            ],
                            [
                                'name' => 'Books',
                            ],
                            [
                                'name' => 'Creative Nonfiction',
                            ],
                            [
                                'name' => 'Diary',
                            ],
                            [
                                'name' => 'Fiction',
                            ],
                            [
                                'name' => 'Haiku',
                            ],
                            [
                                'name' => 'Hello World',
                            ],
                            [
                                'name' => 'Memoir',
                            ],
                            [
                                'name' => 'Nonfiction',
                            ],
                            [
                                'name' => 'Personal Essay',
                            ],
                            [
                                'name' => 'Poetry',
                            ],
                            [
                                'name' => 'Screenwriting',
                            ],
                            [
                                'name' => 'Short Stories',
                            ],
                            [
                                'name' => 'This Happened to Me',
                            ],
                            [
                                'name' => 'Writing Prompts',
                            ],
                            [
                                'name' => 'Writing Tips',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Art',
                        'children' => [
                            [
                                'name' => 'Comics',
                            ],
                            [
                                'name' => 'Contemporary',
                            ],
                            [
                                'name' => 'Drawing',
                            ],
                            [
                                'name' => 'Fine',
                            ],
                            [
                                'name' => 'Generative',
                            ],
                            [
                                'name' => 'Illustration',
                            ],
                            [
                                'name' => 'AI',
                            ],
                            [
                                'name' => 'Painting',
                            ],
                            [
                                'name' => 'Portraits',
                            ],
                            [
                                'name' => 'Street Art',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Gaming',
                        'children' => [
                            [
                                'name' => 'Design',
                            ],
                            [
                                'name' => 'Development',
                            ],
                            [
                                'name' => 'Indie',
                            ],
                            [
                                'name' => 'Metaverse',
                            ],
                            [
                                'name' => 'Nintendo',
                            ],
                            [
                                'name' => 'Playstation',
                            ],
                            [
                                'name' => 'Videogames',
                            ],
                            [
                                'name' => 'Virtual Reality',
                            ],
                            [
                                'name' => 'Xbox',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Humor',
                        'children' => [
                            [
                                'name' => 'Comedy',
                            ],
                            [
                                'name' => 'Jokes',
                            ],
                            [
                                'name' => 'Parody',
                            ],
                            [
                                'name' => 'Satire',
                            ],
                            [
                                'name' => 'Stand-up Comedy',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Movies',
                        'children' => [
                            [
                                'name' => 'Cinema',
                            ],
                            [
                                'name' => 'Film',
                            ],
                            [
                                'name' => 'Filmmaking',
                            ],
                            [
                                'name' => 'Reviews',
                            ],
                            [
                                'name' => 'Oscars',
                            ],
                            [
                                'name' => 'Sundance',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Music',
                        'children' => [
                            [
                                'name' => 'Hip Hop',
                            ],
                            [
                                'name' => 'Indie',
                            ],
                            [
                                'name' => 'Metal',
                            ],
                            [
                                'name' => 'Pop',
                            ],
                            [
                                'name' => 'Rap',
                            ],
                            [
                                'name' => 'Rock',
                            ],
                            [
                                'name' => 'Alterantive',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Journalism',
                        'children' => [
                            [
                                'name' => 'Data',
                            ],
                            [
                                'name' => 'Fake News',
                            ],
                            [
                                'name' => 'Misinformation',
                            ],
                            [
                                'name' => 'True Crime',
                            ],
                            [
                                'name' => 'Industry',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Photography',
                        'children' => [
                            [
                                'name' => 'Cameras',
                            ],
                            [
                                'name' => 'Tips',
                            ],
                            [
                                'name' => 'Photojournalism',
                            ],
                            [
                                'name' => 'Photos',
                            ],
                            [
                                'name' => 'Street Photography',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Podcasts',
                        'children' => [
                            [
                                'name' => 'Equipment',
                            ],
                            [
                                'name' => 'Recommendations',
                            ],
                            [
                                'name' => 'Podcasting',
                            ],
                            [
                                'name' => 'Radio',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Television',
                        'children' => [
                            [
                                'name' => 'Max',
                            ],
                            [
                                'name' => 'Hulu',
                            ],
                            [
                                'name' => 'Netflix',
                            ],
                            [
                                'name' => 'Reality',
                            ],
                            [
                                'name' => 'Reviews',
                            ],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Society',
                'children' => [
                    [
                        'name' => 'Economics',
                        'children' => [
                            [
                                'name' => 'Basic Income',
                            ],
                            [
                                'name' => 'Debt',
                            ],
                            [
                                'name' => 'Economy',
                            ],
                            [
                                'name' => 'Inflation',
                            ],
                            [
                                'name' => 'Markets',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Education',
                        'children' => [
                            [
                                'name' => 'Charter Schools',
                            ],
                            [
                                'name' => 'Education Reform',
                            ],
                            [
                                'name' => 'Higher Education',
                            ],
                            [
                                'name' => 'PhD',
                            ],
                            [
                                'name' => 'Public Schools',
                            ],
                            [
                                'name' => 'Private Schools',
                            ],
                            [
                                'name' => 'Student Loans',
                            ],
                            [
                                'name' => 'Study Abroad',
                            ],
                            [
                                'name' => 'Teaching',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Equality',
                        'children' => [
                            [
                                'name' => 'Disability',
                            ],
                            [
                                'name' => 'Discrimination',
                            ],
                            [
                                'name' => 'Diversity',
                            ],
                            [
                                'name' => 'Feminism',
                            ],
                            [
                                'name' => 'Inclusion',
                            ],
                            [
                                'name' => 'LGBTQ',
                            ],
                            [
                                'name' => 'Racism',
                            ],
                            [
                                'name' => 'Transgender',
                            ],
                            [
                                'name' => 'Womens Rights',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Finance',
                        'children' => [
                            [
                                'name' => '401k',
                            ],
                            [
                                'name' => 'Investing',
                            ],
                            [
                                'name' => 'Money',
                            ],
                            [
                                'name' => 'Philantropy',
                            ],
                            [
                                'name' => 'Real Estate',
                            ],
                            [
                                'name' => 'Budgeting',
                            ],
                            [
                                'name' => 'Retirement',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Law',
                        'children' => [
                            [
                                'name' => 'Criminal Justice',
                            ],
                            [
                                'name' => 'Law School',
                            ],
                            [
                                'name' => 'Legaltech',
                            ],
                            [
                                'name' => 'Social Justice',
                            ],
                            [
                                'name' => 'Supreme Court',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Transportation',
                        'children' => [
                            [
                                'name' => 'Logistics',
                            ],
                            [
                                'name' => 'Public Transit',
                            ],
                            [
                                'name' => 'Self-driving Cars',
                            ],
                            [
                                'name' => 'Trucking',
                            ],
                            [
                                'name' => 'Urban Planning',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Politics',
                        'children' => [
                            [
                                'name' => 'Elections',
                            ],
                            [
                                'name' => 'Government',
                            ],
                            [
                                'name' => 'Gun Control',
                            ],
                            [
                                'name' => 'Immigration',
                            ],
                            [
                                'name' => 'Parties',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Race',
                        'children' => [
                            [
                                'name' => 'Native Americans',
                            ],
                            [
                                'name' => 'Anti-Racism',
                            ],
                            [
                                'name' => 'Asian Americans',
                            ],
                            [
                                'name' => 'Black Lives Matter',
                            ],
                            [
                                'name' => 'Indigenous People',
                            ],
                            [
                                'name' => 'Multiracial',
                            ],
                            [
                                'name' => 'Pacific Islander',
                            ],
                            [
                                'name' => 'White Privilege',
                            ],
                            [
                                'name' => 'White Supremacy',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Science',
                        'children' => [
                            [
                                'name' => 'Archaeology',
                            ],
                            [
                                'name' => 'Astronomy',
                            ],
                            [
                                'name' => 'Astrophysics',
                            ],
                            [
                                'name' => 'Biotechnology',
                            ],
                            [
                                'name' => 'Chemistry',
                            ],
                            [
                                'name' => 'Ecology',
                            ],
                            [
                                'name' => 'Genetics',
                            ],
                            [
                                'name' => 'Geology',
                            ],
                            [
                                'name' => 'Medicine',
                            ],
                            [
                                'name' => 'Neuroscience',
                            ],
                            [
                                'name' => 'Physics',
                            ],
                            [
                                'name' => 'Psychology',
                            ],
                            [
                                'name' => 'Space',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Mathematics',
                        'children' => [
                            [
                                'name' => 'Algebra',
                            ],
                            [
                                'name' => 'Calculus',
                            ],
                            [
                                'name' => 'Geometry',
                            ],
                            [
                                'name' => 'Probability',
                            ],
                            [
                                'name' => 'Statistics',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Drugs',
                        'children' => [
                            [
                                'name' => 'Addiction',
                            ],
                            [
                                'name' => 'Cannabis',
                            ],
                            [
                                'name' => 'Opioids',
                            ],
                            [
                                'name' => 'Pharmaceuticals',
                            ],
                            [
                                'name' => 'Psychedelics',
                            ],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Culture',
                'children' => [
                    [
                        'name' => 'Philosophy',
                        'children' => [
                            [
                                'name' => 'Atheism',
                            ],
                            [
                                'name' => 'Epistemology',
                            ],
                            [
                                'name' => 'Ethics',
                            ],
                            [
                                'name' => 'Existentialism',
                            ],
                            [
                                'name' => 'Metaphysics',
                            ],
                            [
                                'name' => 'Morality',
                            ],
                            [
                                'name' => 'Philosophy',
                            ],
                            [
                                'name' => 'Stoicism',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Religion',
                        'children' => [
                            [
                                'name' => 'Buddhism',
                            ],
                            [
                                'name' => 'Christianity',
                            ],
                            [
                                'name' => 'Hinduism',
                            ],
                            [
                                'name' => 'Islam',
                            ],
                            [
                                'name' => 'Judaism',
                            ],
                            [
                                'name' => 'Zen',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Spirituality',
                        'children' => [
                            [
                                'name' => 'Astrology',
                            ],
                            [
                                'name' => 'Energy Healing',
                            ],
                            [
                                'name' => 'Horoscopes',
                            ],
                            [
                                'name' => 'Mysticism',
                            ],
                            [
                                'name' => 'Reiki',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Cultural Studies',
                        'children' => [
                            [
                                'name' => 'Ancient History',
                            ],
                            [
                                'name' => 'Antropology',
                            ],
                            [
                                'name' => 'Cultural Heritage',
                            ],
                            [
                                'name' => 'Cajuns',
                            ],
                            [
                                'name' => 'Digital Life',
                            ],
                            [
                                'name' => 'History',
                            ],
                            [
                                'name' => 'Museums',
                            ],
                            [
                                'name' => 'Sociology',
                            ],
                            [
                                'name' => 'Tradition',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Fashion',
                        'children' => [
                            [
                                'name' => 'Clothing',
                            ],
                            [
                                'name' => 'Design',
                            ],
                            [
                                'name' => 'Trends',
                            ],
                            [
                                'name' => 'Shoes',
                            ],
                            [
                                'name' => 'Sneakers',
                            ],
                            [
                                'name' => 'Style',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Beauty',
                        'children' => [
                            [
                                'name' => 'Tips',
                            ],
                            [
                                'name' => 'Body Image',
                            ],
                            [
                                'name' => 'Hair',
                            ],
                            [
                                'name' => 'Makeup',
                            ],
                            [
                                'name' => 'Skincare',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Language',
                        'children' => [
                            [
                                'name' => 'Arabic',
                            ],
                            [
                                'name' => 'English',
                            ],
                            [
                                'name' => 'French',
                            ],
                            [
                                'name' => 'German',
                            ],
                            [
                                'name' => 'Hindi',
                            ],
                            [
                                'name' => 'Learning',
                            ],
                            [
                                'name' => 'Linguistics',
                            ],
                            [
                                'name' => 'Mandarin',
                            ],
                            [
                                'name' => 'Portuguese',
                            ],
                            [
                                'name' => 'Spanish',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Sports',
                        'children' => [
                            [
                                'name' => 'Baseball',
                            ],
                            [
                                'name' => 'Basketball',
                            ],
                            [
                                'name' => 'Football',
                            ],
                            [
                                'name' => 'NBA',
                            ],
                            [
                                'name' => 'NFL',
                            ],
                            [
                                'name' => 'Premier League',
                            ],
                            [
                                'name' => 'Soccer',
                            ],
                            [
                                'name' => 'World Cup',
                            ],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'World',
                'children' => [
                    [
                        'name' => 'Cities',
                        'children' => [
                            [
                                'name' => 'Abu Dhabi',
                            ],
                            [
                                'name' => 'Amsterdam',
                            ],
                            [
                                'name' => 'Athens',
                            ],
                            [
                                'name' => 'Bangkok',
                            ],
                            [
                                'name' => 'Barcelona',
                            ],
                            [
                                'name' => 'Barlin',
                            ],
                            [
                                'name' => 'Boston',
                            ],
                            [
                                'name' => 'Buenos Aires',
                            ],
                            [
                                'name' => 'Chicago',
                            ],
                            [
                                'name' => 'Copenhagen',
                            ],
                            [
                                'name' => 'Delhi',
                            ],
                            [
                                'name' => 'Dubai',
                            ],
                            [
                                'name' => 'Dublin',
                            ],
                            [
                                'name' => 'Edinburgh',
                            ],
                            [
                                'name' => 'Glasgow',
                            ],
                            [
                                'name' => 'Hong Kong',
                            ],
                            [
                                'name' => 'Istanbul',
                            ],
                            [
                                'name' => 'Libson',
                            ],
                            [
                                'name' => 'Los Angeles',
                            ],
                            [
                                'name' => 'Madrid',
                            ],
                            [
                                'name' => 'Melbourne',
                            ],
                            [
                                'name' => 'Mexico City',
                            ],
                            [
                                'name' => 'Cancun',
                            ],
                            [
                                'name' => 'Miami',
                            ],
                            [
                                'name' => 'Montreal',
                            ],
                            [
                                'name' => 'New York City',
                            ],
                            [
                                'name' => 'Paris',
                            ],
                            [
                                'name' => 'Prague',
                            ],
                            [
                                'name' => 'Rio De Janeiro',
                            ],
                            [
                                'name' => 'Rome',
                            ],
                            [
                                'name' => 'San Francisco',
                            ],
                            [
                                'name' => 'Sydney',
                            ],
                            [
                                'name' => 'Taipei',
                            ],
                            [
                                'name' => 'Tel Aviv',
                            ],
                            [
                                'name' => 'Tokyo',
                            ],
                            [
                                'name' => 'Toronto',
                            ],
                            [
                                'name' => 'Vancouver',
                            ],
                            [
                                'name' => 'Vienna',
                            ],
                            [
                                'name' => 'Venice',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Nature',
                        'children' => [
                            [
                                'name' => 'Birding',
                            ],
                            [
                                'name' => 'Camping',
                            ],
                            [
                                'name' => 'Climate Change',
                            ],
                            [
                                'name' => 'Conservation',
                            ],
                            [
                                'name' => 'Hiking',
                            ],
                            [
                                'name' => 'Sustainability',
                            ],
                            [
                                'name' => 'Wildlife',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Travel',
                        'children' => [
                            [
                                'name' => 'Tourism',
                            ],
                            [
                                'name' => 'Travel Tips',
                            ],
                            [
                                'name' => 'Travel Writing',
                            ],
                            [
                                'name' => 'Vacation',
                            ],
                            [
                                'name' => 'Vanlife',
                            ],
                            [
                                'name' => 'Video Blogging',
                            ],
                        ]
                    ],
                ]
            ],
        ];

        function blogsStandardTags($data, $blog, $level) {
            foreach ($data as $item) {
                $tags = StandardTag::updateOrCreate(['slug' => Str::slug($item['name']), 'type' => 'product'],[
                    'name' => $item['name'],
                    'type' => 'product',
                    'status' => 'active',
                    'priority' => 1,
                    'created_at' => Carbon::now(),
                ]);

                $newLevel = $level;
                
                if (isset($item['children'])) {
                    $newLevel[] = $tags->id;
                    blogsStandardTags($item['children'], $blog, $newLevel);
                } else {
                    $newLevel[] = $tags->id;
                    $heirarchy = TagHierarchy::updateOrCreate(
                    [
                        'L1' => $blog->id, 
                        'L2' => $newLevel[0], 
                        'L3' => $newLevel[1],
                    ],
                    [
                        'level_type' => 4,
                        'is_multiple' => 1,
                        'created_at' => Carbon::now(),
                    ]);
                    $heirarchy->standardTags()->syncWithoutDetaching($newLevel[2]);
                    $newLevel = [];
                }
            }
        }
        blogsStandardTags($data, $blog, []);
    }
}
