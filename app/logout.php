<?php

	require_once 'connexion_bdd.php';
	require_once 'functions.php';
	require_once 'session.php';

	session_start();
	update_status($bdd, $id_user, 4);
	session_unset(); 
	session_destroy();
	header("Location: ../index.php?logout=ok");
	exit();
	
?>