<?php
Namespace PagarMe;
class PagarMe_TransactionCommon extends PagarMe_Model 
{

	public function __construct($response = array())  {
		parent::__construct($response);			
		if(!isset($this->payment_method)) {
			$this->payment_method = 'credit_card';
		}

		if(!isset($this->status)) {
			$this->status = 'local';
		}
	} 

	public function generateCardHash() 
	{
		$request = new PagarMe_Request('/transactions/card_hash_key','GET');
		$response = $request->run();
		$key = openssl_get_publickey($response['public_key']);
		$params = $this->cardDataParameters();
		$str = "";
		foreach($params as $k => $v) {
			$str .= $k . "=" . $v . "&";	
		}
		$str = substr($str, 0, -1);
		openssl_public_encrypt($str,$encrypt, $key);
		return $response['id'].'_'.base64_encode($encrypt);
	}

	public function unsetCreditCardData() {
		unset($this->card_holder_name);
		unset($this->card_number);
		unset($this->card_expiration_month);
		unset($this->card_expiration_year);
		unset($this->card_cvv);
	}

	public function create() {
		try {
			if(!$this->card_hash && $this->payment_method == 'credit_card') {
				$validation_error= $this->errorInTransaction();
				$this->card_hash = $this->generateCardHash();
				if($validation_error) {
					throw PagarMe_Exception::buildWithError($validation_error);
				}
			} 
		
			if($this->card_hash) {
				$this->unsetCreditCardData();
			}
			parent::create();
		}
		catch(PagarMe_Exception $e) {
			throw $e;
		}
	}

	protected function validateCreditCard($number) {
		// Strip any non-digits (useful for credit card numbers with spaces and hyphens)
		$number=preg_replace('/\D/', '', $number);

		// Set the string length and parity
		$number_length=strlen($number);
		$parity=$number_length % 2;

		// Loop through each digit and do the maths
		$total=0;
		for ($i=0; $i<$number_length; $i++) {
			$digit=$number[$i];
			// Multiply alternate digits by two
			if ($i % 2 == $parity) {
				$digit*=2;
				// If the sum is two digits, add them together (in effect)
				if ($digit > 9) {
					$digit-=9;
				}
			}
			// Total up the digits
			$total+=$digit;
		}

		// If the total mod 10 equals 0, the number is valid
		return ($total % 10 == 0) ? true : false;
	}


	protected function cardDataParameters() 
	{
		return array(
			"card_number" => $this->card_number,
			"card_holder_name" => $this->card_holder_name,
			"card_expiration_date" => $this->card_expiration_month . $this->card_expiration_year,
			"card_cvv" => $this->card_cvv
		);
	}

	//TODO Validate address and phone info
	protected function errorInTransaction() 
	{
		if($this->status != 'local') {
			throw new PagarMe_Exception(null,array('message' => "Transação já realizada", 'parameter_name' => 'status', 'type' => 'forbidden_action'));
		}

		if($this->payment_method == 'credit_card') { 
			if(strlen($this->card_number) > 20 || !$this->validateCreditCard($this->card_number)) {
				return new PagarMe_Exception(null, array('message' => "Número de cartão inválido.", 'parameter_name' => 'card_number', 'type' => "invalid_parameter"));
			}

			else if(strlen($this->card_holder_name) == 0) {
				return new PagarMe_Exception(null, array('message' => " Nome do portador do cartão inválido", 'parameter_name' => 'card_holder_name', 'type' => "invalid_parameter"));
			}

			else if($this->card_expiration_month <= 0 || $this->card_expiration_month > 12) {
				return new PagarMe_Exception(null, array('message' => "Mês de expiração do cartão inválido", 'parameter_name' => 'card_expiration_date', 'type' => "invalid_parameter"));
			}

			else if($this->card_expiration_year <= 0) {
				return new PagarMe_Exception(null, array('message' => "Ano de expiração do cartão inválido", 'parameter_name' => 'card_expiration_date', 'type' => "invalid_parameter"));
			}

			else if($this->card_expiration_year < substr(date('Y'),-2)) {
				return new PagarMe_Exception(null, array('message' => "Cartão expirado", 'parameter_name' => 'card_expiration_date', 'type' => "invalid_parameter"));
			}

			else if(strlen($this->card_cvv) < 3  || strlen($this->card_cvv) > 4) {
				return new PagarMe_Exception(null, array('message' => "Código de segurança inválido", 'parameter_name' => 'card_cvv', 'type' => "invalid_parameter"));
			}

			else {
				return null;
			}
		}
		if($this->amount <= 0) {
			return new PagarMe_Exception(null,array('message' => "Valor inválido", 'parameter_name' => 'amount', 'type' => "invalid_parameter"));
		}

		return null;
	}
} 

?>
