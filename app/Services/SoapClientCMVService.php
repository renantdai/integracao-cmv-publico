<?php

namespace App\Services;
use Illuminate\Support\Facades\Log;

class SoapClientCMVService {

    private $xmlPostString;
    private $wsdl;
    private $soapAction;

    public function __construct(string $wsdl, string $soapAction) {
        $this->wsdl = $wsdl;
        $this->soapAction = $soapAction;
    }

    public function getXmlPostString() {
    }

    public function setXmlPostString(string $xml): void {
        $this->xmlPostString = $xml;
    }

    private function getHeader() {
        $header = array(
            "Content-type: application/soap+xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction:" . $this->soapAction,
            "Content-length: " . strlen($this->xmlPostString),
        );

        return $header;
    }


    public function sendCurl() {
        $url = $this->wsdl;
        $xml_post_string = $this->xmlPostString;
        $header = $this->getHeader();
        $caFile = getcwd() . "/certificado/imbe.pem"; // Windows \\   |  linux /
        #$CA = getcwd() . "\\certificado\CACert.cer";
        #$key = getcwd() . "\\certificado\key.key";

        // PHP cURL  for https connection with auth
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_USERPWD, $soapUser . ":" . $soapPassword); // username and password - declared at the top of the doc
        //curl_setopt($ch, CURLOPT_SSLCERTTYPE, $caFile);
        curl_setopt($ch, CURLOPT_SSLCERT, $caFile);
        //curl_setopt($ch, CURLOPT_SSLKEY, $key);
        //curl_setopt($ch, CURLOPT_CAINFO,  $caFile);
        //curl_setopt($ch, CURLOPT_SSLCERTPASSWD, 'password');
        //curl_setopt($ch, CURLOPT_KEYPASSWD, 'password');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 180);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        //curl_setopt($ch, CURLOPT_CAINFO, $CA);

        $streamVerboseHandle = fopen('php://temp', 'w+'); ## para logar
        curl_setopt($ch, CURLOPT_STDERR, $streamVerboseHandle); ## para logar

        // converting
        $response = curl_exec($ch);

        /*         if ($response !== FALSE) {
            printf(
                "cUrl error (#%d): %s<br>\n",
                curl_errno($ch),
                htmlspecialchars(curl_error($ch))
            );
        } */

        rewind($streamVerboseHandle);
        $verboseLog = stream_get_contents($streamVerboseHandle);

        #return  "cUrl verbose information:\n" . "<pre>" . htmlspecialchars($verboseLog) . "</pre>\n";

        ## para logar

        curl_close($ch);

        // converting
        $response1 = str_replace("<soap:Body>", "", $response);
        $response2 = str_replace("</soap:Body>", "", $response1);

        // convertingc to XML
        $parser = simplexml_load_string($response2);

        if ($parser == false) {
            Log::info('Erro SOAP', ['curl' =>  htmlspecialchars($verboseLog)]);
        }

        return $parser;
    }
}
