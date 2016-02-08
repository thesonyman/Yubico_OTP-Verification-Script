<?php
header("Content-Type: text/plain; charset=utf-8;");

require_once 'Yubico.php'; // You will need this https://github.com/Yubico/php-yubico

$conn = new PDO('mysql:dbname=DBNAME;host=HOSTNAME', 'DBUSER', 'DBPASS');
$Auth_Yubico = new Auth_Yubico('API_ID', 'API_KEY'); // Generate a new id+key from https://upgrade.yubico.com/getapikey

class YubicoOTP {
	var $otp;
	var $user_id;
	
	public function GetStaticOTP($otp){
		$array = array();
		
		$array['YubicoOTP']['StaticOTP'] = substr($otp, 0, 12);
		echo json_encode($array, JSON_FORCE_OBJECT);
	}
	
	public function CheckOTP($otp, $user_id){
		global $conn;
		$array = array();
		$StaticOTP1 = substr($otp, 0, 12); // Get first 12 characters.
		
		$db_statement1 = $conn->prepare("SELECT `yubico_otp` FROM `users` WHERE id=:id"); // Get OTP from users table.
		$db_statement1->execute(array(':id' => $user_id));
		$PDO_CheckStaticOTP1 = $db_statement1->fetch();
		
		if ($StaticOTP1 == $PDO_CheckStaticOTP1[0]) { // Check the first 12 characters of the OTP given and match it with users saved OTP to verify its their key.
			$array['YubicoOTP']['VerifyOTP']['STATUS'] = "true";
			$array['YubicoOTP']['VerifyOTP']['SUCCESS']['INFO'] = "OTP Matches.";
		} else {
			$array['YubicoOTP']['VerifyOTP']['STATUS'] = "false";
			$array['YubicoOTP']['VerifyOTP']['ERROR']['INFO'] = "Does not match key(s) stored in database.";
		}
		
		echo json_encode($array, JSON_FORCE_OBJECT);
	}
	
	public function VerifyOTP($otp, $user_id){
		global $conn;
		global $Auth_Yubico;
		$array = array();
		$StaticOTP2 = substr($otp, 0, 12); // Get first 12 characters
		
		$db_statement2 = $conn->prepare("SELECT `yubico_otp` FROM `users` WHERE id=:id"); // Get OTP from users table
		$db_statement2->execute(array(':id' => $user_id));
		$PDO_CheckStaticOTP2 = $db_statement2->fetch();
		
		if (empty($otp)) {
			$array['YubicoOTP']['VerifyOTP']['ERROR']['INFO'] = "No OTP was given.";
		} elseif (empty($user_id)) {
			$array['YubicoOTP']['VerifyOTP']['ERROR']['INFO'] = "No UserID was given.";
		} elseif ($StaticOTP2 == $PDO_CheckStaticOTP2[0]) { // Check the first 12 characters of the OTP given and match it with users saved OTP to verify its their key.
			$auth = $Auth_Yubico->verify($otp); // Challenge the OTP given.
			if (PEAR::isError($auth)) {
				$array['YubicoOTP']['VerifyOTP']['STATUS'] = "false";
				$array['YubicoOTP']['VerifyOTP']['ERROR']['INFO'] = $auth->getMessage();
				$array['YubicoOTP']['VerifyOTP']['ERROR']['DEBUG'] = $Auth_Yubico->getLastResponse();
			}  else {
				$array['YubicoOTP']['VerifyOTP']['STATUS'] = "true";
				$array['YubicoOTP']['VerifyOTP']['SUCCESS']['INFO'] = "Authenticated.";
			}
		} else {
			$array['YubicoOTP']['VerifyOTP']['STATUS'] = "false";
			$array['YubicoOTP']['VerifyOTP']['ERROR']['INFO'] = "Does not match key(s) stored in database.";
		}
		echo json_encode($array, JSON_FORCE_OBJECT);
	}
}
?>
