<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advertise;
use App\Models\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WebhookController extends Controller
{
    //

    public function index() {
        return Url::all();
    }

    public function message() {

        $hasAlertToNotify = Advertise::whereNull('alert_date')->first();
        if (empty($hasAlertToNotify)) {
        return 'no alerts to notify';
        }

        $ads = Advertise::where('url_id', $hasAlertToNotify->url_id)->whereNull('alert_date')->get();


        $adsToNotify = [];
        foreach($ads as $ad) {
            $ad->alert_date = date('Y-m-d H:i:s', time());
            $ad->save();
            $adsToNotify[] = $ad;
        }

        $postData =  ['payload' =>
            [
            'url' => Url::find($hasAlertToNotify->url_id),
            'ads' => $ads,
            ]
        ];


        $response = Http::acceptJson()
        ->withHeaders([
			'Content-Type' => 'application/json',
		])
        ->post('https://alerta-preco.facilita.dev/api/webhook', $postData)->collect();

        return ['postData: ' => $postData, 'response' =>$response];


    }

    public function store(Request $r) {
        $url = trim($r->url);

        $hasPreviouslyAddedUrl = Url::firstOrNew([ 'url' => $url]);
        $hasPreviouslyAddedUrl->active = true;
        $hasPreviouslyAddedUrl->save();

        return response(['error' => false, 'message' => 'Url Added Successfully'],200);
    }

    public function remove(Request $r) {
        $url = trim($r->url);
        $currentDatabaseUrl = Url::where('url',$url)->first();
        if (!$currentDatabaseUrl) {
            return response(['error' => true, 'message' => 'Url Does not Exists'],400);
        }

        $currentDatabaseUrl->delete();
    }
}
