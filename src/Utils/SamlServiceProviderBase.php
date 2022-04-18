<?php

namespace PhilWilliammee\SamlServiceProvider\Utils;

use SimpleXMLElement;
use PhilWilliammee\SamlServiceProvider\Utils\XmlHelper;

class SamlServiceProviderBase
{
    /**
     * @var string
     */
    private string $id;

    /**
     * The date time the request is made
     *
     * @var string
     */
    private string $issue_instant;

    /**
     * The url the post login request will be sent to
     *
     * @var string
     */
    public string $destination;

    /**
     * The url on this website that returns xml metadata
     *
     * @var string
     */
    private string $issuer;

    /**
     * The url on this website that returns xml metadata
     *
     * @var string
     */
    private string $public_key;

    private string $mailto;

    /**
     * The url on this website that saml will post data to
     */
    private string $assertion_consumer_service_url;

    public function __construct($config = [])
    {
        $this->id = bin2hex(random_bytes(32));
        $this->issue_instant = gmdate('Y-m-d\TH:i:s\Z', time());
        $this->destination = $config['destination'];
        $this->issuer = $config['issuer'];
        $this->assertion_consumer_service_url = $config['assertion_consumer_service_url'];
        $this->public_key = $config['public_key'] ?? "";
        $this->mailto = $config['mailto'];
    }

