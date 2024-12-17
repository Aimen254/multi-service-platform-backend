<?php

namespace Modules\Events\Database\Seeders;

use App\Models\Product;
use App\Models\StandardTag;
use App\Models\User;
use App\Traits\ModuleSessionManager;
use App\Traits\ProductTagsLevelManager;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class EventsGenerateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $event = StandardTag::where('slug', 'events')->first();
        $conferences = StandardTag::where('slug', 'conferences')->first();
        $economic = StandardTag::where('slug', 'economic')->first();
        $sales_marketing = StandardTag::where('slug', 'sales-and-marketing')->first();
        $finance = StandardTag::where('slug', 'finance')->first();
        $medical = StandardTag::where('slug', 'medical')->first();
        $public_health = StandardTag::where('slug', 'public-health')->first();
        $medical_research = StandardTag::where('slug', 'medical-research')->first();

        $concerts = StandardTag::where('slug', 'concerts')->first();
        $music = StandardTag::where('slug', 'music')->first();
        $rock = StandardTag::where('slug', 'rock')->first();
        $jazz = StandardTag::where('slug', 'jazz')->first();
        $classical = StandardTag::where('slug', 'classical')->first();
        $hipHop = StandardTag::where('slug', 'hip-hop')->first();

        $shows = StandardTag::where('slug', 'shows')->first();
        $circus = StandardTag::where('slug', 'circus')->first();
        $magic = StandardTag::where('slug', 'magic')->first();

        $sports = StandardTag::where('slug', 'sports')->first();
        $football = StandardTag::where('slug', 'football')->first();
        $basketball = StandardTag::where('slug', 'basketball')->first();
        $gymnastics = StandardTag::where('slug', 'gymnastics')->first();
        $swimming_diving = StandardTag::where('slug', 'swimming-and-diving')->first();
        $professional = StandardTag::where('slug', 'professional')->first();
        $college = StandardTag::where('slug', 'college')->first();
        $high_school = StandardTag::where('slug', 'high-school')->first();

        $markets = StandardTag::where('slug', 'markets')->first();
        $farmers = StandardTag::where('slug', 'farmers')->first();
        $national_farmers = StandardTag::where('slug', 'national-farmers-market-week')->first();
        $retail = StandardTag::where('slug', 'retail')->first();
        $world_congress_retail = StandardTag::where('slug', 'world-congress-retail')->first();
        $artworks = StandardTag::where('slug', 'artworks')->first();
        $artworks_exhibition = StandardTag::where('slug', 'artworks-exhibition')->first();

        $businessOwner = User::whereEmail('businessOwner@interapptive.com')->first();
        $businessOwner1 = User::whereEmail('businessOwner1@interapptive.com')->first();
        $businessOwner2 = User::whereEmail('businessOwner2@interapptive.com')->first();

        $data = [
            // Economic
            [
                'name' => 'Sales and Marketing Excellence Conference 2023',
                'description' => 'Join us at the Sales and Marketing Excellence Conference 2023, where industry leaders, experts, and professionals gather to discuss the latest trends, strategies, and innovations in the dynamic field of sales and marketing. This conference is specifically tailored for businesses looking to enhance their sales and marketing efforts, gain valuable insights, and network with like-minded individuals. Dont miss the opportunity to elevate your business to new heights with the knowledge shared by our distinguished speakers and the networking opportunities available.',
                'user_id' => $businessOwner->id,
                'status' => 'active',
                'price' => 10,
                'max_price' => 100,
                'image' => 'https://www.securitysales.com/wp-content/uploads/2022/03/20220323_140506-1000x500.jpg',
                'L1' => $event->id,
                'L2' => $conferences->id,
                'L3' => $economic->id,
                'L4' => $sales_marketing->id,
                'event' => [
                    'performer' => 'John Deo',
                    'event_date' => Carbon::now(),
                    'event_ticket' => '9384984mkf',
                    'event_location' => 'New york, 500453',
                    'away_team' => ''
                ]
            ],
            [
                'name' => 'Global Business Summit',
                'description' => "The Global Business Summit is a premier gathering of business leaders, policymakers, and visionaries from around the world. With a focus on economic growth and sustainable development, this conference offers thought-provoking discussions, actionable insights, and networking opportunities to drive global progress.",
                'user_id' => $businessOwner->id,
                'status' => 'active',
                'price' => 10,
                'max_price' => 100,
                'image' => 'https://bsmedia.business-standard.com/_media/bs/img/article/2023-11/20/full/1700478149-8713.png?im=FeatureCrop,size=(826,465)',
                'L1' => $event->id,
                'L2' => $conferences->id,
                'L3' => $economic->id,
                'L4' => $finance->id,
                'event' => [
                    'performer' => 'John amil',
                    'event_date' => Carbon::now(),
                    'event_ticket' => '9384984mkf',
                    'event_location' => 'New york, 500453',
                    'away_team' => ''
                ]
            ],
            // Medical
            [
                'name' => 'Public Health Symposium',
                'description' => 'The Public Health Symposium gathers experts and stakeholders in public health to address pressing issues and challenges facing communities worldwide. Through keynote presentations, breakout sessions, and interactive workshops, attendees will explore strategies for promoting health equity, disease prevention, and community well-being.',
                'user_id' => $businessOwner->id,
                'status' => 'active',
                'price' => 10,
                'max_price' => 100,
                'image' => 'https://isoph.conference.unair.ac.id/wp-content/uploads/2023/08/logo.png',
                'L1' => $event->id,
                'L2' => $conferences->id,
                'L3' => $medical->id,
                'L4' => $public_health->id,
                'event' => [
                    'performer' => 'John Deo',
                    'event_date' => Carbon::now(),
                    'event_ticket' => '9384984mkf',
                    'event_location' => 'New york, 500453',
                    'away_team' => ''
                ]
            ],
            [
                'name' => 'Breakthroughs in Genomic Medicine',
                'description' => "Explore the forefront of medical research at our summit dedicated to breakthroughs in genomic medicine. Delve into topics such as precision medicine, CRISPR gene editing, and personalized therapies, and learn how genomic insights are reshaping the future of healthcare.",
                'user_id' => $businessOwner->id,
                'status' => 'active',
                'price' => 10,
                'max_price' => 100,
                'image' => 'https://i.vimeocdn.com/video/1265758403-36fee9c74193674c2f0e9b15be54243fd39c5d2be5cee9016_640?f=webp',
                'L1' => $event->id,
                'L2' => $conferences->id,
                'L3' => $medical->id,
                'L4' => $medical_research->id,
                'event' => [
                    'performer' => 'John amil',
                    'event_date' => Carbon::now(),
                    'event_ticket' => '9384984mkf',
                    'event_location' => 'New york, 500453',
                    'away_team' => ''
                ]
            ],
            // Concerts
            [
                'name' => 'Rock Revival Night',
                'description' => "Dive into the world of rock music with this introductory event. Learn about the history, evolution, and influence of rock music across different cultures and eras. Whether you're a seasoned fan or a newcomer, this event promises to be an engaging exploration of one of the most iconic genres in music history.",
                'user_id' => $businessOwner1->id,
                'status' => 'active',
                'price' => 10,
                'max_price' => 100,
                'image' => 'https://bloximages.chicago2.vip.townnews.com/swoknews.com/content/tncms/assets/v3/editorial/2/36/23660400-1c4f-5486-b47d-412635a6681f/639bf10303fb7.image.jpg',
                'L1' => $event->id,
                'L2' => $concerts->id,
                'L3' => $music->id,
                'L4' => $rock->id,
                'event' => [
                    'performer' => 'Jabir',
                    'event_date' => Carbon::now(),
                    'event_ticket' => '9384984mkf',
                    'event_location' => 'New york, 500453',
                    'away_team' => ''
                ]
            ],
            [
                'name' => 'Hip-Hop Harmony Fusion Night',
                'description' => "Join us for an unforgettable evening of Harmony Fusion Night, where music enthusiasts, concert lovers, and hip-hop researchers come together for a unique experience. Immerse yourself in the rhythm as talented artists from various genres showcase their skills on stage. From soulful melodies to electrifying hip-hop beats, this event promises a diverse musical journey.",
                'user_id' => $businessOwner2->id,
                'status' => 'active',
                'price' => 10,
                'max_price' => 100,
                'image' => 'https://www.pinkvilla.com/images/2024-02/1103980976_copy-of-cms-lead-image-1.jpg',
                'L1' => $event->id,
                'L2' => $concerts->id,
                'L3' => $music->id,
                'L4' => $hipHop->id,
                'event' => [
                    'performer' => 'Rodulph',
                    'event_date' => Carbon::now(),
                    'event_ticket' => '9384984mkf',
                    'event_location' => 'New york, 500453',
                    'away_team' => ''
                ]
            ],
            [
                'name' => 'Jazz Odyssey',
                'description' => "Dive into the world of jazz at Jazz Odyssey, your Level One experience blending concerts, music, and jazz research. Enjoy soulful performances, explore diverse musical genres, and kickstart your jazz research journey. Join us for a night of musical discovery and intellectual curiosity at Jazz Odyssey!",
                'user_id' => $businessOwner2->id,
                'status' => 'active',
                'price' => 10,
                'max_price' => 100,
                'image' => 'https://www.jambase.com/wp-content/uploads/2022/09/jacob-fred-jazz-odyssey-trio-video-2019-1480x832.jpg',
                'L1' => $event->id,
                'L2' => $concerts->id,
                'L3' => $music->id,
                'L4' => $jazz->id,
                'event' => [
                    'performer' => 'Rodulph',
                    'event_date' => Carbon::now(),
                    'event_ticket' => '9384984mkf',
                    'event_location' => 'New york, 500453',
                    'away_team' => ''
                ]
            ],
            [
                'name' => 'Classical Harmony Night',
                'description' => "Experience the timeless beauty of classical music at Classical Harmony Night. Immerse yourself in enchanting compositions, performed by skilled musicians, as we celebrate the elegance and grace of classical melodies. This event marks your Level One journey into the world of concerts, music, and sets the stage for future explorations. Join us for an evening of refined musical artistry at Classical Harmony Night!",
                'user_id' => $businessOwner2->id,
                'status' => 'active',
                'price' => 10,
                'max_price' => 100,
                'image' => 'https://dynamic-media-cdn.tripadvisor.com/media/photo-o/1a/ab/92/d5/caption.jpg?w=1200&h=-1&s=1',
                'L1' => $event->id,
                'L2' => $concerts->id,
                'L3' => $music->id,
                'L4' => $classical->id,
                'event' => [
                    'performer' => 'Rodulph',
                    'event_date' => Carbon::now(),
                    'event_ticket' => '9384984mkf',
                    'event_location' => 'New york, 500453',
                    'away_team' => ''
                ]
            ],
            // Shows
            [
                'name' => 'SpectraFest',
                'description' => "Experience the magic at SpectraFest – your ultimate entertainment journey. From thrilling concerts and captivating shows to the whimsical world of circus delights, SpectraFest brings levels of fun like never before. Get ready for a one-of-a-kind event that transcends entertainment boundaries!",
                'user_id' => $businessOwner1->id,
                'status' => 'active',
                'price' => 10,
                'max_price' => 100,
                'image' => 'https://preschooleducation.com/wp-content/uploads/2022/09/Circus-Songs-for-Kids.webp',
                'L1' => $event->id,
                'L2' => $concerts->id,
                'L3' => $shows->id,
                'L4' => $circus->id,
                'event' => [
                    'performer' => 'Jabir',
                    'event_date' => Carbon::now(),
                    'event_ticket' => '9384984mkf',
                    'event_location' => 'New york, 500453',
                    'away_team' => ''
                ]
            ],
            [
                'name' => 'Enigma Showcase',
                'description' => "Immerse yourself in the magic of Enigma Showcase – an unforgettable blend of concerts, shows, and mesmerizing magic. A night where entertainment takes center stage, promising a captivating experience for all. Join us for an enchanting evening!",
                'user_id' => $businessOwner2->id,
                'status' => 'active',
                'price' => 10,
                'max_price' => 100,
                'image' => 'https://ultimatepartyplannergoa.com/images/Magic2.jpg',
                'L1' => $event->id,
                'L2' => $concerts->id,
                'L3' => $shows->id,
                'L4' => $magic->id,
                'event' => [
                    'performer' => 'Rodulph',
                    'event_date' => Carbon::now(),
                    'event_ticket' => '9384984mkf',
                    'event_location' => 'New york, 500453',
                    'away_team' => ''
                ]
            ],
            // Sports - Football
            [
                'name' => 'USC Football',
                'description' => "Experience the ultimate thrill at USC Football, where professional football takes the spotlight. Fast-paced action, skilled players, and intense competition make this event your top-tier sports destination. Don't miss the excitement – it's a level one event that's all about the game!",
                'user_id' => $businessOwner2->id,
                'status' => 'active',
                'price' => 10,
                'max_price' => 100,
                'image' => 'https://d3jycsk0m72ya7.cloudfront.net/images/2022/2/1/usc_trojans_football_national_signing_day_2022.jpg',
                'L1' => $event->id,
                'L2' => $sports->id,
                'L3' => $football->id,
                'L4' => $professional->id,
                'event' => [
                    'performer' => 'Rodulph',
                    'event_date' => Carbon::now(),
                    'event_ticket' => '9384984mkf',
                    'event_location' => 'New york, 500453',
                    'away_team' => ''
                ]
            ],
            [
                'name' => 'College Football Spectacle',
                'description' => "Experience the ultimate thrill at our College Football Spectacle! Dive into the world of sports excitement as college teams clash on the field. Don't miss the intense competition and vibrant college spirit in this action-packed event. Grab your seats for a memorable evening of football frenzy!",
                'user_id' => $businessOwner2->id,
                'status' => 'active',
                'price' => 10,
                'max_price' => 100,
                'image' => 'https://dmn-dallas-news-prod.cdn.arcpublishing.com/resizer/v2/6ID77EZZ7BGNNJEBZ2NX6LSRSI.jpg?auth=a35f9e88589e7b56e27de86c657038281927db44a65bd6532d44f8fb9d971cb1&height=1107&width=1660&smart=true',
                'L1' => $event->id,
                'L2' => $sports->id,
                'L3' => $football->id,
                'L4' => $college->id,
                'event' => [
                    'performer' => 'Rodulph',
                    'event_date' => Carbon::now(),
                    'event_ticket' => '9384984mkf',
                    'event_location' => 'New york, 500453',
                    'away_team' => ''
                ]
            ],
            [
                'name' => 'High School Football Showcase',
                'description' => "Experience the thrill of high school football at its finest. Join us for an action-packed showcase featuring talented young athletes competing on the gridiron. Don't miss the excitement as the next generation of football stars display their skills in this electrifying event!",
                'user_id' => $businessOwner2->id,
                'status' => 'active',
                'price' => 10,
                'max_price' => 100,
                'image' => 'https://assets1.cbsnewsstatic.com/hub/i/r/2023/10/19/4120d979-8f38-4abf-a1e1-93caa83d027b/thumbnail/1200x630/0f7b0a99e130f8620237dc188748a84d/moon-vs-usc-hs-football.jpg?v=18a5d3569ab1a3ca759fe14d213f7845',
                'L1' => $event->id,
                'L2' => $sports->id,
                'L3' => $football->id,
                'L4' => $high_school->id,
                'event' => [
                    'performer' => 'Rodulph',
                    'event_date' => Carbon::now(),
                    'event_ticket' => '9384984mkf',
                    'event_location' => 'New york, 500453',
                    'away_team' => ''
                ]
            ],
            // Sports - Basketball
            [
                'name' => 'ProHoops Spectacle',
                'description' => "Experience the thrill at ProHoops Spectacle – your ultimate professional basketball showdown! Join us for an electrifying event that combines sportsmanship, skill, and pure basketball prowess. Don't miss the slam dunks, three-pointers, and intense competition. It's where passion meets the court! 🏀",
                'user_id' => $businessOwner2->id,
                'status' => 'active',
                'price' => 10,
                'max_price' => 100,
                'image' => 'https://images.eurohoops.net/2023/09/7380a071-kkp-5.jpg',
                'L1' => $event->id,
                'L2' => $sports->id,
                'L3' => $basketball->id,
                'L4' => $professional->id,
                'event' => [
                    'performer' => 'Rodulph',
                    'event_date' => Carbon::now(),
                    'event_ticket' => '9384984mkf',
                    'event_location' => 'New york, 500453',
                    'away_team' => ''
                ]
            ],
            [
                'name' => 'SlamFest College Hoops',
                'description' => "Dive into SlamFest, where the excitement of college basketball unfolds. Join us for an epic sports experience, celebrating the spirit of college hoops. Fast-paced action, fierce competition – it's a slam dunk of fun!",
                'user_id' => $businessOwner2->id,
                'status' => 'active',
                'price' => 10,
                'max_price' => 100,
                'image' => 'https://images.eurohoops.net/2023/08/0f9b959e-josh-giddey-anthony-edwards-karl-anthony-towns-collage-top-10-players-world-cup.jpg',
                'L1' => $event->id,
                'L2' => $sports->id,
                'L3' => $basketball->id,
                'L4' => $college->id,
                'event' => [
                    'performer' => 'Rodulph',
                    'event_date' => Carbon::now(),
                    'event_ticket' => '9384984mkf',
                    'event_location' => 'New york, 500453',
                    'away_team' => ''
                ]
            ],
            [
                'name' => 'SlamFest 2024',
                'description' => "A high school basketball extravaganza! Witness thrilling hoops action as teams compete for glory. Don't miss the slam dunks, intense rivalries, and pure athleticism. Get ready for an unforgettable sports spectacle at SlamFest! 🏀",
                'user_id' => $businessOwner2->id,
                'status' => 'active',
                'price' => 10,
                'max_price' => 100,
                'image' => 'https://www.fiba.basketball/images.fiba.com/Graphic/B/D/C/B/u7MwDfoQjUyxoXdbJbyZNA.png?v=20220926160359591',
                'L1' => $event->id,
                'L2' => $sports->id,
                'L3' => $basketball->id,
                'L4' => $high_school->id,
                'event' => [
                    'performer' => 'Rodulph',
                    'event_date' => Carbon::now(),
                    'event_ticket' => '9384984mkf',
                    'event_location' => 'New york, 500453',
                    'away_team' => ''
                ]
            ],
            // Sports - Gymnastics
            [
                'name' => 'ProGym Spectacle',
                'description' => "Level up your excitement at ProGym Spectacle, a thrilling event where professional athletes showcase their prowess in gymnastics. Witness the perfect blend of sportsmanship and skill in a dynamic and fast-paced spectacle. Don't miss this adrenaline-packed experience!",
                'user_id' => $businessOwner2->id,
                'status' => 'active',
                'price' => 10,
                'max_price' => 100,
                'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRdYeT3fdYcFCKTz9woHrLzQG_0s7Hu3sC48fsi0s6KFdTJk-p00fgRIrqfl6rspduRoSY&usqp=CAU',
                'L1' => $event->id,
                'L2' => $sports->id,
                'L3' => $gymnastics->id,
                'L4' => $professional->id,
                'event' => [
                    'performer' => 'Rodulph',
                    'event_date' => Carbon::now(),
                    'event_ticket' => '9384984mkf',
                    'event_location' => 'New york, 500453',
                    'away_team' => ''
                ]
            ],
            [
                'name' => 'Sports Spectacle',
                'description' => "Join us for a thrilling Sports Spectacle at the college level, featuring intense competitions across various disciplines, with a spotlight on the grace and agility of gymnastics. Get ready to witness top-notch athleticism and college spirit in this dynamic event!",
                'user_id' => $businessOwner2->id,
                'status' => 'active',
                'price' => 10,
                'max_price' => 100,
                'image' => 'https://images.fresha.com/lead-images/placeholders/gym-and-fitness-9.jpg?class=width-small',
                'L1' => $event->id,
                'L2' => $sports->id,
                'L3' => $gymnastics->id,
                'L4' => $college->id,
                'event' => [
                    'performer' => 'Rodulph',
                    'event_date' => Carbon::now(),
                    'event_ticket' => '9384984mkf',
                    'event_location' => 'New york, 500453',
                    'away_team' => ''
                ]
            ],
            [
                'name' => 'Athletic Showcase',
                'description' => "Join us for an exhilarating Athletic Showcase, where sports enthusiasts and high school talents shine! From impressive feats on the field to the grace of gymnastics, experience the spirit of competition and camaraderie. Don't miss this dynamic event, bringing together the best of high school athleticism.",
                'user_id' => $businessOwner2->id,
                'status' => 'active',
                'price' => 10,
                'max_price' => 100,
                'image' => 'https://imengine.public.prod.mmg.navigacloud.com/?uuid=42246160-7615-59d4-99a4-2f06eaf9995c&function=cropresize&type=preview&source=false&q=75&crop_w=0.99999&crop_h=0.8427&width=1200&height=675&x=1.0E-5&y=0.07865',
                'L1' => $event->id,
                'L2' => $sports->id,
                'L3' => $gymnastics->id,
                'L4' => $high_school->id,
                'event' => [
                    'performer' => 'Rodulph',
                    'event_date' => Carbon::now(),
                    'event_ticket' => '9384984mkf',
                    'event_location' => 'New york, 500453',
                    'away_team' => ''
                ]
            ],
            // Sports - Swimming Diving
            [
                'name' => 'DivePros Championship',
                'description' => "Dive into the thrill of DivePros Championship – a premier sports event where professional athletes showcase their prowess in the captivating world of swimming and diving. Witness precision, skill, and excellence as top-notch competitors vie for victory in this exhilarating competition. Don't miss the splash – join us for a sporting spectacle!🏊🏆",
                'user_id' => $businessOwner2->id,
                'status' => 'active',
                'price' => 10,
                'max_price' => 100,
                'image' => 'https://i.pinimg.com/originals/95/05/59/9505599d7c3539487b19842d758dcfbb.jpg',
                'L1' => $event->id,
                'L2' => $sports->id,
                'L3' => $swimming_diving->id,
                'L4' => $professional->id,
                'event' => [
                    'performer' => 'Rodulph',
                    'event_date' => Carbon::now(),
                    'event_ticket' => '9384984mkf',
                    'event_location' => 'New york, 500453',
                    'away_team' => ''
                ]
            ],
            [
                'name' => 'Swimming Splash',
                'description' => "Dive into excitement at Swimming Splash, where the thrill of collegiate competition meets the elegance of swimming and diving. Join us for an unforgettable experience that merges athleticism and collegiate spirit in a splash of excitement!",
                'user_id' => $businessOwner2->id,
                'status' => 'active',
                'price' => 10,
                'max_price' => 100,
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4a/Depart4x100.jpg/1200px-Depart4x100.jpg',
                'L1' => $event->id,
                'L2' => $sports->id,
                'L3' => $swimming_diving->id,
                'L4' => $college->id,
                'event' => [
                    'performer' => 'Rodulph',
                    'event_date' => Carbon::now(),
                    'event_ticket' => '9384984mkf',
                    'event_location' => 'New york, 500453',
                    'away_team' => ''
                ]
            ],
            [
                'name' => 'DiveFest',
                'description' => "Dive into excitement at DiveFest - the ultimate high school sports event! Experience thrilling competitions in swimming and diving. It's your level one destination for sports excellence and aquatic prowess. Join us for a splash of high school athleticism and make a splash at DiveFest! 🏊🏆",
                'user_id' => $businessOwner2->id,
                'status' => 'active',
                'price' => 10,
                'max_price' => 100,
                'image' => 'https://i.vimeocdn.com/video/1669842073-9c1dc08b8854935576810f83ade6c36b22bebf91a6b832a3bbf5a5f7506fa47e-d',
                'L1' => $event->id,
                'L2' => $sports->id,
                'L3' => $swimming_diving->id,
                'L4' => $high_school->id,
                'event' => [
                    'performer' => 'Rodulph',
                    'event_date' => Carbon::now(),
                    'event_ticket' => '9384984mkf',
                    'event_location' => 'New york, 500453',
                    'away_team' => ''
                ]
            ],
            // Markets
            [
                'name' => 'Farmers Market Event',
                'description' => "Join us for the Farmers Market Event, where local farmers showcase their fresh produce and artisanal goods. Discover a vibrant array of fruits, vegetables, baked goods, and more, all sourced directly from the farmers themselves. Experience the best of local agriculture while supporting your community. Don't miss out on this opportunity to connect with growers and artisans, and enjoy a delightful shopping experience at our Farmers Market Event!",
                'user_id' => $businessOwner2->id,
                'status' => 'active',
                'price' => 10,
                'max_price' => 100,
                'image' => 'https://www.silverkris.com/wp-content/uploads/2016/08/shutterstock_1599424258.jpg',
                'L1' => $event->id,
                'L2' => $markets->id,
                'L3' => $farmers->id,
                'L4' => $national_farmers->id,
                'event' => [
                    'performer' => 'Rodulph',
                    'event_date' => Carbon::now(),
                    'event_ticket' => '9384984mkf',
                    'event_location' => 'New york, 500453',
                    'away_team' => ''
                ]
            ],
            [
                'name' => 'TechCraft Expo',
                'description' => "Join us at TechCraft Expo, where innovation meets inspiration! This event is your gateway to the latest trends and advancements in the world of technology. From cutting-edge software solutions to revolutionary hardware developments, our expo brings together top-notch professionals and enthusiasts in a vibrant atmosphere of knowledge sharing and networking. Don't miss the opportunity to explore the forefront of the tech industry at TechCraft Expo!",
                'user_id' => $businessOwner2->id,
                'status' => 'active',
                'price' => 10,
                'max_price' => 100,
                'image' => 'https://scrapbookexpo.com/wp-content/uploads/2019/12/StyleTechCraft.jpg',
                'L1' => $event->id,
                'L2' => $markets->id,
                'L3' => $retail->id,
                'L4' => $world_congress_retail->id,
                'event' => [
                    'performer' => 'Rodulph',
                    'event_date' => Carbon::now(),
                    'event_ticket' => '9384984mkf',
                    'event_location' => 'New york, 500453',
                    'away_team' => ''
                ]
            ],
            [
                'name' => 'Artistry Expo',
                'description' => "Join us at Artistry Expo, a vibrant celebration of creativity and culture. At our event, you'll discover a kaleidoscope of artistic expressions spanning various mediums and genres. From captivating paintings and sculptures to mesmerizing performances and interactive installations, Artistry Expo showcases the boundless ingenuity of talented artists from around the world.",
                'user_id' => $businessOwner2->id,
                'status' => 'active',
                'price' => 10,
                'max_price' => 100,
                'image' => 'https://www.portugalresident.com/wp-content/uploads/2023/02/Art-Expo-PTM-1.jpg',
                'L1' => $event->id,
                'L2' => $markets->id,
                'L3' => $artworks->id,
                'L4' => $artworks_exhibition->id,
                'event' => [
                    'performer' => 'Rodulph',
                    'event_date' => Carbon::now(),
                    'event_ticket' => '9384984mkf',
                    'event_location' => 'New york, 500453',
                    'away_team' => ''
                ]
            ],

        ];

        foreach ($data as $item) {
            ModuleSessionManager::setModule('events');
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
            $product->events()->delete();
            $product->events()->create($item['event']);

            $product->standardTags()->syncWithoutDetaching([$item['L1'], $item['L2'], $item['L3'], $item['L4']]);
            ProductTagsLevelManager::checkProductTagsLevel($product);
        }
    }
}
