<?php
Namespace PagarMe;
class PagarMe_Request extends PagarMe 
{
	private $path;
	private $method;
	private $parameters = Array();
	private $headers;
	private $live;

	public function __construct($path, $method, $live = PagarMe::live) 
	{
		$this->method = $method;
		$this->path = $path;
		$this->live = $live;	
	}
	public function run() 
	{
		try {
			if(!parent::getApiKey()) {
				throw new Exception("You need to configure API key before performing requests.");
			}

			$this->parameters = array_merge($this->parameters, array( "api_key" => parent::getApiKey()));
			// var_dump($this->parameters);
			// $this->headers = (PagarMe::live) ? array("X-Live" => 1) : array();
			try {
				$client = new RestClient(array("method" => $this->method, "url" => $this->full_api_url($this->path), "headers" => $this->headers, "parameters" => $this->parameters ));	
				$response = $client->run();
				// var_dump($response);
				$decode = json_decode($response["body"], true);
				if(!$decode) {
					throw new Exception("Failed to decode json from response.\n\n Response: ".$response);
				} else {
					if($response["code"] == 200) {
						return $decode;

					} else {
						throw PagarMe_Exception::buildWithFullMessage($decode);
					}
				}
			} catch(RestClient_Exception $e) {
				throw new PagarMe_Exception($e->getMessage());

			}		
		} catch(Exception $e) {
			throw new PagarMe_Exception($e->getMessage());
		}
	}


	public function setParameters($parameters)
   	{
		$this->parameters = $parameters;
	}

	public function getParameters() 
	{
		return $this->parameters;
	}
}
?>
