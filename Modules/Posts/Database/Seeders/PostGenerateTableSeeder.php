<?php

namespace Modules\Posts\Database\Seeders;

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

class PostGenerateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        

        $post = StandardTag::where('slug', 'posts')->first();
        $sports = StandardTag::where('slug', 'sports')->first();
        $other_sport = StandardTag::where('name', 'Other Sport')->first();
        $lsu = StandardTag::where('slug', 'lsu')->first();
        $business = StandardTag::where('slug', 'business')->first();
        $healthcare = StandardTag::where('slug', 'healthcare')->first();
        $technology = StandardTag::where('slug', 'technology')->first();
        $real_estate = StandardTag::where('slug', 'real-estate')->first();
        $education= StandardTag::where('slug', 'education')->first();
        $football = StandardTag::where('slug', 'football')->first();
        $transportation = StandardTag::where('slug', 'transportation')->first();
        $farming = StandardTag::where('slug', 'farming')->first();
        $agriculture = StandardTag::where('slug', 'agriculture')->first();
        $pga = StandardTag::where('slug', 'pga')->first();
        $opinion = StandardTag::where('slug', 'opinion')->first();
        $culture = StandardTag::where('slug', 'culture')->first();
        $communities = StandardTag::where('slug', 'communities')->first();
        $denham_springs = StandardTag::where('slug', 'denham-springs')->first();
        $livestock = StandardTag::where('slug', 'livestock')->first();
        $mental_health = StandardTag::where('slug', 'mental-health')->first();
        $diplomacy = StandardTag::where('slug', 'Diplomacy')->first();
        $economics = StandardTag::where('slug', 'economics')->first();
        $politics = StandardTag::where('slug', 'politics')->first();
        $all = StandardTag::where('slug', 'all')->first();

        $businessOwner = User::whereEmail('businessOwner@interapptive.com')->first();
        $businessOwner1 = User::whereEmail('businessOwner1@interapptive.com')->first();
        $businessOwner2 = User::whereEmail('businessOwner2@interapptive.com')->first();

        $data = [
            [
                'name' => 'No matter what happens at The Oval, England will look back at Ashes 2023 as a lost opportunity: Mark Butcher',
                'description' => 'Former England cricketer Mark Butcher expressed that the side will look at the Ashes 2023 as a lost opportunity even if they win the fifth and final Test at The Oval in London. Notably, the Aussies retained the urn following a rain-marred fourth Test at Old Trafford in Manchester.England were comfortably placed after posting a humongous 592 and piling up a massive lead of 275 runs in the second innings. The Aussies were in a spot of bother, however, persistent rain at the venue interrupted the play, pushing the match into a draw.Reflecting on the matches thus far, former cricketer Butcher stated that the Three Lions will look at the series as a lost opportunity. He further added that a win at The Oval in the fifth Test would not change their fate and that the weather changed the complexion of the series in Australia favour.England really will look back on the whole thing, no matter what happens at The Oval as a lost opportunity. Even in the first two Test matches that they lost, they had opportunities to reverse both of those results and just were not quite smart enough or quite experienced enough perhaps to take advantage of them… which left them with a mountain to climb, Butcher told Wisden.Winning three on the bounce against Australia is a Herculean task. They probably would have won the game if it were not for the weather, but still thats baked into cricket in England. Bad weather has always played a part, sometimes it helps you out sometimes it dumps all over you and thats the game, he added.England unwanted streak of being unable to win the Ashes continues as they were last successful in 2015. Meanwhile, the Aussies will be gunning to win their first Ashes series away from home since 2001.',
                'user_id' =>  $businessOwner->id,
                'status' => 'active',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/77/Lords-Cricket-Ground-Pavilion-06-08-2017.jpg/1200px-Lords-Cricket-Ground-Pavilion-06-08-2017.jpg',
                'L1' =>  $post->id,
                'L2' => $sports->id,
                'L3' =>  $other_sport->id, 
                'L4' =>   $all->id,        
            ],
            [
                'name' => 'PGA Coach Greg Avant & his son Tommi have much more in common than just their love for the game.',
                'description' => "In the summer of 1997, Greg Avant, PGA, won the Southwest PGA Match Play Championship.
                That same year his son, Tommi, was born.Flash forward to the summer of 2022, and Tommi, now a PGA Associate, continues on the family legacy with a win at the same Match Play Championship.“At the time, I didn’t know he had won the Match Play,” says Tommi, who's an Assistant Professional at Lone Tree Golf Club in Chandler, Arizona. “I knew he had won some majors in the Section, but I didn’t know which ones. I was just focused and motivated to win on my own. Once I found out he won this in ‘97, I thought it was so cool that we are the only father-son to win the same event in Section history.”“Before he went into the final match, I just told him ‘Hey man, this would be pretty cool, but go do your thing,’” remembers Greg, Lone Tree's PGA Director of Golf and Owner,Tommi ended up beating 2022 PGA Professional Champion Jesse Mueller in the final match to claim the title. Mueller played in two major championships in 2022, has participated in 13 PGA Tour events and 19 Korn Ferry Tour events, so it was a big moment for the younger Avant.",
                'user_id' =>  $businessOwner->id,
                'status' => 'active',
                'image' => 'https://pbs.twimg.com/media/F2Pc35pbkAANh1d?format=jpg&name=900x900',
                'L1' =>  $post->id,
                'L2' => $sports->id,
                'L3' =>  $pga->id, 
                'L4' =>   $all->id,        
            ],
            [
                'name' => 'How to Make a Winning Offer on a Home',
                'description' => "Having a complete understanding of your budget and how much house you can afford is essential. The best way to know this is to get pre-approved for a loan early in the homebuying process. Only 44% of today’s prospective homebuyers are planning to apply for pre-approval, so be sure to take this step so you stand out from the crowd. Doing so make it clear to sellers you’re a serious and qualified buyer, and it can give you a competitive edge in a bidding war.It’s only natural to want the best deal you can get on a home. However, that also warns that submitting an offer that’s too low can lead sellers to doubt how serious you are as a buyer. Don’t make an offer that will be tossed out as soon as it’s received. The expertise your agent brings to this part of the process will help you stay competitive.After submitting an offer, the seller may accept it, reject it, or counter it with their own changes. In a competitive market, it’s important to stay nimble throughout the negotiation process. You can strengthen your position with an offer that includes flexible move-in dates, a higher price, or minimal contingencies",
                'user_id' =>  $businessOwner->id,
                'status' => 'active',
                'image' => 'https://53.fs1.hubspotusercontent-na1.net/hub/53/hubfs/Sales_Blog/real-estate-business-compressor.jpg?width=595&height=400&name=real-estate-business-compressor.jpg',
                'L1' =>  $post->id,
                'L2' => $business->id,
                'L3' =>  $real_estate->id, 
                'L4' =>   $all->id,        
            ],
            [
                'name' => 'Computing power has already established its place in the digital era, with almost every device and appliance being computerized',
                'description' => "Computing power has already established its place in the digital era, with almost every device and appliance being computerized. And it’s here for even more as data science experts have predicted that the computing infrastructure we are building right now will only evolve for the better in the coming years. At the same time, we have 5G already; gear up for an era of 6G with more power in our hands and devices surrounding us. Even better, computing power is generating more tech jobs in the industry but would require specialized qualifications for candidates to acquire. From data science to robotics and IT management, this field will power the largest percentage of employment in every country. The more computing our devices will need, the more technicians, IT teams, relationship managers, and the customer care economy will flourish.One essential branch under this field that you can learn today is RPA, i.e. Robotic Process Automation. At Simplilearn, RPA is all about computing and automation software that can train you for a high-paying role in the IT industry.",
                'user_id' =>  $businessOwner->id,
                'status' => 'active',
                'image' => 'https://media.istockphoto.com/id/1159763203/photo/shot-of-asian-it-specialist-using-laptop-in-data-center-full-of-rack-servers-concept-of-high.jpg?s=612x612&w=0&k=20&c=SR8yKo4z0SEZxpFRKp-03SUFouIXOa5g1DIIUwgBYWQ=',
                'L1' =>  $post->id,
                'L2' => $business->id,
                'L3' =>  $technology->id, 
                'L4' =>   $all->id,        
            ],
            [
                'name' => 'Healthcare Transformation in the Post-Coronavirus Pandemic Era',
                'description' => "Coronavirus disease 2019 (COVID-19) is a contagious disease caused by the virus severe acute respiratory syndrome coronavirus 2 (SARS-CoV-2). The first known case was identified in Wuhan, China, in December 2019. The disease quickly spread worldwide, resulting in the COVID-19 pandemic.The symptoms of COVID‑19 are variable but often include fever,cough, headache,fatigue, breathing difficulties, loss of smell, and loss of taste.The effects of the coronavirus disease 2019 (COVID-19) pandemic globally are striking as it impacts greatly the social, political, economic, and healthcare aspects of many countries. The toll of this pandemic quantified with human lives and suffering, the psychosocial impact, and the economic slowdown constitute strong reasons to translate experiences into actionable lessons, not simply to prevent similar future crises, but rather to improve the whole spectrum of population health and healthcare delivery. This is the third coronavirus (CoV) outbreak of international concern in 20 years, after the severe acute respiratory syndrome (SARS-CoV) and the Middle-East respiratory syndrome (MERS-CoV), in addition to other viral outbreaks such as Zika virus and Ebola virus over the last decade. It becomes clear that infectious diseases should be considered among the most important health hazards that we will need to continue facing in the foreseeable future. Thus, the transformation of various aspects at the individual as well as the societal and governmental levels seems inevitable.The COVID-19 pandemic has become a reality check for many aspects of healthcare systems, especially regarding their overall readiness. Public health surveillance programs and available infrastructures were shown as not consistently optimal",
                'user_id' =>  $businessOwner->id,
                'status' => 'active',
                'image' => 'https://telemedicine.arizona.edu/sites/default/files/blogpics/Lead_22.jpg',
                'L1' =>  $post->id,
                'L2' => $business->id,
                'L3' =>  $healthcare->id, 
                'L4' =>   $all->id,        
            ],
            [
                'name' => 'Denham Springs, Louisiana',
                'description' => "Denham Springs is a city in Livingston Parish, Louisiana, United States. The 2010 U.S. census placed the population at 10,215,up from 8,757 at the 2000 U. S. census. At the 2020 United States census, 9,286 people lived in the city.[3] The city is the largest area of commercial and residential development in Livingston Parish. Denham Springs and Walker are the only parish municipalities classified as cities.The area has been known as Amite Springs, Hill's Springs, and Denham Springs.",
                'user_id' =>  $businessOwner1->id,
                'status' => 'active',
                'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRJLn79i_SPL6dLu4vYr6Bqp3-zi7mcFl_V2Q&usqp=CAU',
                'L1' =>  $post->id,
                'L2' => $communities->id,
                'L3' =>  $denham_springs->id, 
                'L4' =>   $all->id,        
            ],
            [
                'name' => 'An efficient transportation system has fundamental economic benefits',
                'description' => "Transportation is the movement of goods and people form one place to another. This is an important element of quality of life of a city or nation. An efficient transportation system has fundamental economic benefits as it allows people and goods to move freely at low cost with predictable travel times. Transportation also impacts human safety and happiness as people spend much of their time moving from place to place. Pleasant and safe forms of transportation make a place more livable.A mode of transport is a solution that makes use of a certain type of vehicle, infrastructure, and operation. The transport of a person or of cargo may involve one mode or several of the modes, with the latter case being called inter-modal or multi-modal transport. Each mode has its own advantages and disadvantages, and will be chosen on the basis of cost, capability, and route.Governments deal with the way the vehicles are operated, and the procedures set for this purpose, including financing, legalities, and policies. In the transport industry, operations and ownership of infrastructure can be either public or private, depending on the country and mode.Passenger transport may be public, where operators provide scheduled services, or private. Freight transport has become focused on containerization, although bulk transport is used for large volumes of durable items. Transport plays an important part in economic growth and globalization, but most types cause air pollution and use large amounts of land. While it is heavily subsidized by governments, good planning of transport is essential to make traffic flow and restrain urban sprawl.The following are common elements of transportation.Aircraft, Autonomous ships, Boats, Bullet trains, Buses, Bicycles, Cars",
                'user_id' =>  $businessOwner1->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1473042904451-00171c69419d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8dHJhbnNwb3J0fGVufDB8fDB8fHww&auto=format&fit=crop&w=600&q=60',
                'L1' =>  $post->id,
                'L2' => $business->id,
                'L3' =>  $transportation->id, 
                'L4' =>   $all->id,        
            ],
            [
                'name' => 'The Art and Science of Agriculture',
                'description' => "Agriculture encompasses crop and livestock production, aquaculture, fisheries and forestry for food and non-food products.Agriculture is the art and science of cultivating the soil, growing crops, and raising livestock. It includes the preparation of plant and animal products for people to use and their distribution to markets. Agriculture provides most of the world’s food and fabrics. Cotton, wool, and leather are all agricultural products. Agriculture also provides wood for construction and paper products.These products, as well as the agricultural methods used, may vary from one part of the world to another.Over centuries, the growth of agriculture supported the development of cities.Agriculture was the key development in the rise of sedentary human civilization, whereby farming of domesticated species created food surpluses that enabled people to live in cities.The major agricultural products can be broadly grouped into foods, fibers, fuels, and raw materials (such as rubber). Food classes include cereals (grains), vegetables, fruits, cooking oils, meat, milk, eggs, and fungi. Global agricultural production amounts to approximately 11 billion tonnes of food. Before agriculture became widespread, hunting and gathering was how people fed themselves. Between 10,000 and 12,000 years ago, people gradually learned how to grow cereal and root crops, and settled down to a life based on farming.Eventually, much of Earth’s population became dependent on agriculture. Scholars are not sure why this shift to farming took place, but it may have occurred because of climate change.When people began growing crops, they also continued to adapt animals and plants for human use. Adapting wild plants and animals for people to use is called domestication. Hunter-gatherers began to domesticate animals and change the natural environment to grow more food even before settled farming became widespread.Barley, wheat, legumes, vetch, and flax were among the first plants to be domesticated.",
                'user_id' =>  $businessOwner1->id,
                'status' => 'active',
                'image' => 'https://images.nationalgeographic.org/image/upload/t_edhub_resource_key_image/v1638891477/EducationHub/photos/rastafarian-agriculture-fair.jpg',
                'L1' =>  $post->id,
                'L2' => $farming->id,
                'L3' =>  $agriculture->id, 
                'L4' =>   $all->id,        
            ],
            [
                'name' => 'Artificial Intelligence (AI) and Machine Learning',
                'description' => "Artificial intelligence (AI) is the intelligence of machines or software, as opposed to the intelligence of human beings or animals. AI applications include advanced web search engines (e.g., Google Search), recommendation systems, understanding human speech, self-driving cars (e.g., Waymo), generative or creative tools, and competing at the highest level in strategic games.Artificial Intelligence, or AI, has already received a lot of buzz in the past decade, but it continues to be one of the new technology trends because of its notable effects on how we live, work and play are only in the early stages. AI is already known for its superiority in image and speech recognition, navigation apps, smartphone personal assistants, ride-sharing apps and so much more.Other than that AI will be used further to analyze interactions to determine underlying connections and insights, to help predict demand for services like hospitals enabling authorities to make better decisions about resource utilization, and to detect the changing patterns of customer behaviour by analyzing data in near real-time, driving revenues and enhancing personalized experiences.Machine Learning the subset of AI, is also being deployed in all kinds of industries, creating a huge demand for skilled professionals. Forrester predicts AI, machine learning, and automation will create 9 percent of new U.S. jobs by 2025, jobs including robot monitoring professionals, data scientists, automation specialists, and content curators, making it another new technology trend you must keep in mind tool.Machine learning (ML) is an umbrella term for solving problems for which development of algorithms by human programmers would be cost-prohibitive, and instead the problems are solved by helping machines 'discover' their 'own' algorithms.Recently, generative artificial neural networks have been able to surpass results of many previous approaches.Machine learning approaches have been applied to large language models, computer vision, speech recognition, email filtering, agriculture and medicine, where it is too costly to develop algorithms to perform the needed tasks.",
                'user_id' =>  $businessOwner1->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1516192518150-0d8fee5425e3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8YXJ0aWZpY2FsJTIwaW50ZWxsaWdlbmNlfGVufDB8fDB8fHww&auto=format&fit=crop&w=600&q=60',
                'L1' =>  $post->id,
                'L2' => $business->id,
                'L3' =>  $technology->id, 
                'L4' =>   $all->id,        
            ],
            [
                'name' => 'Robotic Process Automation (RPA)',
                'description' => "Like AI and Machine Learning, Robotic Process Automation, or RPA, is another technology that is automating jobs. RPA is the use of software to automate business processes such as interpreting applications, processing transactions, dealing with data, and even replying to emails. RPA automates repetitive tasks that people used to do.Although Forrester Research estimates RPA automation will threaten the livelihood of 230 million or more knowledge workers or approximately 9 percent of the global workforce, RPA is also creating new jobs while altering existing jobs. McKinsey finds that less than 5 percent of occupations can be totally automated, but about 60 percent can be partially automated.For you as an IT professional looking to the future and trying to understand latest technology trends, RPA offers plenty of career opportunities, including developer, project manager, business analyst, solution architect and consultant. And these jobs pay well. Making it the next technology trend you must keep a watch on.In traditional workflow automation tools, a software developer produces a list of actions to automate a task and interface to the back end system using internal application programming interfaces (APIs) or dedicated scripting language. In contrast, RPA systems develop the action list by watching the user perform that task in the application's graphical user interface (GUI), and then perform the automation by repeating those tasks directly in the GUI. This can lower the barrier to the use of automation in products that might not otherwise feature APIs for this purpose.RPA tools have strong technical similarities to graphical user interface testing tools. These tools also automate interactions with the GUI, and often do so by repeating a set of demonstration actions performed by a user. RPA tools differ from such systems in that they allow data to be handled in and between multiple applications, for instance, receiving email containing an invoice, extracting the data, and then typing that into a bookkeeping system.",
                'user_id' =>  $businessOwner1->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1535378273068-9bb67d5beacd?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NXx8YXJ0aWZpY2FsJTIwaW50ZWxsaWdlbmNlfGVufDB8fDB8fHww&auto=format&fit=crop&w=600&q=60',
                'L1' =>  $post->id,
                'L2' => $business->id,
                'L3' =>  $technology->id, 
                'L4' =>   $all->id,        
            ],
            [
                'name' => 'Economics',
                'description' => "Economics is a social science that studies the production, distribution, and consumption of goods and services.Economics focuses on the behaviour and interactions of economic agents and how economies work. Microeconomics analyzes what's viewed as basic elements in the economy, including individual agents and markets, their interactions, and the outcomes of interactions. Individual agents may include, for example, households, firms, buyers, and sellers. Macroeconomics analyzes the economy as a system where production, consumption, saving, and investment interact, and factors affecting it: employment of the resources of labour, capital, and land, currency inflation, economic growth, and public policies that have impact on these elements.Other broad distinctions within economics include those between positive economics, describing 'what is', and normative economics, advocating 'what ought to be'; between economic theory and applied economics; between rational and behavioural economics; and between mainstream economics and heterodox economics",
                'user_id' =>  $businessOwner2->id,
                'status' => 'active',
                'image' => 'https://plus.unsplash.com/premium_photo-1661604346220-5208d18cb34e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8ZWNvbm9taWNzfGVufDB8fDB8fHww&auto=format&fit=crop&w=600&q=60',
                'L1' =>  $post->id,
                'L2' => $opinion->id,
                'L3' =>  $economics->id, 
                'L4' =>   $all->id,        
            ],
            [
                'name' => 'Culture as shared patterns of behaviors and interactions,',
                'description' => "Culture is an umbrella term which encompasses the social behavior, institutions, and norms found in human societies, as well as the knowledge, beliefs, arts, laws, customs, capabilities, and habits of the individuals in these groups. Culture is often originated from or attributed to a specific region or location.Humans acquire culture through the learning processes of enculturation and socialization, which is shown by the diversity of cultures across societies.A cultural norm codifies acceptable conduct in society; it serves as a guideline for behavior, dress, language, and demeanor in a situation, which serves as a template for expectations in a social group. Accepting only a monoculture in a social group can bear risks, just as a single species can wither in the face of environmental change, for lack of functional responses to the change. Thus in military culture, valor is counted a typical behavior for an individual and duty, honor, and loyalty to the social group are counted as virtues or functional responses in the continuum of conflict. In the practice of religion, analogous attributes can be identified in a social group.Cultural change, or repositioning, is the reconstruction of a cultural concept of a society. Cultures are internally affected by both forces encouraging change and forces resisting change. Cultures are externally affected via contact between societies.In the humanities, one sense of culture as an attribute of the individual has been the degree to which they have cultivated a particular level of sophistication in the arts, sciences, education, or manners. The level of cultural sophistication has also sometimes been used to distinguish civilizations from less complex societies. Such hierarchical perspectives on culture are also found in class-based distinctions between a high culture of the social elite and a low culture, popular culture, or folk culture of the lower classes, distinguished by the stratified access to cultural capital.",
                'user_id' =>  $businessOwner2->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1543906965-f9520aa2ed8a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8Y3VsdHVyZXxlbnwwfHwwfHx8MA%3D%3D&auto=format&fit=crop&w=600&q=60',
                'L1' =>  $post->id,
                'L2' => $opinion->id,
                'L3' =>  $culture->id, 
                'L4' =>   $all->id,        
            ],
            [
                'name' => 'Quantum Computing',
                'description' => "Next remarkable technology trend is quantum computing, which is a form of computing that takes advantage of quantum phenomena like superposition and quantum entanglement. This amazing technology trend is also involved in preventing the spread of the coronavirus, and to develop potential vaccines, thanks to its ability to easily query, monitor, analyze and act on data, regardless of the source. Another field where quantum computing is finding applications is banking and finance, to manage credit risk, for high-frequency trading and fraud detection.Quantum computers are now a multitude times faster than regular computers and huge brands like Splunk, Honeywell, Microsoft, AWS, Google and many others are now involved in making innovations in the field of Quantum Computing. The revenues for the global quantum computing market are projected to surpass $2.5 billion by 2029. And to make a mark in this new trending technology, you need to have experience with quantum mechanics, linear algebra, probability, information theory, and machine learning.The basic unit of information in quantum computing is the qubit, similar to the bit in traditional digital electronics. Unlike a classical bit, a qubit can exist in a superposition of its two 'basis' states, which loosely means that it is in both states simultaneously. When measuring a qubit, the result is a probabilistic output of a classical bit. If a quantum computer manipulates the qubit in a particular way, wave interference effects can amplify the desired measurement results. The design of quantum algorithms involves creating procedures that allow a quantum computer to perform calculations efficiently and quickly.",
                'user_id' =>  $businessOwner2->id,
                'status' => 'active',
                'image' => 'https://plus.unsplash.com/premium_photo-1689801528484-6fc4743b96e0?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8UXVhbnR1bSUyMENvbXB1dGluZ3xlbnwwfHwwfHx8MA%3D%3D&auto=format&fit=crop&w=600&q=60',
                'L1' =>  $post->id,
                'L2' => $business->id,
                'L3' =>  $technology->id, 
                'L4' =>   $all->id,        
            ],
            [
                'name' => 'Livestock production makes a major contribution to agriculture value added services.',
                'description' => "Livestock are the domesticated animals raised in an agricultural setting to provide labor and produce diversified products for consumption such as meat, eggs, milk, fur, leather, and wool. The term is sometimes used to refer solely to animals who are raised for consumption, and sometimes used to refer solely to farmed ruminants, such as cattle, sheep, goats. Horses are considered livestock in the United States.The USDA classifies pork, veal, beef, and lamb (mutton) as livestock, and all livestock as red meat. Poultry and fish are not included in the category.The breeding, maintenance, slaughter and general subjugation of livestock, called animal husbandry, is a part of modern agriculture and has been practiced in many cultures since humanity's transition to farming from hunter-gatherer lifestyles. Animal husbandry practices have varied widely across cultures and time periods. It continues to play a major economic and cultural role in numerous communities.Livestock farming practices have largely shifted to intensive animal farming.Intensive animal farming increases the yield of the various commercial outputs, but also negatively impacts animal welfare, the environment, and public health.In particular, beef, dairy and sheep are an outsized source of greenhouse gas emissions from agriculture.",
                'user_id' =>  $businessOwner2->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1507103011901-e954d6ec0988?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTB8fGxpdmVzdG9ja3xlbnwwfHwwfHx8MA%3D%3D&auto=format&fit=crop&w=600&q=60',
                'L1' =>  $post->id,
                'L2' => $farming->id,
                'L3' =>  $livestock->id, 
                'L4' =>   $all->id,        
            ],
            [
                'name' => 'Politics means the activities of the government or people who try to influence the way a country is governed.',
                'description' => "Politics ('affairs of the cities') is the set of activities that are associated with making decisions in groups, or other forms of power relations among individuals, such as the distribution of resources or status. The branch of social science that studies politics and government is referred to as political science.It may be used positively in the context of a 'political solution' which is compromising and nonviolent,or descriptively as 'the art or science of government', but also often carries a negative connotation.The concept has been defined in various ways, and different approaches have fundamentally differing views on whether it should be used extensively or limitedly, empirically or normatively, and on whether conflict or co-operation is more essential to it.A variety of methods are deployed in politics, which include promoting one's own political views among people, negotiation with other political subjects, making laws, and exercising internal and external force, including warfare against adversaries.Politics is exercised on a wide range of social levels, from clans and tribes of traditional societies, through modern local governments, companies and institutions up to sovereign states, to the international level.In modern nation states, people often form political parties to represent their ideas. Members of a party often agree to take the same position on many issues and agree to support the same changes to law and the same leaders. An election is usually a competition between different parties.",
                'user_id' =>  $businessOwner2->id,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1520452112805-c6692c840af0?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8cG9saXRpY3N8ZW58MHx8MHx8fDA%3D&auto=format&fit=crop&w=600&q=60',
                'L1' =>  $post->id,
                'L2' => $opinion->id,
                'L3' =>  $politics->id, 
                'L4' =>   $all->id,        
            ],
        ];

        foreach($data as $item) {
            ModuleSessionManager::setModule('posts');
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