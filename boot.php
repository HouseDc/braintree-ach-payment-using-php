<?php
//auto loading the package in project
require "vendor/autoload.php";

// setting up the enviroment and creadentails 

// $gateway = new Braintree\Gateway([
//     'environment' => 'sandbox',
//     'merchantId' => '8rzhcjp2nbqxn4nn',
//     'publicKey' => 'wpf7jykd4dgh85cm',
//     'privateKey' => '304da6b461743b301a2ad911cb74896c'
// ]);

$gateway = new Braintree\Gateway([
  'environment' => 'sandbox',
  'merchantId' => 'ffyww4wscdzf8c8p',
  'publicKey' => '497nnp29z3ffc8y5',
  'privateKey' => 'a4cb0cc4d17a81f8a31c507c84a193ea'
]);
