<?php
require "boot.php";

// pass $clientToken to your front-end
$clientToken = $gateway->clientToken()->generate();
echo json_encode($clientToken);