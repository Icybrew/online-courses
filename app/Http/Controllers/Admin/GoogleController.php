<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use League\OAuth2\Client\Provider\Google;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class GoogleController
 * @package App\Http\Controllers\Admin
 */
class GoogleController extends Controller
{
    public function authorize(Request $request, Google $google)
    {
        $user = $request->getSession()->get('user');

        if (!empty($_GET['error'])) {
            // Got an error, probably user denied access
            return redirect()->route('admin.index')->withErrors(['error' => htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8')]);
        } elseif (empty($_GET['code'])) {

            if (!isset($user->google) || (isset($user->google) && date('Y-m-d H:i:s', $user->google->getExpires()) < date('Y-m-d H:i:s'))) {
                // If we don't have an authorization code then get one
                $authUrl = $google->getAuthorizationUrl();
                $_SESSION['oauth2state'] = $google->getState();
                header('Location: ' . $authUrl);
                exit;
            } else {
                dd($user->google->getRefreshToken());
                return redirect()->route('admin.index')->with(['success' => 'Already authorized']);
            }

        } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

            // State is invalid, possible CSRF attack in progress
            unset($_SESSION['oauth2state']);
            exit('Invalid state');

        } else {

            // Try to get an access token (using the authorization code grant)
            $token = $google->getAccessToken('authorization_code', [
                'code' => $_GET['code']
            ]);

            $user->google = $token;

            return redirect()->route('admin.index')->with(['success' => 'Authorization successful']);

            // Optional: Now you have a token you can look up a users profile data
            try {

                // We got an access token, let's now get the owner details
                $ownerDetails = $google->getResourceOwner($token);

                //_dd($token);

                // Use these details to create a new profile
                printf('Hello %s!', $ownerDetails->getFirstName());

            } catch (Exception $e) {

                // Failed to get user details
                exit('Something went wrong: ' . $e->getMessage());

            }

            // Use this to interact with an API on the users behalf
            echo '<br>Token: ' . $token->getToken() . '<br>';

            // Use this to get a new access token if the old one expires
            echo '<br>Refresh token: ' . $token->getRefreshToken() . '<br>';

            // Unix timestamp at which the access token expires
            echo '<br>Token expires: ' . $token->getExpires() . '<br>';
        }
    }
}
