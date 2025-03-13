<?php
// Define a static encryption key and method
define('ENCRYPTION_KEY', '###GitGudPark2025SecureKey###'); // Replace with a strong key
define('ENCRYPTION_METHOD', 'AES-256-CBC'); // A secure encryption method

/**
 * Encrypt a given value.
 *
 * @param string $value The value to encrypt.
 * @return string The encrypted string, base64 encoded.
 */
function encrypt($value) {
    $key = hash('sha256', ENCRYPTION_KEY); // Hash the key to ensure it's 256 bits
    $iv = substr(hash('sha256', 'static_iv'), 0, 16); // Use a static IV (not recommended for sensitive data)
    return base64_encode(openssl_encrypt($value, ENCRYPTION_METHOD, $key, 0, $iv));
}

/**
 * Decrypt a given value.
 *
 * @param string $value The encrypted value (base64 encoded).
 * @return string The decrypted string.
 */
function decrypt($value) {
    $key = hash('sha256', ENCRYPTION_KEY); // Hash the key to ensure it's 256 bits
    $iv = substr(hash('sha256', 'static_iv'), 0, 16); // Use the same static IV
    return openssl_decrypt(base64_decode($value), ENCRYPTION_METHOD, $key, 0, $iv);
}