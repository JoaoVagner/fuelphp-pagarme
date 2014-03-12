<?php

class PagarMe_TransactionTest extends PagarMeTestCase {

	public function testCharge() {
		$transaction = self::createTestTransaction();
		$this->assertFalse($transaction->getId());
		$transaction->charge();
		$this->validateTransactionResponse($transaction);
	}

	public function testAntifraudTransaction() {
		$t = self::createTestTransactionWithCustomer();
		$t->charge();
		$this->validateTransactionResponse($t);
	}

	public function testPostbackUrl() {
		$t = self::createTestTransaction();	
		$t->setPostbackUrl('http://url.com');
		$t->charge();

		$this->assertEqual($t->getStatus(), 'processing');
	}
	
	public function testPostbackUrlWithCardHash() {
		$t = self::createTestTransactionWithCustomer();
		$card_hash = $t->generateCardHash();

		$t->setPostbackUrl('http://url.com');
		$t->charge();

		$this->validateTransactionResponse($t);

		$this->assertEqual($t->getPostbackUrl(), 'http://url.com');
		$this->assertEqual($t->getStatus(), 'processing');
	}

	public function testChargeWithCardHash() {
		$t = self::createTestTransactionWithCustomer();
		$card_hash = $t->generateCardHash();

		$transaction = self::createTestTransactionWithCustomer();
		$transaction->setCardHash($card_hash);
		$transaction->charge();
		$this->validateTransactionResponse($transaction);
	}

	public function testTransactionWithBoleto() {
		authorizeFromEnv();
		$t1 = self::createTestTransaction();
		$t1->setPaymentMethod('boleto');
		$t1->charge();

		$this->validateTransactionResponse($t1);

		$t2 = self::createTestTransactionWithCustomer();
		$t2->setPaymentMethod('boleto');
		$t2->charge();

		$this->validateTransactionResponse($t2);


		$this->assertEqual($t2->getPaymentMethod(), 'boleto');
		$this->assertEqual($t2->getBoletoUrl(), 'http://www.pagar.me/');
		$this->assertTrue($t2->getBoletoBarcode());
	}

	public function testPostback() {
		$transaction = self::createTestTransaction();
		$transaction->setPostbackUrl('abc2');

		$this->assertEqual('abc2', $transaction->getPostbackUrl());
	}

	public function testRefund() {
		$transaction = self::createTestTransaction();
		$transaction->charge();
		$this->validateTransactionResponse($transaction);
		$transaction->refund();
		$this->assertEqual($transaction->getStatus(), 'refunded');

		$this->expectException(new IsAExpectation('PagarMe_Exception'));
		$transaction->refund();
	}

	public function testCreation() {
		$transaction = self::createTestTransaction();
		$this->assertEqual($transaction->getStatus(), 'local');
		$this->assertEqual($transaction->getPaymentMethod(), 'credit_card');
	} 

	public function testMetadata() {
		$transaction = self::createTestTransaction();
		$transaction->setMetadata(array('event' => array('name' => "Evento irado", 'quando'=> 'amanha')));
		$transaction->charge();
		$this->assertTrue($transaction->getId());

		$transaction2 = PagarMe_Transaction::findById($transaction->getId());
		$metadata = $transaction2->getMetadata();
		$this->assertEqual($metadata['event']['name'], "Evento irado");
	}

	public function testDeepMetadata() {
		$transaction = self::createTestTransaction();
		$transaction->setMetadata(array('basket' => array('session' => array('date' => "31/04/2014", 'time' => "12:00:00"), 'ticketTypeId'=> '5209', 'type' => "inteira", 'quantity' => '1', 'price' => 2000)));
		$transaction->charge();
		$this->assertTrue($transaction->getId());

		$transaction2 = PagarMe_Transaction::findById($transaction->getId());
		$metadata = $transaction2->getMetadata();
		$this->assertEqual($metadata['basket']['quantity'], "1");
		$this->assertEqual($metadata['basket']['session']['date'], "31/04/2014");
	}

	public function testValidation() {
		$transaction = new PagarMe_Transaction();
		$transaction->setCardNumber("123");
		$this->expectException(new IsAExpectation('PagarMe_Exception'));
		$transaction->charge();
		$transaction->setCardNumber('4111111111111111');

		$transaction->setCardHolderName('');
		$this->expectException(new IsAExpectation('PagarMe_Exception'));
		$transaction->charge();
		$transaction->setCardHolderName("Jose da silva");

		$transaction->setExpiracyMonth(13);
		$this->expectException(new IsAExpectation('PagarMe_Exception'));
		$transaction->charge();
		$transaction->setExpiracyMonth(12);

		$transaction->setExpiracyYear(10);
		$this->expectException(new IsAExpectation('PagarMe_Exception'));
		$transaction->charge();
		$transaction->setExpiracyYear(16);

		$transaction->setCvv(123456);
		$this->expectException(new IsAExpectation('PagarMe_Exception'));
		$transaction->charge();
		$transaction->setCvv(123);

		$transaction->setAmount(0);
		$this->expectException(new IsAExpectation('PagarMe_Exception'));
		$transaction->charge();
		$transaction->setAmount(1000);
	}


	public function testFingerprint() {
		$this->assertTrue(PagarMe::validateFingerprint('13', sha1('13' . '#' . PagarMe::getApiKey())));		
	}
}

?>
