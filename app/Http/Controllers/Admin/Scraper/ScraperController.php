<?php

namespace App\Http\Controllers\Admin\Scraper;

use App\Jobs\Scraper;
use App\Models\Product;;

use App\Jobs\TagScraper;
use App\Models\Business;;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class ScraperController extends Controller
{
    public function index()
    {
        // // get values
        // $curl = curl_init();

        // curl_setopt_array($curl, [
        //     CURLOPT_URL => "https://text-index-862027e.svc.gcp-starter.pinecone.io/vectors/fetch?ids=x2",
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => "",
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 30,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => "GET",
        //     CURLOPT_HTTPHEADER => [
        //         "Api-Key: aee58608-9838-406c-85b0-2f9be1fa3b8f",
        //         "accept: application/json"
        //     ],
        // ]);

        // $response = curl_exec($curl);
        // $err = curl_error($curl);

        // curl_close($curl);

        // if ($err) {
        //     return "cURL Error #:" . $err;
        // } else {
        //     return $response;
        // }

        // store values
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://products-862027e.svc.gcp-starter.pinecone.io/vectors/upsert",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                'vectors' => [
                    [
                        'metadata' => [
                            'tilte' => 'T-shirt',
                            'description' => 'New T-shirt in the market',
                            'tags' => ['Retail', 'Fashion']
                        ],
                        'id' => 'v-1',
                        'values' => [
                            0.1,
                            0.2
                        ]
                    ]
                ]
            ]),
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "content-type: application/json",
                "Api-Key: aee58608-9838-406c-85b0-2f9be1fa3b8f"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
        return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }
}
