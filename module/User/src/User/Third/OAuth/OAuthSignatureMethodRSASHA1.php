<?php
namespace User\Third\OAuth;

abstract class OAuthSignatureMethodRSASHA1 extends OAuthSignatureMethod {
    public function get_name() {
        return "RSA-SHA1";
    }

    protected abstract function fetch_public_cert(&$request);
    protected abstract function fetch_private_cert(&$request);

    public function build_signature($request, $consumer, $token) {
        $base_string = $request->get_signature_base_string();
        $request->base_string = $base_string;
        $cert = $this->fetch_private_cert($request);
        $privatekeyid = openssl_get_privatekey($cert);
        $ok = openssl_sign($base_string, $signature, $privatekeyid);
        openssl_free_key($privatekeyid);
        return base64_encode($signature);
    }

    public function check_signature($request, $consumer, $token, $signature) {
        $decoded_sig = base64_decode($signature);
        $base_string = $request->get_signature_base_string();
        $cert = $this->fetch_public_cert($request);
        $publickeyid = openssl_get_publickey($cert);
        $ok = openssl_verify($base_string, $decoded_sig, $publickeyid);
        openssl_free_key($publickeyid);
        return $ok == 1;
    }
}