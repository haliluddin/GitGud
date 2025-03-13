<?php
class PayMongoHandler {
    private $secretKey;
    
    public function __construct($secretKey = 'sk_test_Z1uRQ7sjBBdW7q2auivmS1PL') {
        $this->secretKey = $secretKey;
    }
    
    public function createPaymentLink($amount, $description = 'Payment', $metadata = []) {
        $data = [
            'data' => [
                'attributes' => [
                    'amount' => $amount,
                    'currency' => 'PHP',
                    'description' => $description,
                    'metadata' => $metadata,
                ],
            ],
        ];

        $ch = curl_init('https://api.paymongo.com/v1/links');
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($this->secretKey . ':'),
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        if (!$response) {
            return ['error' => 'Failed to create payment link'];
        }
        
        $responseData = json_decode($response, true);
        
        if (isset($responseData['data']['attributes']['checkout_url'])) {
            return [
                'success' => true,
                'payment_id' => $responseData['data']['id'],
                'checkout_url' => $responseData['data']['attributes']['checkout_url'],
                'status_check_html' => $this->generateStatusCheckHtml($responseData['data']['id'])
            ];
        }
        
        return ['error' => 'Error creating payment link'];
    }
    
    public function checkPaymentStatus($paymentId) {
        $ch = curl_init("https://api.paymongo.com/v1/links/{$paymentId}");
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode($this->secretKey . ':')
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        if (!$response) {
            return ['error' => 'Failed to check payment status'];
        }
        
        $responseData = json_decode($response, true);
        
        if (isset($responseData['data']['attributes']['status'])) {
            return [
                'success' => true,
                'status' => $responseData['data']['attributes']['status']
            ];
        }
        
        return ['error' => 'Unable to determine payment status'];
    }
    
    private function generateStatusCheckHtml($paymentId) {
        return '
        <div id="payment-status">Checking payment status...</div>
        <script>
        function checkPaymentStatus() {
            fetch("check_payment_status.php?payment_id=' . $paymentId . '")
                .then(response => response.json())
                .then(data => {
                    if(data.status === "paid") {
                        document.getElementById("payment-status").innerHTML = "Payment completed successfully!";
                        window.location.href = "success_page.php";
                    } else if(data.status === "unpaid") {
                        document.getElementById("payment-status").innerHTML = "Waiting for payment...";
                        setTimeout(checkPaymentStatus, 5000);
                    } else {
                        document.getElementById("payment-status").innerHTML = "Error checking payment status";
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    document.getElementById("payment-status").innerHTML = "Error checking payment status";
                });
        }
        checkPaymentStatus();
        </script>';
    }
}
?>
