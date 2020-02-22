<?php

class License {
	
	const STDIN = true;
	const HTTP_SERVER = "http://opencart2/";
	
	/**
	* @param 2035-02-28|opencart2|Александр|blblago@ukr.net
	* Можно генерить ключ для создания лицензии c380a00bc920d4f8
	*/
	public function genlicense($data) {
		
		$encrypt = @openssl_encrypt($data, "aes-256-cbc", "13de59d5e2_b9b7d", false, "c380a00bc920d4f8");
		$encrypt_res = (base64_encode($encrypt.":c380a00bc920d4f8"));
		
		return	$encrypt_res;
	}
	
    public function check($getLicenseKey)
    {
        if (!extension_loaded("openssl") || !function_exists("openssl_decrypt")) {
            $this->error = "Your PHP installation appears to be missing the OpenSSL extension which is require!";
            return false;
        }

        if (!empty($getLicenseKey)) {
            $ext_1 = explode(":", base64_decode(urldecode(trim($getLicenseKey, " \n\t\r"))));

            $getLicenseKey = @openssl_decrypt($ext_1[0], "aes-256-cbc", "13de59d5e2_b9b7d", false, $ext_1[1]);
			
            $ext_2 = explode("|", $getLicenseKey);
            if (is_array($ext_2) && count($ext_2) == 4 && !empty($ext_2[2]) && !empty($ext_2[3])) {
                if (strtotime($ext_2[0] . " 00:00:00") < strtotime("now")) {
                    $this->error = "The license has expired!";
                } else {
                    if (!$this->is_STDIN() && preg_match("/" . preg_quote($ext_2[1]) . "/i", $_SERVER["SERVER_NAME"]) && preg_match("/" . preg_quote($ext_2[1]) . "/i", $_SERVER["HTTP_HOST"]) && preg_match("/" . preg_quote($ext_2[1]) . "/i", self::HTTP_SERVER)) {
					   return true;
                    }
                    if ($this->is_STDIN() && preg_match("/" . preg_quote($ext_2[1]) . "/i", self::HTTP_SERVER)) {
                        return true;
                    }
                }
            }
        }
        break;
    }

    private function is_STDIN()
    {  
        if (defined("STDIN")) {
            return true;
        }
        if ((!isset($_SERVER["REMOTE_ADDR"]) || empty($_SERVER["REMOTE_ADDR"])) && !isset($_SERVER["HTTP_USER_AGENT"]) && 0 < count($_SERVER["argv"])) {
            return true;
        }
        return false;
    }	
	
}

$lic = new License();
$lic_key = $lic->genlicense("2035-02-28|opencart2|Александр|blblago@ukr.net");
echo $lic_key . "<br />";
$check = $lic->check($lic_key);
var_dump($check);