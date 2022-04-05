<?php

namespace PhilWilliammee\SamlServiceProvider;

use Closure;
use Illuminate\Contracts\View\View;
use PhilWilliammee\SamlServiceProvider\Utils\SamlServiceProviderBase;
use PhilWilliammee\SamlServiceProvider\Models\SamlLogin;

class SamlServiceProvider
{
    private SamlServiceProviderBase $saml_sp;

    /**
     * Returns a view that automatically sends the user to the IDP SSO service.
     * The IDP SSO will then redirect back to the relay_state url.
     *
     * @param string $session_id
     * @param string $relay_state
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public static function login(string $session_id, string $relay_state): View|Closure|string
    {
        return view('samlserviceprovider::login', [
            'session_id' => $session_id,
            'relay_state' => $relay_state,
        ]);
    }

    /**
     * instantiates the SamlServiceProvider class
     * Maps the users session id to the SamlServiceProvider id and records it in the database for later use.
     *
     * @param string $session_id
     * @return SamlLogin
     */
    public function createLogin(string $session_id): SamlLogin
    {
        $config = config('samlserviceprovider');
        $this->saml_sp = new SamlServiceProviderBase($config);
        return SamlLogin::updateOrCreate([
            'session_id' => $session_id,
        ], [
            'saml_id' => $this->saml_sp->getId(),
        ]);
    }

    /**
     * Generates the encrypted SamlServiceProvider xml for the saml IDP request
     *
     * @return string
     */
    public function getAuthenticationRequest(): string
    {
        return $this->saml_sp->getAuthenticationRequest();
    }

    /**
     * Retrieves the users attributes that were stored by the ACS in the database.
     *
     * @param string $session_id
     * @return string
     */
    public static function getAttributes($session_id):? array
    {
        $login = SamlLogin::firstWhere([
            'session_id' => $session_id,
        ]);

        if (!$login) {
            return null;
        }

        $xml_string = $login->xml_string;
        $decoded_id = $xml_string ? SamlServiceProviderBase::getKeyFromSamlResponse($xml_string) : null;

        if ($login->saml_id !== $decoded_id) {
            return null;
        }

        $user_data = SamlServiceProviderBase::getDataFromXmlResponse($xml_string);

        return $user_data;
    }

    /**
     * Deletes the SamlServiceProvider id from the database
     *
     * @param string $session_id
     * @return void
     */
    public static function logout($session_id)
    {
        self::cleanUp($session_id);
    }

    /**
     * Assertion Consumer Service (ACS)
     * Handles the IDP assertion for the user and records the response in the database.
     */
    public static function processAcsResponse($encoded_saml_response): bool
    {
        $xml_string = SamlServiceProviderBase::decodeSamlResponse($encoded_saml_response);
        $decoded_id = SamlServiceProviderBase::getKeyFromSamlResponse($xml_string);
        $not_on_or_after = SamlServiceProviderBase::getNotOnOrAfterFromSamlResponse($xml_string);
        return SamlLogin::firstWhere('saml_id', $decoded_id)
            ->update([
                'xml_string' => $xml_string,
                'not_on_or_after' => $not_on_or_after,
            ]);
    }

    public static function getMetadata()
    {
        $config = config('samlserviceprovider');
        $saml_sp = new SamlServiceProviderBase($config);
        return $saml_sp->getMetadata();
    }

    public static function cleanUp($session_id)
    {
        SamlLogin::where([
            'session_id' => $session_id,
        ])->orWhere('not_on_or_after', '<', now()->subMinutes(5))
            ->delete();
    }
}
