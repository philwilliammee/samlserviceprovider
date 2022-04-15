<?php

namespace PhilWilliammee\SamlServiceProvider\Http\Controllers;

use PhilWilliammee\SamlServiceProvider\SamlServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        // Firewall request to only allowed origins.
        $origin = $request->header('Origin');
        $request_uri = $request->getRequestUri();
        if (!$origin) {
            Log::error("No Origin header . Request: " . $request_uri);
            return response()->json(['error' => 'Origin header is missing'], 400);
        }
        $destination = config('samlserviceprovider.destination');
        $destination_url = parse_url($destination);
        $origin_url = parse_url($origin);
        if ($destination_url['host'] !== $origin_url['host']) {
            $msg_str = "Origin header does not match destination.";
            $msg_str .= " Origin: {$origin_url['host']}";
            $msg_str .= "Destination: {$destination_url['host']}";
            $msg_str .= " Request: {$request_uri}";
            Log::error($msg_str);
            return response()->json(['error' => 'Origin header is not allowed'], 400);
        }

        // If scheme is not https abort
        if ($destination_url['scheme'] !== 'https') {
            Log::error("Destination scheme is not https. Request: " . $request_uri);
            return response()->json(['error' => 'Destination url is not https'], 400);
        }

        $encoded_saml_response = $request->get('SAMLResponse');
        $relay_state = $request->get('RelayState');
        $is_successful = SamlServiceProvider::processAcsResponse($encoded_saml_response);
        if (!$is_successful) {
            Log::error("SAML Response is not valid. Request: " . json_encode($request->all()));
        }
        return redirect($relay_state);
    }

    public function metadata()
    {
        // stream the metadata download
        $metadata = SamlServiceProvider::getMetadata();

        $callback = function () use ($metadata) {
            $file = fopen('php://output', 'w');
            fwrite($file, $metadata);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/xml',
            'Content-Disposition' => 'attachment; filename="metadata.xml"',
            ]);
    }
}
