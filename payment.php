<?php
require "boot.php";

if (empty($_POST['tokenizedPayload'])) {
    header('location: index.php');
} else {
    // BraintreeResultUsBankAccountVerification

    $nonceFromTheClient = $_POST["tokenizedPayload"];
    $amount = $_POST["amount"];
    $deviceDataFromTheClient  = $_POST["deviceData"];

    $result = $gateway->paymentMethod()->create([
        'paymentMethodNonce' => $nonceFromTheClient,
        'options' => [
            'usBankAccountVerificationMethod' => \Braintree\Result\UsBankAccountVerification::NETWORK_CHECK
        ]
    ]);


    if ($result->success) {
        $usBankAccount = $result->paymentMethod;
        $verified = $usBankAccount->verified;
        $responseCode = $usBankAccount->verifications[0]->processorResponseCode;  //1000 for approved

        if ($verified) {
            $resultTrans = $gateway->transaction()->sale([
                'amount' => $amount,
                'paymentMethodToken' => $usBankAccount,
                'deviceData' => $deviceDataFromTheClient,
                'options' => [
                    'submitForSettlement' => True
                ]
            ]);

            if ($resultTrans->success || !is_null($resultTrans->transaction)) {
                $trasaction_id  = $resultTrans->transaction->id;
            } else {
                $errorString = '';
                foreach ($resultTrans->errors->deepAll() as $key => $error) {
                    $errorString = 'Error ' . $error->code . ' : ' . $error->message . '\n';
                }
                echo '<pre>';
                print_r($errorString);
                die;
            }
        } else {
            echo '<div style="width:100vw;height:100vh;display:flex;justify-content:center;align-items:center">
                <span style="color:red;display:block"> BANK ACCOUNT VERIFICATION FAILED </span>
            </div>';
            die;
        }
    } else {
        echo '<div style="width:100vw;height:100vh;display:flex;justify-content:center;align-items:center">
            <span style="color:red;display:block"> PAYMENT METHOD CREATING FAILED</span>
        </div>';
        die;
    }


    // prevously used for card payment 
    // $result = $gateway->transaction()->sale([
    //     'amount' => $amount,
    //     'paymentMethodNonce' => $nonceFromTheClient,
    //     'deviceData' => $deviceDataFromTheClient,
    //     'options' => [
    //         'submitForSettlement' => True
    //     ]
    // ]);

    // if ($result->success || !is_null($result->transaction)) {
    //     $trasaction_id  = $result->transaction->id;
    // } else {
    //     $errorString = '';
    //     foreach ($result->errors->deepAll() as $key => $error) {
    //         $errorString = 'Error ' . $error->code . ' : ' . $error->message . '\n';
    //     }
    //     echo '<pre>';
    //     print_r($errorString);
    //     die;
    // }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Payment Result</title>
</head>

<body>
    <div class="d-flex justify-content-center align-items-center" style="width:100vw;height:100vh">
        <div style="width:400px">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th colspan="2" class="bg-light text-center">Transaction Result</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        <tr class="">
                            <td scope="row">Transaction ID</td>
                            <td><?php echo $trasaction_id ?></td>
                        </tr>
                        <tr class="">
                            <td scope="row">First Name</td>
                            <td><?php echo $result->transaction->customer['firstName'] ?></td>
                        </tr>
                        <tr class="">
                            <td scope="row">Last Name</td>
                            <td><?php echo $result->transaction->customer['lastName'] ?></td>
                        </tr>
                        <tr class="">
                            <td scope="row">Amount</td>
                            <td><?php echo $result->transaction->amount . ' ' . $result->transaction->currencyIsoCode ?></td>
                        </tr>
                        <tr class="">
                            <td colspan="2" class="bg-success text-white text-center">
                                Successfull
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</body>

</html>