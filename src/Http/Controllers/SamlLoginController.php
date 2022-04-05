<?php

namespace PhilWilliammee\SamlServiceProvider\Http\Controllers;

use PhilWilliammee\SamlServiceProvider\SamlServiceProvider;
use Illuminate\Http\Request;

class SamlLoginController extends Controller
{

    /** creates an auth request and redirects to IDP */
    public function login(): View|Closure|string
    {
        $session_id = request()->get('session_id');
        $redirect_url = request()->get('redirect_url', "/");
        if (!$session_id) {
            return redirect()->back()->withErrors(['session_id' => 'Session ID is missing']);
        }

        return SamlServiceProvider::login($session_id, $redirect_url);
    }

    /** Deletes the SamlServiceProvider id from the database */
    public function logout()
    {
        $session_id = request()->get('session_id');
        $message = "";
        if ($session_id) {
            SamlServiceProvider::logout($session_id);
            $message = "Logged out";
        } else {
            $message = "No session_id";
        }

        return redirect('/')->with('message', $message);
    }

    /**
     * Handles the API post Assertion Consumer Service request from the IdP
     *
     * @return void
     */
    public function samlAcs(Request $request)
    {
        $encoded_saml_response = $request->get('SAMLResponse');
        $relay_state = $request->get('RelayState');
        SamlServiceProvider::processAcsResponse($encoded_saml_response);
        return redirect($relay_state);
    }

    public function metadata()
    {
        $metadata = SamlServiceProvider::getMetadata();
        return response($metadata, 200)
            ->header('Content-Type', 'text/xml');
    }
}
