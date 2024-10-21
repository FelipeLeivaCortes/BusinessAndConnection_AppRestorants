<?php

namespace App\Http\Controllers;

use Ably\AblyRest;
use Illuminate\Http\Request;

class AblyController extends Controller {

    public function auth(Request $request) {
        $client       = new AblyRest(get_option('ably_api_key'));
        $tokenDetails = $client->auth->requestToken();
        $token = $client->auth->createTokenRequest();

        return response()->json($token);
    }

}
