<?php

/**
 * Fuel
 *
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.7
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2013 Fuel Development Team
 * @link       http://fuelphp.com
 */
Autoloader::add_core_namespace('PagarMe');

Autoloader::add_classes(array(
    'PagarMe\\PagarMe'              => __DIR__ . '/vendor/lib/Pagarme/PagarMe.php',
    'PagarMe\\PagarMe_Address'      => __DIR__ . '/vendor/lib/Pagarme/Address.php',
    'PagarMe\\PagarMe_Customer'     => __DIR__ . '/vendor/lib/Pagarme/Customer.php',
    'PagarMe\\PagarMe_Error'        => __DIR__ . '/vendor/lib/Pagarme/Error.php',
    'PagarMe\\PagarMe_Exception'    => __DIR__ . '/vendor/lib/Pagarme/Exception.php',
    'PagarMe\\PagarMe_Model'        => __DIR__ . '/vendor/lib/Pagarme/Model.php',
    'PagarMe\\PagarMe_Object'       => __DIR__ . '/vendor/lib/Pagarme/Object.php',
    'PagarMe\\PagarMe_Phone'        => __DIR__ . '/vendor/lib/Pagarme/Phone.php',
    'PagarMe\\PagarMe_Plan'         => __DIR__ . '/vendor/lib/Pagarme/Plan.php',
    'PagarMe\\PagarMe_Request'      => __DIR__ . '/vendor/lib/Pagarme/Request.php',
    'PagarMe\\RestClient'           => __DIR__ . '/vendor/lib/Pagarme/RestClient.php',
    'PagarMe\\PagarMe_Set'          => __DIR__ . '/vendor/lib/Pagarme/Set.php',
    'PagarMe\\PagarMe_Subscription' => __DIR__ . '/vendor/lib/Pagarme/Subscription.php',
    'PagarMe\\PagarMe_Transaction'  => __DIR__ . '/vendor/lib/Pagarme/Transaction.php',
    'PagarMe\\PagarMe_TransactionCommon' => __DIR__ . '/vendor/lib/Pagarme/TransactionCommon.php',
    'PagarMe\\PagarMe_Util'         => __DIR__ . '/vendor/lib/Pagarme/Util.php'
));


/* End of file bootstrap.php */
