<?php
Namespace PagarMe;
class PagarMe_Plan extends PagarMe_Model 
{
	protected function validate() 
	{
		if($this->amount <= 0) {
			throw new PagarMe_Exception(null, array('message' => "Amount invalido!", 'parameter_name' => 'amount', 'type' => 'invalid_parameter'));;
		} else if($this->days <= 0) {
			throw new PagarMe_Exception(null, array('message' => "Days inválido!", 'parameter_name' => 'days', 'type' => 'invalid_parameter'));
		} else if(strlen($this->name) <= 0) {
			throw new PagarMe_Exception(null,array( 'message' => "Name inválido!", 'parameter_name' => 'name', 'type' => 'invalid_parameter'));
		} else if($this->trial_days < 0) {
			throw new PagarMe_Exception(null, array('message' => "Trial days invalido!", 'parameter_name' => 'trial_days', 'type' => 'invalid_parameter'));
		} else {
			return true;
		}
	}
}


?>
