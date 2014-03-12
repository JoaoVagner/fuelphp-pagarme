<?php
Namespace PagarMe;
class PagarMe_Transaction extends PagarMe_TransactionCommon {

	public function charge() 
	{
		$this->create();
	}

	public function refund() 
	{
		try {
			if($this->status == 'refunded') {
				throw new PagarMe_Exception(null, array('message' => "Transaction already refunded!", 'type' => 'forbidden_action'));
			}

			if($this->status != 'paid') {
				throw new PagarMe_Exception(null, array('message' => "Transaction needs to be paid to be refunded", 'type' => 'forbidden_action'));
			}

			if($this->payment_method != 'credit_card') {
				throw new PagarMe_Exception(null, array('message' => "Boletos can't be refunded", 'type' => 'forbidden_action'));
			}

			$request = new PagarMe_Request(self::getUrl().'/'.$this->id . '/refund', 'POST');
			$response = $request->run();
			$this->refresh($response);
		} catch(Exception $e) {
			throw new PagarMe_Exception($e->getMessage());
		}

	}
}

?>
