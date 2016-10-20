<?php
// Fix bug #49853 - https://bugs.php.net/bug.php?id=49853
namespace TodoPago;

class Client extends \SoapClient
{
	protected $custom_headers;
	
	public function __doRequest($request, $location, $action, $version, $one_way = NULL)
    {
        $soap_request = $request;
		
		$context = $this->custom_headers;
		$custom_headers = array_filter(explode("\r\n",$context['http']['header']));

        $headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Cache-Control: no-cache",
            "Pragma: no-cache",    
            "SOAPAction: \"$action\"",
            "Content-length: ".strlen($soap_request),
        );

		
        $soap_do = curl_init();

        $url = $location;

        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,

            CURLOPT_USERAGENT      => 'PHPSOAP/'.PHP_VERSION,
            CURLOPT_URL            => $url ,

            CURLOPT_POSTFIELDS => $soap_request ,
            CURLOPT_HTTPHEADER => array_merge($headers,$custom_headers) ,
        );

        curl_setopt_array($soap_do , $options);

        $output = curl_exec($soap_do);
        if( $output === false)
        {
			$err = 'Curl error: ' . curl_error($soap_do);
			throw new \Exception($err);
        }

        curl_close($soap_do);
        return $output;
    }

	public function setCustomHeaders($context)
	{
		$this->custom_headers = $context;
	}
}
