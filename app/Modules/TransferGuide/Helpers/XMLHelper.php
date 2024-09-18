<?php

class XMLHelper {
    private $data;
    private $namespace;
    private $xml;

    public function __construct($data, $namespace) {
        $this->data = $data;
        $this->namespace = $namespace;
        $this->xml = new DOMDocument('1.0', 'UTF-8');
    }

    public function generateXML() {
        $root = $this->xml->createElement('soapenv:Envelope');
        $root->setAttribute('xmlns:soapenv', 'http://schemas.xmlsoap.org/soap/envelope/');
        $root->setAttribute('xmlns:gui', $this->namespace);

        $header = $this->xml->createElement('soapenv:Header');
        $root->appendChild($header);

        $body = $this->xml->createElement('soapenv:Body');
        // $action = $this->xml->createElement('gui:'.$this->action);
        // $body->appendChild($action);
        $root->appendChild($body);


        $this->xml->appendChild($root);

        $this->createNodes($this->data, $body);

        $this->xml->formatOutput = true;
        return $this->xml->saveXML();
    }

    private function createNodes($data, $parent) {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if(is_numeric($key)){
                    $element = $this->xml->createElement('gui:'.array_keys($data[$key])[0], $value[0][array_keys($data[$key])[0]]);
                    $parent->appendChild($element);
                    $this->createNodes($value[array_keys($data[$key])[0]], $element);
                }
                else{
                    $element = $this->xml->createElement('gui:'.$key);
                    $parent->appendChild($element);
                    $this->createNodes($value, $element);
                }
            } else {
                $element = $this->xml->createElement('gui:'.$key, $value);
                $parent->appendChild($element);
            }
        }
    }
}