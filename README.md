# Yubico_OTP-Verification-Script-
Check/Verify Yubico OTP with REST API

<b>Example:</b>
<pre>
require_once 'class.Yubico_OTP.php';
$YubicoOTP = new YubicoOTP;
echo $YubicoOTP->VerifyOTP($_GET['otp'],$_GET['user_id']);
</pre>

<b>MySQL Table Example:</b>
<pre>
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `yubico_otp` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

INSERT INTO `users` (`id`, `yubico_otp`) VALUES (1, 'ccbbddeertkrctjkkcglfndnlihhnvekchkcctif');
</rpe>
