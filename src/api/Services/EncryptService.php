<?php

namespace api\Services;

class EncryptService {

    public static function decode($text, $key): bool|string
    {
        try {
            $c = base64_decode($text);
            $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
            $iv = substr($c, 0, $ivlen);
            $hmac = substr($c, $ivlen, $sha2len = 32);
            $ciphertext_raw = substr($c, $ivlen + $sha2len);
            $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
            $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);

            if (hash_equals($hmac, $calcmac))// сравнение, не подверженное атаке по времени
            {
                return $original_plaintext;
            }
            return false;
        }
        catch (\Exception $e) {
            return false;
        }
    }

    public static function encode($text, $key): bool|string
    {
        try {
            $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
            $iv = openssl_random_pseudo_bytes($ivlen);
            $ciphertext_raw = openssl_encrypt($text, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
            $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
            return base64_encode( $iv.$hmac.$ciphertext_raw );
        }
        catch(\Exception $e) {
            return false;
        }
    }
}