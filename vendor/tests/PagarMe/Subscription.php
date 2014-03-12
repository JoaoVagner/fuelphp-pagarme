<?php

class PagarMe_SubscriptionTest extends PagarMeTestCase {

	public function testCreate() {
		$subscription = self::createTestSubscription();	
		$subscription->create();
		$this->validateSubscription($subscription);
	}

	public function testUpdate() {
		$subscription = self::createTestSubscription();
		$subscription->create();

		$subscription->setPaymentMethod('boleto');
		$subscription->save();

		$subscription2 = PagarMe_Subscription::findById($subscription->getId());
		$this->assertEqual($subscription2->getPaymentMethod(), 'boleto');
	}

	public function testCreateWithFraud() {
		$subscription = self::createSubscriptionWithCustomer();
		$subscription->create();
		$this->validateSubscription($subscription);
	}

	public function testCreateWithPlanAndFraud() {
		$subscription = self::createSubscriptionWithCustomer();
		$plan = self::createTestPlan();
		$plan->create();
		$subscription->setPlan($plan);

		$subscription->create();
		$this->validateSubscription($subscription);
		$this->assertTrue($subscription->getId());
		$this->assertTrue($subscription->getCustomer());
		$this->assertTrue($subscription->getPlan()->getId());
		$this->assertTrue($plan->getId());

		$subscription2 = PagarMe_Subscription::findById($subscription->getId());
		$this->assertTrue($subscription2->getPlan());
		$this->assertEqual($subscription2->getPlan()->getId(), $plan->getId());
	}

	public function testCreateWithPlan() {
		$plan = self::createTestPlan();
		$subscription = self::createTestSubscription();
		$plan->create();

		$subscription->setPlan($plan);
		$subscription->create();

		$this->validateSubscription($subscription);
		$this->assertTrue($subscription->getPlan()->getId());
		$this->assertTrue($plan->getId());

		$subscription2 = PagarMe_Subscription::findById($subscription->getId());
		$this->assertTrue($subscription2->getPlan());
		$this->assertEqual($subscription2->getPlan()->getId(), $plan->getId());
	}

	public function testCancel() {
		$subscription = self::createTestSubscription();
		$subscription->create();

		$subscription->cancel();
		$this->assertEqual($subscription->status, 'canceled');
	}

	public function testCharge() {
		$subscription = self::createTestSubscription();
		$subscription->create();
		$subscription->charge(3600);
		$transaction = $subscription->getCurrentTransaction();
		$this->assertEqual($transaction->getAmount(), '3600');
	}
}

?>
