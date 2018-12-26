<?php

	try {
		$bdd = new PDO('mysql:host=localhost;dbname=messagerie', 'root', '');
		$bdd->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$bdd->exec('SET NAMES utf8');
	}

	catch (Exception $e) {
    	echo 'Erreur : '.$e->getMessage().'<br>';
    	echo 'N° : '.$e->getCode();
	}

?>