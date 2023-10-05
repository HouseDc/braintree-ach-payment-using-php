<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Braintree Payment</title>
    <!-- bootstrap css /\ -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- jquery cdn -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- braintree js  -->
    <script src="https://js.braintreegateway.com/web/dropin/1.40.2/js/dropin.min.js"></script>
    <style>
        input::placeholder {
            color: #c3c3c3 !important;
        }

        .tope {
            height: 100vh;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #0000006b;
            z-index: 999;
        }

        .laodingitem {
            border: 15px solid #eaf0f691;
            border-radius: 50%;
            border-top: 15px solid #13df06;
            width: 100px;
            height: 100px;
            animation: spinner 4s linear infinite;
        }

        @keyframes spinner {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div id="loader" class="tope" style="display: none;">
        <div class="laodingitem"></div>
    </div>
    <div id="errorsite" class="tope" style="display: none;">
        <div class="card" style="width: 40%;">
            <div class="card-header text-uppercase" id="errorType"></div>
            <div class="card-body bg-white d-flex align-items-center">
                <img src="err.png" alt="" style="width: 40px;height:40px">
                <div class="ms-5" id="errorMsg">
                </div>
            </div>
            <div class="card-footer text-end py-1">
                <button type="button" name="" id="" class="bg-primary text-white py-1 px-3 border-0 rounded" onclick="this.parentElement.parentElement.parentElement.style.display='none'">Close</button>
            </div>
        </div>
    </div>
    <div class="container my-5">
        <div class="w-75 m-auto">
            <form id="payment-form" onsubmit="return validateForm()" action="payment.php" method="post">
                <h6 class="text-uppercase mb-3 text-start">Amount detail</h6>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount (USD) <span class="text-danger">*</span></label>
                                    <input type="number" min="0" placeholder="enter your amount" step="any" name="amount" id="amount" class="form-control" aria-describedby="helpId" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <h6 class="text-uppercase my-3 text-start">Bank Account Information</h6>
                <div class="card">
                    <div class="card-header bg-white p-0">
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="w-50 h-100 border-end">
                                <label for="personal-type" class="w-100 text-center p-2 form-check-label">
                                    <input type="radio" onchange="changeOwnershipType('personal')" class="form-check-input" name="ownership-type" required id="personal-type" value="personal">
                                    Personal
                                </label>
                            </div>
                            <div class="w-50 h-100 border-start">
                                <label for="business-type" class="w-100 text-center p-2 form-check-label">
                                    <input type="radio" onchange="changeOwnershipType('business')" name="ownership-type" class="form-check-input" required id="business-type" value="business">
                                    Business
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- persinal accont needs  -->
                            <div class="col-md-12" id="personal-account" style="display: none;"></div>
                            <!-- busines account needs  -->
                            <div class="col-md-6" id="business-account" style="display: none;"></div>
                            <!-- other required fields  -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="account-number" class="form-label">Account Number <span class="text-danger">*</span></label>
                                    <input type="number" min="0" name="account-number" required id="account-number" class="form-control" placeholder="999999999" aria-describedby="helpId">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="routing-number" class="form-label">Routing Number <span class="text-danger">*</span></label>
                                    <input type="number" min="0" name="routing-number" required id="routing-number" class="form-control" placeholder="307075259" aria-describedby="helpId">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="routing-number" class="form-label d-block">Account Type <span class="text-danger">*</span></label>
                                <div class="form-check d-inline-block mx-1">
                                    <input class="form-check-input" type="radio" required value="checking" name="account-type" id="checking">
                                    <label class="form-check-label" for="checking">
                                        Checking
                                    </label>
                                </div>
                                <div class="form-check d-inline-block mx-3">
                                    <input class="form-check-input" type="radio" required value="savings" name="account-type" id="savings">
                                    <label class="form-check-label" for="savings">
                                        Savings
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- billing address  -->
                <h6 class="text-uppercase my-3 text-start"> billing address</h6>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="street-address" class="form-label">Street Address <span class="text-danger">*</span></label>
                                    <input type="text" min="0" name="street-address" required id="street-address" class="form-control" placeholder="123 Fake St" aria-describedby="helpId">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="extended-address" class="form-label">Extended Address</label>
                                    <input type="text" min="0" name="extended-address" id="extended-address" class="form-control" placeholder="Apparment B" aria-describedby="helpId">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="locality" class="form-label">Locality <span class="text-danger">*</span></label>
                                    <input type="text" min="0" name="locality" required id="locality" class="form-control" placeholder="San Francisco B" aria-describedby="helpId">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="region" class="form-label">Region</label>
                                    <select class="form-select " name="region" required id="region">
                                        <option value="">Select state</option>
                                        <option value="AL">Alabama</option>
                                        <option value="AK">Alaska</option>
                                        <option value="AS">American Samoa</option>
                                        <option value="AZ">Arizona</option>
                                        <option value="AR">Arkansas</option>
                                        <option value="UM-81">Baker Island</option>
                                        <option value="CA">California</option>
                                        <option value="CO">Colorado</option>
                                        <option value="CT">Connecticut</option>
                                        <option value="DE">Delaware</option>
                                        <option value="DC">District of Columbia</option>
                                        <option value="FL">Florida</option>
                                        <option value="GA">Georgia</option>
                                        <option value="GU">Guam</option>
                                        <option value="HI">Hawaii</option>
                                        <option value="UM-84">Howland Island</option>
                                        <option value="ID">Idaho</option>
                                        <option value="IL">Illinois</option>
                                        <option value="IN">Indiana</option>
                                        <option value="IA">Iowa</option>
                                        <option value="UM-86">Jarvis Island</option>
                                        <option value="UM-67">Johnston Atoll</option>
                                        <option value="KS">Kansas</option>
                                        <option value="KY">Kentucky</option>
                                        <option value="UM-89">Kingman Reef</option>
                                        <option value="LA">Louisiana</option>
                                        <option value="ME">Maine</option>
                                        <option value="MD">Maryland</option>
                                        <option value="MA">Massachusetts</option>
                                        <option value="MI">Michigan</option>
                                        <option value="UM-71">Midway Atoll</option>
                                        <option value="MN">Minnesota</option>
                                        <option value="MS">Mississippi</option>
                                        <option value="MO">Missouri</option>
                                        <option value="MT">Montana</option>
                                        <option value="UM-76">Navassa Island</option>
                                        <option value="NE">Nebraska</option>
                                        <option value="NV">Nevada</option>
                                        <option value="NH">New Hampshire</option>
                                        <option value="NJ">New Jersey</option>
                                        <option value="NM">New Mexico</option>
                                        <option value="NY">New York</option>
                                        <option value="NC">North Carolina</option>
                                        <option value="ND">North Dakota</option>
                                        <option value="MP">Northern Mariana Islands</option>
                                        <option value="OH">Ohio</option>
                                        <option value="OK">Oklahoma</option>
                                        <option value="OR">Oregon</option>
                                        <option value="UM-95">Palmyra Atoll</option>
                                        <option value="PA">Pennsylvania</option>
                                        <option value="PR">Puerto Rico</option>
                                        <option value="RI">Rhode Island</option>
                                        <option value="SC">South Carolina</option>
                                        <option value="SD">South Dakota</option>
                                        <option value="TN">Tennessee</option>
                                        <option value="TX">Texas</option>
                                        <option value="UM">United States Minor Outlying Islands</option>
                                        <option value="VI">United States Virgin Islands</option>
                                        <option value="UT">Utah</option>
                                        <option value="VT">Vermont</option>
                                        <option value="VA">Virginia</option>
                                        <option value="UM-79">Wake Island</option>
                                        <option value="WA">Washington</option>
                                        <option value="WV">West Virginia</option>
                                        <option value="WI">Wisconsin</option>
                                        <option value="WY">Wyoming</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="postal-code" class="form-label">Postal Code <span class="text-danger">*</span></label>
                                    <input type="text" min="0" name="postal-code" required id="postal-code" class="form-control" placeholder="94119" aria-describedby="helpId">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <h6 class="text-uppercase my-3 text-start"> authorization</h6>
                <div class="card p-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="mandateText" id="mandateText" required>
                        <label class="form-check-label" for="mandateText">
                            By clicking PayNow, I authorize Braintree, a service of PayPal, on behalf of Alkouser (i) to verify my bank account information using bank information and consumer reports and (ii) to debit my bank account.
                        </label>
                    </div>
                </div>
                <!-- tokenizepayload & devicedata  -->
                <input type="hidden" id="nonce" name="tokenizedPayload" />
                <input type="hidden" id="deviceData" name="deviceData" />
                <div class="mt-3 text-end">
                    <button type="submit" class="btn btn-info">Pay Now</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://js.braintreegateway.com/web/3.97.2/js/client.min.js"></script>
    <script src="https://js.braintreegateway.com/web/3.97.2/js/data-collector.min.js"></script>
    <script src="https://js.braintreegateway.com/web/3.97.2/js/us-bank-account.min.js"></script>

    <script>
        function changeOwnershipType(v) {
            let divP = document.getElementById('personal-account');
            let divB = document.getElementById('business-account');
            divP.innerHTML = '';
            divP.style.display = 'none';
            divB.innerHTML = '';
            divB.style.display = 'none';
            if (v == 'personal') {
                let data = ` <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="first-name" class="form-label">First Name <span class="text-danger">*</span></label>
                                            <input type="text" min="0" name="first-name" required id="first-name" class="form-control" placeholder="first name" aria-describedby="helpId">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="last-name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                            <input type="text" min="0" name="last-name" required id="last-name" class="form-control" placeholder="last name" aria-describedby="helpId">
                                        </div>
                                    </div>
                </div>`;
                divP.innerHTML = data;
                divP.style.display = 'block';
            } else {
                let data = ` <div class="mb-3">
                                    <label for="business-name" class="form-label">Business Name <span class="text-danger">*</span></label>
                                    <input type="text" min="0" name="business-name" required id="business-name" class="form-control" placeholder="business name" aria-describedby="helpId">
                </div>`;
                divB.innerHTML = data;
                divB.style.display = 'block';
            }
        }
    </script>

    <script>
        function validateForm() {
            event.preventDefault();
            var routingNumberInput = document.querySelector('input[name="routing-number"]');
            var accountNumberInput = document.querySelector('input[name="account-number"]');
            var accountTypeInput = document.querySelector('input[name="account-type"]:checked');
            var ownershipTypeInput = document.querySelector('input[name="ownership-type"]:checked');
            var firstNameInput = document.querySelector('input[name="first-name"]');
            var lastNameInput = document.querySelector('input[name="last-name"]');
            var businessNameInput = document.querySelector('input[name="business-name"]');
            var billingAddressStreetInput = document.querySelector('input[name="street-address"]');
            var billingAddressExtendedInput = document.querySelector('input[name="extended-address"]');
            var billingAddressLocalityInput = document.querySelector('input[name="locality"]');
            var billingAddressRegionSelect = document.querySelector('select[name="region"]');
            var billingAddressPostalInput = document.querySelector('input[name="postal-code"]');

            var loader = document.getElementById('loader');
            var errorsite = document.getElementById('errorsite');
            var errorType = document.getElementById('errorType');
            var errorMsg = document.getElementById('errorMsg');
            loader.style.display = "flex";

            $.ajax({
                url: 'token.php',
                type: 'get',
                dataType: 'json',
                success: function(data) {
                    const form = document.getElementById('payment-form');

                    // device data collection  and put to id deviceData inut field
                    braintree.client.create({
                        authorization: data
                    }, function(err, clientInstance) {
                        // Creation of any other components...
                        braintree.dataCollector.create({
                            client: clientInstance
                        }, function(err, dataCollectorInstance) {
                            if (err) {
                                console.log(err);
                                throw (err);
                            }
                            // At this point, you should access the dataCollectorInstance.deviceData value and provide it
                            // to your server, e.g. by injecting it into your form as a hidden input.
                            var deviceData = dataCollectorInstance.deviceData;
                            document.getElementById('deviceData').value = deviceData;
                        });
                    });

                    // document.getElementById('nonce').value = 'tokenizedPayload.nonce';
                    // form.submit();
                    // us bank instance and tokenization of bank detial at last put the tokenized nonce to id nonce input filed
                    braintree.client.create({
                        authorization: data
                    }, function(clientErr, clientInstance) {
                        if (clientErr) {
                            console.error('There was an error creating the Client.');
                            loader.style.display = "none";
                            errorType.innerText = 'Braintree client error';
                            errorMsg.innerText = clientErr.message;
                            errorsite.style.display = "flex";
                            throw clientErr;
                        }

                        braintree.usBankAccount.create({
                            client: clientInstance
                        }, function(usBankAccountErr, usBankAccountInstance) {
                            if (usBankAccountErr) {
                                console.error('There was an error creating the USBankAccount instance.');
                                loader.style.display = "none";
                                errorType.innerText = 'Braintree us bank Error';
                                errorMsg.innerText = usBankAccountErr.message;
                                errorsite.style.display = "flex";
                                throw usBankAccountErr;
                            }

                            var bankDetails = {
                                routingNumber: routingNumberInput.value,
                                accountNumber: accountNumberInput.value,
                                accountType: accountTypeInput.value,
                                ownershipType: ownershipTypeInput.value,
                                billingAddress: {
                                    streetAddress: billingAddressStreetInput.value,
                                    extendedAddress: billingAddressExtendedInput.value,
                                    locality: billingAddressLocalityInput.value,
                                    region: billingAddressRegionSelect.value,
                                    postalCode: billingAddressPostalInput.value
                                }
                            };

                            if (bankDetails.ownershipType === 'personal') {
                                bankDetails.firstName = firstNameInput.value;
                                bankDetails.lastName = lastNameInput.value;
                            } else {
                                bankDetails.businessName = businessNameInput.value;
                            }

                            // tokenizing the bankdetail 
                            usBankAccountInstance.tokenize({
                                bankDetails: bankDetails,
                                mandateText: 'By clicking PayNow, I authorize Braintree, a service of PayPal, on behalf of Alkouser (i) to verify my bank account information using bank information and consumer reports and (ii) to debit my bank account.'
                            }, function(tokenizeErr, tokenizedPayload) {
                                if (tokenizeErr) {
                                    loader.style.display = "none";
                                    errorType.innerText = 'Braintree bank info tokenize error';
                                    errorMsg.innerText = tokenizeErr;
                                    errorsite.style.display = "flex";
                                    console.error('There was an error tokenizing the bank details.');
                                    throw tokenizeErr;
                                }

                                // Submit tokenizedPayload.nonce to your server as you would
                                document.getElementById('nonce').value = tokenizedPayload.nonce;
                                form.submit();
                                // other payment method nonces.
                            });
                        });
                    });
                },
                error: function(err) {
                    loader.style.display = "none";
                    errorType.innerText = 'Application error';
                    errorMsg.innerText = 'Could not create client token';
                    errorsite.style.display = "flex";
                    console.log(err);
                },
            });

            return true;
        }

        // document.getElementById('submitBTN').addEventListener('click', function(event) {
        //     event.preventDefault();
        //     var routingNumberInput = document.querySelector('input[name="routing-number"]');
        //     var accountNumberInput = document.querySelector('input[name="account-number"]');
        //     var accountTypeInput = document.querySelector('input[name="account-type"]:checked');
        //     var ownershipTypeInput = document.querySelector('input[name="ownership-type"]:checked');
        //     var firstNameInput = document.querySelector('input[name="first-name"]');
        //     var lastNameInput = document.querySelector('input[name="last-name"]');
        //     var businessNameInput = document.querySelector('input[name="business-name"]');
        //     var billingAddressStreetInput = document.querySelector('input[name="street-address"]');
        //     var billingAddressExtendedInput = document.querySelector('input[name="extended-address"]');
        //     var billingAddressLocalityInput = document.querySelector('input[name="locality"]');
        //     var billingAddressRegionSelect = document.querySelector('select[name="region"]');
        //     var billingAddressPostalInput = document.querySelector('input[name="postal-code"]');

        //     $.ajax({
        //         url: 'token.php',
        //         type: 'get',
        //         dataType: 'json',
        //         success: function(data) {
        //             const form = document.getElementById('payment-form');

        //             // device data collection 
        //             braintree.client.create({
        //                 authorization: data
        //             }, function(err, clientInstance) {
        //                 // Creation of any other components...
        //                 braintree.dataCollector.create({
        //                     client: clientInstance
        //                 }, function(err, dataCollectorInstance) {
        //                     if (err) {
        //                         console.log(err);
        //                         return false;
        //                     }
        //                     // At this point, you should access the dataCollectorInstance.deviceData value and provide it
        //                     // to your server, e.g. by injecting it into your form as a hidden input.
        //                     var deviceData = dataCollectorInstance.deviceData;
        //                     document.getElementById('deviceData').value = deviceData;
        //                 });
        //             });

        //             // document.getElementById('nonce').value = 'tokenizedPayload.nonce';
        //             // form.submit();

        //             braintree.client.create({
        //                 authorization: data
        //             }, function(clientErr, clientInstance) {
        //                 if (clientErr) {
        //                     console.error('There was an error creating the Client.');
        //                     throw clientErr;
        //                 }

        //                 braintree.usBankAccount.create({
        //                     client: clientInstance
        //                 }, function(usBankAccountErr, usBankAccountInstance) {
        //                     var bankDetails = {
        //                         routingNumber: routingNumberInput.value,
        //                         accountNumber: accountNumberInput.value,
        //                         accountType: accountTypeInput.value,
        //                         ownershipType: ownershipTypeInput.value,
        //                         billingAddress: {
        //                             streetAddress: billingAddressStreetInput.value,
        //                             extendedAddress: billingAddressExtendedInput.value,
        //                             locality: billingAddressLocalityInput.value,
        //                             region: billingAddressRegionSelect.value,
        //                             postalCode: billingAddressPostalInput.value
        //                         }
        //                     };

        //                     if (bankDetails.ownershipType === 'personal') {
        //                         bankDetails.firstName = firstNameInput.value;
        //                         bankDetails.lastName = lastNameInput.value;
        //                     } else {
        //                         bankDetails.businessName = businessNameInput.value;
        //                     }

        //                     usBankAccountInstance.tokenize({
        //                         bankDetails: bankDetails,
        //                         mandateText: 'By clicking PayNow, I authorize Braintree, a service of PayPal, on behalf of Alkouser (i) to verify my bank account information using bank information and consumer reports and (ii) to debit my bank account.'
        //                     }, function(tokenizeErr, tokenizedPayload) {
        //                         if (tokenizeErr) {
        //                             console.error('There was an error tokenizing the bank details.');
        //                             throw tokenizeErr;
        //                         }

        //                         // Submit tokenizedPayload.nonce to your server as you would
        //                         document.getElementById('nonce').value = tokenizedPayload.nonce;
        //                         form.submit();
        //                         // other payment method nonces.
        //                     });
        //                 });
        //             });
        //         },
        //         error: function(err) {
        //             console.log(err);
        //         },
        //     });
        // });
    </script>
</body>