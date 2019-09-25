<?php

namespace App\Utils\Cybersource;

use stdClass;

/**
 * CybsSoapClient
 *
 * An implementation of PHP's SOAPClient class for making CyberSource requests.
 */
class CybsSoapClient extends CybsClient
{

    function __construct($options=array())
    {
        //$properties = parse_ini_file('cybs.ini');
        $properties = [
          "merchant_id" => "tc_cr_011007172",
          "transaction_key" => "0QGJoZxR+mSbUFwdsNSi5jk+8jhXSW3cxk5aUu1nt7eYhckgji+BDr0JTMmYQK6FmBOvsf5bScbtvxD3HIWiLxg3F7p6mhzN7cUIXeiL7t2u6L280DrhioY1U6f00Po+IJQiLmvOA9LXrV+ozf+Xo4NJatXo5mCZV+3AeU6TTR2w4zNwabGTKA4LwRPEtMSEL+puNfiHb0u9J0KkLxdZ42yHxDje+A4X89PkMTncsI3Yuf/jOZsYkTJnpNYWE+IevgAhMKs6qJeXni63RVhiIrGsZaLrFeTBxUwjXWOJtdG8fKIZkDvFoUaU9S+H3xPff8/ZWe2zOJVaSvDgc6/lJA==",
          "wsdl" => "https://ics2wstest.ic3.com/commerce/1.x/transactionProcessor/CyberSourceTransaction_1.120.wsdl",
          "nvp_wsdl" => "https://ics2wstest.ic3.com/commerce/1.x/transactionProcessor/CyberSourceTransaction_NVP_1.120.wsdl"
        ];
        
        parent::__construct($options, $properties);
    }

    /**
     * Returns a properly formatted request object from a SimpleXMLElement. 
     *
     * @param SimpleXMLElement $simpleXml Representation of an XML structure
     * @return stdClass A request with the data from the SimpleXMLElement.
     */
    public function simpleXmlToCybsRequest($simpleXml)
    {
        $vars = get_object_vars($simpleXml);
        $request = new stdClass();

        foreach(array_keys($vars) as $key) {
            $element = $vars[$key];
            if ($key == 'comment') {
                continue;
            }
            if (is_string($element)) {
                $request->$key = $element;
            } else if (is_array($element)) {
                $array = $element;
                if ($key == "@attributes") {
                    // Each attribute in the '@attributes' array should
                    // instead be a property of the parent element.
                    foreach($array as $k => $value) {
                        $request->$k = $value;
                    }
                } else {
                    $newArray = array();
                    foreach($array as $k => $value) {
                        $newArray[$k] = $this->simpleXmlToCybsRequest($value);
                    }
                    $request->$key = $newArray; 
                }
            } else if ($element instanceof SimpleXMLElement) {
                $request->$key = $this->simpleXmlToCybsRequest($element);
            }
        }
        return $request;
    }

    /**
     * Returns an object initialized with basic client information.
     *
     * @param string $merchantReferenceCode Desired reference code for the request
     * @return stdClass An object initialized with the basic client info.
     */
    public function createRequest($merchantReferenceCode)
    {
        $request = new stdClass();
        $request->merchantID = $this->getMerchantId();
        $request->merchantReferenceCode = $merchantReferenceCode;
        $request->clientLibrary = self::CLIENT_LIBRARY_VERSION;
        $request->clientLibraryVersion = phpversion();
        $request->clientEnvironment = php_uname();
        return $request;
    }

    /**
     * Runs a transaction from an XML string
     *
     * @param string $filePath The path to the XML file
     * @param string $merchantReferenceCode Desired reference code for the request
     * @return stdClass An object representation of the transaction response.     
     */
    public function runTransactionFromXml($xml, $merchantReferenceCode)
    {
        $request = $this->createRequest($merchantReferenceCode);
        $simpleXml = simplexml_load_string($xml);
        $xmlRequest = $this->simpleXmlToCybsRequest($simpleXml);
        $mergedRequest = (object) array_merge((array) $request, (array) $xmlRequest);
        return $this->runTransaction($mergedRequest);
    }

    /**
     * Runs a transaction from an XML file.
     *
     * @param string $filePath The path to the XML file
     * @param string $merchantReferenceCode Desired reference code for the request
     * @return stdClass An object representation of the transaction response.     
     */
    public function runTransactionFromFile($filePath, $merchantReferenceCode)
    {
        $request = $this->createRequest($merchantReferenceCode);
        $xml = file_get_contents($filePath);
        return $this->runTransactionFromXml($xml, $merchantReferenceCode);
    }
}
