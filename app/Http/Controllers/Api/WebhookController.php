<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Url;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    //

    public function index() {
        return Url::all();
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
