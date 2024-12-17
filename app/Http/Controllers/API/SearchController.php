<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use DonatelloZa\RakePlus\RakePlus;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use App\Models\PublicProfile;
use App\Models\StandardTag;
use App\Transformers\Search\SearchTransformer;
use App\Transformers\PublicProfileTransformer;

class SearchController extends Controller
{
    protected $keywordsArray = [];
    protected $keywordString = '';
    protected $l1 = null;
    protected $l2 = null;
    protected $l3 = null;

    public function __construct()
    {
        // extracting keywords from string in array form
        $this->keywordsArray = RakePlus::create(\request()->keyword)->keywords();
        $this->keywordString = implode(' ', $this->keywordsArray);

        if (request()->input('L1')) {
            // fetching L1 tag id based on slug or id
            $this->l1 = StandardTag::where('id', request()->input('L1'))
                ->orWhere('slug', request()->input('L1'))->first()?->id;
        }

        if (request()->input('L2')) {
            // fetching L2 tag id based on slug or id
            $this->l2 = StandardTag::where('id', request()->input('L2'))
                ->orWhere('slug', request()->input('L2'))->first()?->id;
        }

        if (request()->input('L3')) {
            // fetching L3 tag id based on slug or id
            $this->l3 = StandardTag::where('id', request()->input('L3'))
                ->orWhere('slug', request()->input('L3'))->first()?->id;
        }
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(SearchRequest $request)
    {
        $limit = request()->input('limit') ? request()->input('limit') : 5;

        if(request()->input('type') == 'persona') {
            $profiles = $this->searchPersona($limit);
            $paginate = apiPagination($profiles, $limit);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => (new PublicProfileTransformer)->transformCollection($profiles),
                'meta' => $paginate,
            ], JsonResponse::HTTP_OK);
        }

        // // generating filteration query
        // $logic = '';

        // foreach ($this->keywordsArray as $index => $keyword) {
        //     $logicString = $index === 0
        //         ? 'product_priorities.P2 LIKE "%' . $keyword . '%" OR product_priorities.P3 LIKE "%' . $keyword . '%" OR product_priorities.P4 LIKE "%' . $keyword . '%"'
        //         :' OR product_priorities.P2 LIKE "%' . $keyword . '%" OR product_priorities.P3 LIKE "%' . $keyword . '%" OR product_priorities.P4 LIKE "%' . $keyword . '%"';

