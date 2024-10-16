<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class IDMeController extends Controller
{
    // Redirect the user to ID.me authorization page
    public function redirectToIDMe()
    {
        $client_id = env('IDME_CLIENT_ID');
        $redirect_uri = env('IDME_REDIRECT_URI');
        $scope = env('IDME_SCOPE');

        $authorization_url = env('IDME_AUTHORIZE_URL') . "?client_id={$client_id}&redirect_uri={$redirect_uri}&response_type=code&scope={$scope}";
        echo $authorization_url;
        dd($authorization_url);
        return redirect($authorization_url);
    }

    // Handle the callback from ID.me
    public function handleCallback(Request $request)
    {
        $code = $request->get('code');

        if (!$code) {
            return redirect('/')->with('error', 'Authorization code not found.');
        }

        $client = new Client();
        // Step 1: Exchange the authorization code for an access token
        $response = $client->post(env('IDME_TOKEN_URL'), [
            'form_params' => [
                'client_id' => env('IDME_CLIENT_ID'),
                'client_secret' => env('IDME_CLIENT_SECRET'),
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => env('IDME_REDIRECT_URI'),
            ]
        ]);

        $body = json_decode($response->getBody(), true);
        $access_token = $body['access_token'];

        // Step 2: Fetch user info from ID.me using the access token
        $userResponse = $client->get(env('IDME_USER_INFO_URL'), [
            'headers' => [
                'Authorization' => "Bearer {$access_token}",
            ]
        ]);

        $user_info = json_decode($userResponse->getBody(), true);

        // Handle and process the user information
        // For now, we'll just return the user info as a JSON response
        return response()->json($user_info);
    }
}
