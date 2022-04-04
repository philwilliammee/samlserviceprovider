<?php
/**
 * This file is part of the SamlServiceProvider package.
 *
 */
return [
    // The destination URL of the SAML Identity Provider SSO.
    'destination' => "",
    // The issuer of the SAML Service Provider.
    'issuer' => config("app.url") . "/saml-metadata",
    // The assertion consumer service URL of the SAML Service Provider.
    'assertion_consumer_service_url' => config("app.url") . "/saml-acs",
    // The certificate of the SAML Service Provider.
    'public_key' => "",
    "mailto" => "",
];