    public function getAuthenticationRequest(): string
    {
        $root = <<<XML
            <samlp:AuthnRequest
                xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
                xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
            />
        XML;
        $xml_root = new SimpleXMLElement($root);
        $xml_root->addAttribute('ID', $this->id);
        $xml_root->addAttribute('Version', '2.0');
        $xml_root->addAttribute('IssueInstant', $this->issue_instant);
        $xml_root->addAttribute('Destination', $this->destination);
        $xml_root->addAttribute('AssertionConsumerServiceURL', $this->assertion_consumer_service_url);
        $xml_root->addAttribute('ProtocolBinding', 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST');
        $xml_root->addChild('saml:Issuer', $this->issuer, 'urn:oasis:names:tc:SAML:2.0:assertion');
        $xml_name_policy = $xml_root->addChild('samlp:NameIDPolicy', null, 'urn:oasis:names:tc:SAML:2.0:protocol');
        $xml_name_policy->addAttribute('Format', 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient');
        $xml_name_policy->addAttribute('AllowCreate', 'true');

        $msg_str = XmlHelper::removeXmlHeader($xml_root->asXML());
        $msg_str = base64_encode($msg_str);
        return $msg_str;
    }

    public function getMetadata(): string
    {
        $root = <<<XML
            <md:EntityDescriptor
                xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata"
                xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
            />
        XML;

        $xml_root = new SimpleXMLElement($root);
        $xml_root->addAttribute('entityID', $this->issuer);

        $descriptor = [
            'name' => 'md:SPSSODescriptor',
            'value' => '',
            'namespace' => 'urn:oasis:names:tc:SAML:2.0:metadata',
            'attributes' => [
                'protocolSupportEnumeration' => 'urn:oasis:names:tc:SAML:2.0:protocol urn:oasis:names:tc:SAML:1.1:protocol'
            ],
            "nodes" =>
            [
                [
                    'name' => 'md:KeyDescriptor',
                    'value' => '',
                    'namespace' => 'urn:oasis:names:tc:SAML:2.0:metadata',
                    'attributes' => [
                        'use' => 'signing'
                    ],
                    "nodes" =>
                    [
                        [
                            'name' => 'ds:KeyInfo',
                            'value' => '',
                            'namespace' => 'http://www.w3.org/2000/09/xmldsig#',
                            "nodes" =>
                            [
                                [
                                    'name' => 'ds:X509Data',
                                    'value' => '',
                                    'namespace' => 'http://www.w3.org/2000/09/xmldsig#',
                                    "nodes" =>
                                    [
                                        [
                                            'name' => 'ds:X509Certificate',
                                            'value' => $this->public_key,
                                            'namespace' => 'http://www.w3.org/2000/09/xmldsig#',

                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'md:KeyDescriptor',
                    'value' => '',
                    'namespace' => 'urn:oasis:names:tc:SAML:2.0:metadata',
                    'attributes' => [
                        'use' => 'encryption'
                    ],
                    "nodes" =>
                    [
                        [
                            'name' => 'ds:KeyInfo',
                            'value' => '',
                            'namespace' => 'http://www.w3.org/2000/09/xmldsig#',
                            "nodes" =>
                            [
                                [
                                    'name' => 'ds:X509Data',
                                    'value' => '',
                                    'namespace' => 'http://www.w3.org/2000/09/xmldsig#',
                                    "nodes" =>
                                    [
                                        [
                                            'name' => 'ds:X509Certificate',
                                            'value' => $this->public_key,
                                            'namespace' => 'http://www.w3.org/2000/09/xmldsig#',
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'md:AssertionConsumerService',
                    'value' => '',
                    'namespace' => 'urn:oasis:names:tc:SAML:2.0:metadata',
                    'attributes' => [
                        'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
                        'Location' => $this->assertion_consumer_service_url,
                        'index' => '0'
                    ]
                ]
            ]
        ];

        $contact_person = [
            'name' => 'md:ContactPerson',
            'value' => '',
            'namespace' => 'urn:oasis:names:tc:SAML:2.0:metadata',
            'attributes' => [
                'contactType' => 'technical'
            ],
            "nodes" =>
            [
                [
                    'name' => 'md:GivenName',
                    'value' => 'Administrator',
                    'namespace' => 'urn:oasis:names:tc:SAML:2.0:metadata',
                ],
                [
                    'name' => 'md:EmailAddress',
                    'value' => $this->mailto,
                    'namespace' => 'urn:oasis:names:tc:SAML:2.0:metadata',
                ]
            ]
        ];

        XmlHelper::createXmlElementAndAttachToParent($descriptor, $xml_root);
        XmlHelper::createXmlElementAndAttachToParent($contact_person, $xml_root);
        $xml_string = $xml_root->asXML();
        return $xml_string;
    }

    public static function decodeSamlResponse(string $encoded_saml_response): string
    {
        return base64_decode($encoded_saml_response);
    }

    public static function getDataFromXmlResponse(string $xml_string): array
    {
        $xml = simplexml_load_string($xml_string);
        if (!$xml) {
            return [];
        }
        $xml->registerXPathNamespace('saml2', "urn:oasis:names:tc:SAML:2.0:assertion");
        $attributes = $xml->xpath('//saml2:Attribute');

        foreach ($attributes as $attribute) {
            $name = (string) $attribute['FriendlyName'];
            $values = [];
            foreach ($attribute->xpath('saml2:AttributeValue') as $attributeValues) {
                $values[] = (string) $attributeValues;
            }
            $data[$name] = $values;
        }
        return $data;
    }

    public static function getKeyFromSamlResponse(string $xml_string)
    {
        $xml = simplexml_load_string($xml_string);
        if (!$xml) {
            return "";
        }
        $xml->registerXPathNamespace('saml2', "urn:oasis:names:tc:SAML:2.0:assertion");
        $subject_conformation_data = $xml->xpath('//saml2:SubjectConfirmationData');
        $subject_conformation_attributes = $subject_conformation_data[0]->attributes();
        return (string) $subject_conformation_attributes['InResponseTo'];
    }

    public static function getNotOnOrAfterFromSamlResponse(string $xml_string)
    {
        $xml = simplexml_load_string($xml_string);
        if (!$xml) {
            return "";
        }
        $xml->registerXPathNamespace('saml2', "urn:oasis:names:tc:SAML:2.0:assertion");
        $conditions = $xml->xpath('//saml2:Conditions');
        $conditions_attributes = $conditions[0]->attributes();
        return (string) $conditions_attributes['NotOnOrAfter'];
    }

    public function getId()
    {
        return $this->id;
    }
}