        //     $logic .= $logicString;
        // }
        // return $logic;
        $hierarchies = DB::table('tag_hierarchies')->select(

            // level one tag information
            'level_one.id as level_one_id',
            'level_one.slug as level_one_slug',
            'level_one.name as level_one_name',

            // level two tag information
            'level_two.id as level_two_id',
            'level_two.slug as level_two_slug',
            'level_two.name as level_two_name',

            // level three tag information
            'level_three.id as level_three_id',
            'level_three.slug as level_three_slug',
            'level_three.name as level_three_name',

            // level four tag information
            'standard_tags.id as level_four_id',
            'standard_tags.slug as level_four_slug',
            'standard_tags.name as level_four_name',

            // fetching products count
            // DB::raw("(
            //     SELECT COUNT(product_priorities.id) FROM product_priorities
            //     WHERE 
            //     product_priorities.P1 LIKE CONCAT('%', level_one_name, '%')
            //     AND product_priorities.P1 LIKE CONCAT('%', level_two_name, '%')
            //     AND product_priorities.P1 LIKE CONCAT('%', level_three_name, '%')
            //     AND product_priorities.P1 LIKE CONCAT('%', level_four_name, '%')
            //     AND (
            //         MATCH (product_priorities.P1) AGAINST ('$this->keywordString' IN BOOLEAN MODE)
            //         OR
            //         MATCH (product_priorities.P2) AGAINST ('$this->keywordString' IN BOOLEAN MODE)
            //         OR
            //         MATCH (product_priorities.P3) AGAINST ('$this->keywordString' IN BOOLEAN MODE)
            //         OR
            //         MATCH (product_priorities.P4) AGAINST ('$this->keywordString' IN BOOLEAN MODE)
            //     )
            // ) AS products_count"),

            // fetch relevancy
            DB::raw("(
                SELECT 
                    (( " . $this->buildJsonSearchQuery('product_priorities.P1') . " ) +
                    ( " . $this->buildJsonSearchQuery('product_priorities.P2') . " ) +
                    ( " . $this->buildJsonSearchQuery('product_priorities.P3') . " ) +
                    ( " . $this->buildJsonSearchQuery('product_priorities.P4') . " )) AS word_count
                FROM 
                    product_priorities 
                WHERE 
                    product_priorities.P1 LIKE CONCAT('%', level_one_name, '%') AND
                    product_priorities.P1 LIKE CONCAT('%', level_two_name, '%') AND
                    product_priorities.P1 LIKE CONCAT('%', level_three_name, '%') AND
                    product_priorities.P1 LIKE CONCAT('%', level_four_name, '%') AND
                    (
                        MATCH(product_priorities.P1) AGAINST('$this->keywordString' IN BOOLEAN MODE) OR 
                        MATCH(product_priorities.P2) AGAINST('$this->keywordString' IN BOOLEAN MODE) OR 
                        MATCH(product_priorities.P3) AGAINST('$this->keywordString' IN BOOLEAN MODE) OR 
                        MATCH(product_priorities.P4) AGAINST('$this->keywordString' IN BOOLEAN MODE)
                    )
                ORDER BY word_count DESC
                LIMIT 1
            ) AS word_count")
        )->join(
            'standard_tags AS level_one', 'tag_hierarchies.L1', '=', 'level_one.id'
        )->join(
            'standard_tags AS level_two', 'tag_hierarchies.L2', '=', 'level_two.id'
        )->join(
            'standard_tags AS level_three', 'tag_hierarchies.L3', '=', 'level_three.id'
        )->join(
            'tag_hierarchies_standard_tag', 'tag_hierarchies.id', '=', 'tag_hierarchies_standard_tag.tag_hierarchy_id'
        )->join(
            'standard_tags', 'tag_hierarchies_standard_tag.standard_tag_id', '=', 'standard_tags.id'
        )->join(
            'product_standard_tag', 'standard_tags.id', '=', 'product_standard_tag.standard_tag_id'
        )->join(
            'products', 'product_standard_tag.product_id', '=', 'products.id'
        )
        ->join(
            'product_priorities', 'products.id', '=', 'product_priorities.product_id'
        )->when($this->l1, function ($query) {
            // if level 1 is selected
            $query->where('tag_hierarchies.L1', $this->l1);
        })->when($this->l2, function ($query) {
            // if level 2 is selected
            $query->where('tag_hierarchies.L2', $this->l2);
        })->when($this->l3, function ($query) {
            // if level 3 is selected
            $query->where('tag_hierarchies.L3', $this->l3);
        })->where(function ($query) {
            foreach ($this->keywordsArray as $keyword) {
                $query->orWhere('product_priorities.P1', 'like', "%$keyword%");
                $query->orWhere('product_priorities.P2', 'like', "%$keyword%");
                $query->orWhere('product_priorities.P3', 'like', "%$keyword%");
                $query->orWhere('product_priorities.P4', 'like', "%$keyword%");
            }
        })->whereNotNull(['L2','L3'])->groupBy(
            'level_one_id',
            'level_two_id',
            'level_three_id',
            'level_four_id'
        )->whereRaw('
        product_priorities.P1 LIKE CONCAT("%", level_one.name, "%")
        AND product_priorities.P1 LIKE CONCAT("%", level_two.name, "%") 
        AND product_priorities.P1 LIKE CONCAT("%", level_three.name, "%")
        AND product_priorities.P1 LIKE CONCAT("%", standard_tags.name, "%")
        AND (
            MATCH (product_priorities.P1) AGAINST (? IN BOOLEAN MODE)
            OR
            MATCH (product_priorities.P2) AGAINST (? IN BOOLEAN MODE)
            OR
            MATCH (product_priorities.P3) AGAINST (? IN BOOLEAN MODE)
            OR
            MATCH (product_priorities.P4) AGAINST (? IN BOOLEAN MODE)
        )
    ',[$this->keywordString, $this->keywordString, $this->keywordString, $this->keywordString])
        // ->havingRaw('products_count > 0');
        ->orderByRaw('word_count desc');
        $hierarchies = $hierarchies->paginate($limit);
        $paginate = apiPagination($hierarchies, $limit);
        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'data' => (new SearchTransformer)->transformCollection($hierarchies, ['searchString' => $this->keywordString]),
            'meta' => $paginate,
        ], JsonResponse::HTTP_OK);
    }

    protected function buildJsonSearchQuery($column){
        $jsonSearchQuery = '';
        switch ($column) {
            case 'product_priorities.P1':
                $multiplyBy = 1000;
                break;
            case 'product_priorities.P2':
                $multiplyBy = 100;
                break;
            case 'product_priorities.P3':
                $multiplyBy = 10;
                break;
            default:
                $multiplyBy = 1;
                break;
        }
        foreach ($this->keywordsArray as $keyword) {
            $jsonSearchQuery .= "IF(JSON_VALID($column) AND JSON_LENGTH($column) > 0 AND JSON_SEARCH($column, 'all', '$keyword') IS NOT NULL, 1, 0) * $multiplyBy + ";
        }
        return rtrim($jsonSearchQuery, ' + ');
    }

    private function searchPersona($limit) {
        $keywords = explode(' ', $this->keywordString);

        $personas = PublicProfile::where('user_id', '<>', auth('sanctum')->user()?->id)
            ->where(function($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('nick_name', 'LIKE', '%' . $keyword . '%');
                }
            })
            ->with(['followers' => function($subquery) {
                $subquery->where('public_profiles.id', request()->input('profile_id'));
            }])
            ->withCount(['followers' => function($query) {
                $query->where('status', 'accepted');
            }, 'products'])
            ->paginate($limit);

        return $personas;
    }
}
