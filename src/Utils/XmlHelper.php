<?php

namespace PhilWilliammee\SamlServiceProvider\Utils;

use SimpleXMLElement;

class XmlHelper
{
    /**
     * @param SimpleXMLElement[]|false $xml_response
     * @return array
     */
    public static function convertXmlToArray($xml_response): array
    {
        if ($xml_response == false) {
            return [];
        }

        $json = json_encode($xml_response);
        $data = json_decode($json, true) ?? [];
        return $data;
    }

    public static function createXmlElementAndAttachToParent(array $data, SimpleXMLElement $xml_parent)
    {
        if (!isset($data['name']) || !isset($data['value']) || !isset($data['namespace'])) {
            return;
        }

        $xml_node = $xml_parent->addChild($data['name'], $data['value'], $data['namespace']);

        if (isset($data['attributes'])) {
            foreach ($data['attributes'] as $attribute_name => $attribute_value) {
                $xml_node->addAttribute($attribute_name, $attribute_value);
            }
        }

        if (!isset($data['nodes']) || !is_array($data['nodes'])) {
            return;
        }

        foreach ($data['nodes'] as $data_node) {
            self::createXmlElementAndAttachToParent($data_node, $xml_node);
        }
    }

    public static function removeXmlHeader(string $xml_string): string
    {
        // $msg_str = $xml_root->asXML();
        // $t_xml = new DOMDocument();
        // $t_xml->loadXML($msg_str);
        // $msg_str = $t_xml->saveXML($t_xml->documentElement);
        $xml_string = preg_replace('/<\?xml.*\?>/', '', $xml_string);
        // remove new lines
        $xml_string = preg_replace('/\n/', '', $xml_string);
        return $xml_string;
    }
}
