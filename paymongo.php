<?php

$secretKey = 'sk_test_Z1uRQ7sjBBdW7q2auivmS1PL';

$data = [
    'data' => [
        'attributes' => [
            'amount' => 30000,
            'currency' => 'PHP',
            'description' => 'Test Payment Link',
            'metadata' => [
                'order_id' => '123456',
            ],
        ],
    ],
];

$ch = curl_init('https://api.paymongo.com/v1/links');

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Basic ' . base64_encode($secretKey . ':'),
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(['error' => curl_error($ch)]);
} else {
    $responseData = json_decode($response, true);

    if (isset($responseData['data']['attributes']['checkout_url'])) {
        $jsonString = ['checkout_url' => $responseData['data']['attributes']['checkout_url']];
        $jsonEncodedString = json_encode($jsonString);
        $cleanedString = stripslashes($jsonEncodedString);
        echo $cleanedString;

        // Store the payment ID for status checking
        $paymentId = $responseData['data']['id'];
        
        echo "<br><br><a href='".$responseData['data']['attributes']['checkout_url']."' target='_blank'>Click here to pay</a>";
        
        // Add payment status checking
        echo "<div id='payment-status'>Checking payment status...</div>";
        echo "
        <script>
        function checkPaymentStatus() {
            fetch('check_payment_status.php?payment_id=" . $paymentId . "')
                .then(response => response.json())
                .then(data => {
                    if(data.status === 'paid') {
                        document.getElementById('payment-status').innerHTML = 'Payment completed successfully!';
                        window.location.href = 'success_page.php'; // Redirect to success page
                    } else if(data.status === 'unpaid') {
                        document.getElementById('payment-status').innerHTML = 'Waiting for payment...';
                        setTimeout(checkPaymentStatus, 5000); // Check again after 5 seconds
                    } else {
                        document.getElementById('payment-status').innerHTML = 'Error checking payment status';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('payment-status').innerHTML = 'Error checking payment status';
                });
        }
        
        // Start checking payment status
        checkPaymentStatus();
        </script>
        ";
    } else {
        echo json_encode(['error' => 'Error creating payment link: Unknown error occurred.']);
    }
}

curl_close($ch);

?>