<?php
require_once "Display.php";
require_once "Database.php";

if(!empty($_GET['action'])){
    switch($_GET['action']){
        case "createAccount":
			if(isset($_SERVER['PHP_AUTH_USER']) && isset($_POST['email']) && isset($_SERVER['PHP_AUTH_PW'])){
				echo Database::createAccount($_SERVER['PHP_AUTH_USER'], $_POST['email'], $_SERVER['PHP_AUTH_PW']);
			}else{
				echo Display::json(403);
			}
        break;
		case "getPasswords":
			if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])){
				echo Database::getPasswords($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
			}else{
				echo Display::json(403);
			}
		break;
		case "savePassword":
			if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) && isset($_POST['website']) && isset($_POST['username']) && isset($_POST['password'])){
				echo Database::savePassword($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'], $_POST['website'], $_POST['username'], $_POST['password']);
			}else{
				echo Display::json(403);
			}
		break;
		case "editPassword":
			if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) && isset($_POST['password_id']) && isset($_POST['website']) && isset($_POST['username']) && isset($_POST['password'])){
				echo Database::editPassword($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'], $_POST['password_id'], $_POST['website'], $_POST['username'], $_POST['password']);
			}else{
				echo Display::json(403);
			}
		break;
		case "deletePassword":
			if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) && isset($_POST['password_id'])){
				echo Database::deletePassword($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'], $_POST['password_id']);
			}else{
				echo Display::json(403);
			}
		break;
		case "deleteAccount":
			if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])){
				echo Database::deleteAccount($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
			}else{
				echo Display::json(403);
			}
		break;
        default:
            echo Display::json(401);
        break;
    }
}else{
    echo Display::json(400);
}
?>