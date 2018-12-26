<?php

	if ( !isset( $_SESSION ) ) {
		session_start();
	}

	if ( isset($_POST["admin_login"]) && isset($_POST["admin_password"]) ) {
		require('../app/connexion_bdd.php');
		$admin_log = $_POST["admin_login"];
		$admin_pw = $_POST["admin_password"];
		if ( trim($admin_log) == '' || trim($admin_pw) == '' ) {
			$feedback = 'Veuillez remplir tous les champs';
			header("Location: connexion.php");
			exit();
		}
		else {
			$admin_request = $bdd->query("SELECT * FROM admin WHERE login = '".$admin_log."';");
			$admin_request = $admin_request->fetchAll(PDO::FETCH_OBJ);
			if ( count($admin_request) > 0 ) {
				foreach ($admin_request as $admin) {
					$hash = $admin->password;
					if ( password_verify($admin_pw, $hash) ) {
						$_SESSION["id_admin"] = $admin->id_admin;
						$id_admin = $_SESSION["id_admin"];
						header("Location: backoffice.php");
						exit();
					}
					else {
						$feedback = 'Identifiant ou mot de passe incorrect';
						header("Location: connexion.php");
						exit();
					}
				}
			}
			else {
				$feedback = 'Identifiant ou mot de passe incorrect';
				header("Location: connexion.php");
				exit();
			}
		}
	}

	if ( isset($_POST["email"]) && isset($_POST["password"]) ) {

		require('app/connexion_bdd.php');
		$form_email = $bdd->quote($_POST["email"]);
		$form_pw = $_POST["password"];

		if ( trim($form_email) == '' || trim($form_pw) == '' ) {
			$feedback = 'Veuillez remplir tous les champs';
		}
		else {
			$request = $bdd->query("SELECT id_user, email, password FROM user WHERE email = ".$form_email);
			$request = $request->fetchAll(PDO::FETCH_OBJ);
			if ( count($request) > 0 ) {
				foreach ($request as $user) {
					$hash = $user->password;
					if ( password_verify($form_pw, $hash) ) {
						$_SESSION["id_user"] = $user->id_user;
						header("Location: interface.php");
						exit();
					}
					else {
						$feedback = 'Email ou mot de passe incorrect';
					}
				}
			}
			else {
				$feedback = 'Email ou mot de passe incorrect';
			}
		}
	}

	if ( isset($_GET['logout']) ) {
		$feedback = 'Vous avez été correctement déconnecté-e';
	}

?>