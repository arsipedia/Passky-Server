<?php
header('Content-Type: application/json; charset=utf-8');
header("Content-Security-Policy: default-src 'none'; frame-ancestors 'none'; object-src 'none'; base-uri 'none'; require-trusted-types-for 'script'; form-action 'none'");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("X-Frame-Options: DENY");
header("Referrer-Policy: no-referrer");
header("Permissions-Policy: interest-cohort=()");

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 86400");

require_once "Settings.php";

$today = date('Y-m-d');

$executed = Settings::readLocalData('cron_executed', true);
if($executed == null){
	$executed = Settings::readLocalData('cron_executed', false);
	if($executed != null){
		$ttl = Settings::ttlLocalData('cron_executed', false);
		if($ttl >= 5) Settings::writeLocalData('cron_executed', $executed, $ttl, true);
	}
}

if($today == $executed){
	echo '{"status":"success"}';
	return;
}

Settings::purgeLocalData();
Settings::writeLocalData('cron_executed', $today, 86400, true);
Settings::writeLocalData('cron_executed', $today, 86400, false);

$maxPasswords = Settings::getMaxPasswords();

try{
	$conn = Settings::createConnection();

	$stmt = $conn->prepare("UPDATE users SET max_passwords = :max_passwords, premium_expires = null WHERE CURDATE() > premium_expires");
	$stmt->bindParam(':max_passwords', $maxPasswords, PDO::PARAM_INT);
	$stmt->execute();
}catch(PDOException) {}
$conn = null;

if(Settings::getDBCacheMode() == 2){
	try{
		$conn = Settings::createConnection();

		$stmt = $conn->prepare("SELECT COUNT(*) AS 'amount' FROM users");
		$stmt->execute();

		$amount = ($stmt->rowCount() == 1) ? $stmt->fetch()['amount'] : -1;
		Settings::writeLocalData('user_count', $amount, 864000, true);
		Settings::writeLocalData('user_count', $amount, 864000, false);
	}catch(PDOException $e) {
		Settings::writeLocalData('user_count', -1, 864000, true);
		Settings::writeLocalData('user_count', -1, 864000, false);
	}

	try{
		$conn = Settings::createConnection();

		$stmt = $conn->prepare("SELECT COUNT(*) AS 'amount' FROM passwords");
		$stmt->execute();

		$amount = ($stmt->rowCount() == 1) ? $stmt->fetch()['amount'] : -1;
		Settings::writeLocalData('password_count', $amount, 864000, true);
		Settings::writeLocalData('password_count', $amount, 864000, false);
	}catch(PDOException $e) {
		Settings::writeLocalData('password_count', -1, 864000, true);
		Settings::writeLocalData('password_count', -1, 864000, false);
	}
}

echo '{"status":"success"}';
?>