<?php

//  Coded By Hayder J. Kadhim
//  08/31/16
//  documentation and How-To :
//  Email : haeder.algorabi@gmail.com
//  alameersoft.com && hayderalgorabi.com


class zainjwt
{
    private $token;

    function __construct($Data_Array,$Api_Secret)
    {

        $this->token = zainjwt::encode($Data_Array, $Api_Secret);

    }
    public function sendData($MERCHANT_ID,$Api_NUMBER,$Req_Mode = "test"){
        $url = null;
        switch($Req_Mode){
            case "test":
                $url = "https://test.zaincash.iq/transaction/init";
                break;
            case "live":
                $url = "https://api.zaincash.iq/transaction/init";
                break;

            default :
                $url = "https://test.zaincash.iq/transaction/init";
        }
        $Form = '<html><body onload="">
                 <form id="go" method="post" action="'.$url.'">
                 <input type="hidden" name="token" value="'.$this->token.'">
                 <input type="hidden" name="merchantId" value="'.$MERCHANT_ID.'">
                 <button class="zaincash-btn" type="submit" >
                 <img  style="vertical-align:middle" src="https://test.zaincash.iq/images/zaincash-ar.png">
                 </button>
                 </form>
                 <script>document.getElementById(\'go\').submit();</script>
                 </body>
                 </html>
                ';
        echo $Form;
    }

    private static function decode($zainjwt, $key = null, $verify = true)
    {
        $tks = explode('.', $zainjwt);
        if (count($tks) != 3) {
            throw new UnexpectedValueException('Wrong number of segments');
        }
        list($headb64, $bodyb64, $cryptob64) = $tks;
        if (null === ($header = zainjwt::jsonDecode(zainjwt::urlsafeB64Decode($headb64)))) {
            throw new UnexpectedValueException('Invalid segment encoding');
        }
        if (null === $payload = zainjwt::jsonDecode(zainjwt::urlsafeB64Decode($bodyb64))) {
            throw new UnexpectedValueException('Invalid segment encoding');
        }
        $sig = zainjwt::urlsafeB64Decode($cryptob64);
        if ($verify) {
            if (empty($header->alg)) {
                throw new DomainException('Empty algorithm');
            }
            if ($sig != zainjwt::sign("$headb64.$bodyb64", $key, $header->alg)) {
                throw new UnexpectedValueException('Signature verification failed');
            }
        }
        return $payload;
    }

    private static function encode($payload, $key, $algo = 'HS256')
    {
        $header = array('typ' => 'zainjwt', 'alg' => $algo);
        $segments = array();
        $segments[] = zainjwt::urlsafeB64Encode(zainjwt::jsonEncode($header));
        $segments[] = zainjwt::urlsafeB64Encode(zainjwt::jsonEncode($payload));
        $signing_input = implode('.', $segments);
        $signature = zainjwt::sign($signing_input, $key, $algo);
        $segments[] = zainjwt::urlsafeB64Encode($signature);
        return implode('.', $segments);
    }

    private static function sign($msg, $key, $method = 'HS256')
    {
        $methods = array(
            'HS256' => 'sha256',
            'HS384' => 'sha384',
            'HS512' => 'sha512',
        );
        if (empty($methods[$method])) {
            throw new DomainException('Algorithm not supported');
        }
        return hash_hmac($methods[$method], $msg, $key, true);
    }

    private static function jsonDecode($input)
    {
        $obj = json_decode($input);
        if (function_exists('json_last_error') && $errno = json_last_error()) {
            zainjwt::_handleJsonError($errno);
        } else if ($obj === null && $input !== 'null') {
            throw new DomainException('Null result with non-null input');
        }
        return $obj;
    }

    private static function jsonEncode($input)
    {
        $json = json_encode($input);
        if (function_exists('json_last_error') && $errno = json_last_error()) {
            zainjwt::_handleJsonError($errno);
        } else if ($json === 'null' && $input !== null) {
            throw new DomainException('Null result with non-null input');
        }
        return $json;
    }

    private static function urlsafeB64Decode($input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

    private static function urlsafeB64Encode($input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    private static function _handleJsonError($errno)
    {
        $messages = array(
            JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
            JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
            JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON'
        );
        throw new DomainException(
            isset($messages[$errno])
                ? $messages[$errno]
                : 'Unknown JSON error: ' . $errno
        );
    }
}