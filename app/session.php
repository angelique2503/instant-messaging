<?php

	require_once 'functions.php';

	if ( !isset( $_SESSION ) ) {
		session_start();
	}

	if ( !isset( $_SESSION['id_user'] ) ) {
		header("Location: index.php");
		exit();
	}
	else {
		$id_user = $_SESSION['id_user'];
		update_status($bdd, $id_user, 1);
	}
	
?>