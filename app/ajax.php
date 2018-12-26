<?php

	/* Traitement des requêtes AJAX */

	require_once 'connexion_bdd.php';
	require 'functions.php';
	require 'classes/user.class.php';

	// Créer l'objet User
	if ( isset( $_POST["user_id"] ) ) {
		$id_user = $bdd->quote($_POST["user_id"]);
		$logged_user = new User($bdd, $id_user);
	}

	// Envoyer un message
	if ( isset( $_POST["msg"] ) && isset( $_POST["user_id"] ) && isset( $_POST["recepteur_id"] ) ) {
		send_message( $bdd, $_POST["msg"], $_POST["user_id"], $_POST["recepteur_id"] );
	}

	// Mettre à jour les informations du compte de l'utilisateur
	if ( isset( $_POST["column_name"]) && isset( $_POST["user_value"]) && isset( $_POST["user_id"]) ) {
		update_accountInfos( $bdd, $_POST["column_name"], $_POST["user_value"], $_POST["user_id"] );
	}

	if ( isset( $_GET["user_id"] ) && isset( $_GET["id_status"] ) ) {
		$request = "UPDATE user SET fk_user_status = :fk_user_status WHERE id_user = :id_user;";
		$prepare = $bdd->prepare( $request, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL) );
		$prepare->execute(array(
  			':fk_user_status' => $_GET["id_status"],
  			':id_user' => $_GET["user_id"]
  		));
	}

	date_default_timezone_set('Europe/Paris');

	/*function get_receiverProfile($bdd, $id_receiver, $name, $pseudo, $status) {
		$receiver = $bdd->query('SELECT 
			firstname, pseudo, fk_user_status, css_status 
			FROM user 
			LEFT OUTER JOIN user_status ON fk_user_status = id_user_status
			WHERE id_user = '.$bdd->quote($id_receiver)
		);
		$array = [
			'firstname' => $name,
			'pseudo' => $pseudo,
			'id_status' => $status,
			'css_class' => '',
			'receiver_send_a_message' => false,
			'receiver_message' => '',
			'msg_hour' => '',
			'msg_day' => '',
			'comparer_date' => ''
		];
		foreach ($receiver->fetchAll(PDO::FETCH_OBJ) as $r) {

			$array['css_class'] = $r->css_status;

			if ( $r->firstname != $name ) {
				$array['firstname'] = $r->firstname;
			}
			if ( $r->pseudo != $pseudo ) {
				$array['pseudo'] = $r->pseudo;
			}
			if ( $r->fk_user_status != $status ) {
				$array['id_status'] = $r->fk_user_status;
			}

		}
		return $array;
	}*/

	// Mise à jour en temps réel
	if (
		isset( $_GET['id_user'] ) &&
		isset( $_GET['id_receiver'] ) &&
		isset( $_GET['receiver_name'] ) &&
		isset( $_GET['receiver_pseudo'] ) &&
		isset( $_GET['receiver_id_status'] ) &&
		isset( $_GET['user_time'] )
	) {

		$r_name = $_GET['receiver_name'];
		$r_pseudo = $_GET['receiver_pseudo'];
		$r_status = $_GET['receiver_id_status'];
		$user_time = $_GET['user_time'];

		$last_message_send_by_receiver = $bdd->query('
			SELECT id_user, firstname, pseudo,
			user_message, fk_user_status, css_status,
			msg_date,
			DATE_FORMAT(msg_date, "%H:%i:%s") AS hour,
			DATE_FORMAT(msg_date, "%d/%m/%y") AS day
			FROM message
			LEFT OUTER JOIN user ON fk_user = id_user
			LEFT OUTER JOIN user_status ON fk_user_status = id_user_status
			WHERE id_user = '.$bdd->quote($_GET['id_receiver']).'
			ORDER BY msg_date DESC LIMIT 1
		');

		$array = [
			'firstname' => $r_name,
			'pseudo' => $r_pseudo,
			'id_status' => $r_status,
			'css_class' => '',
			'receiver_send_a_message' => false,
			'receiver_message' => '',
			'msg_hour' => '',
			'msg_day' => '',
			'comparer_date' => ''
		];

		if ( $array['receiver_send_a_message'] == true ) {
			$array['receiver_send_a_message'] = false;
			$array['receiver_message'] = '';
			$array['msg_hour'] = '';
			$array['msg_day'] = '';
		}

		// Détecter si le destinataire a envoyé un message
		foreach ($last_message_send_by_receiver->fetchAll(PDO::FETCH_OBJ) as $last_receiver_message) {
			$array['css_class'] = $last_receiver_message->css_status;
			if ( strtotime($last_receiver_message->msg_date) > strtotime($user_time) /*new DateTime($last_receiver_message->msg_date) > new DateTime($user_time)*/ ) {
				//$array = get_receiverProfile($bdd, $_GET['id_receiver'], $r_name, $r_pseudo, $r_status);
				$array['receiver_send_a_message'] = true;
				$array['receiver_message'] = $last_receiver_message->user_message;
				$array['msg_hour'] = $last_receiver_message->hour;
				$array['msg_day'] = $last_receiver_message->day;
			}
			// Si le destinataire a mis à jour son nom...
			elseif ( $last_receiver_message->firstname != $r_name ) {
				$array['firstname'] = $last_receiver_message->firstname;
			}
			// ... son pseudo
			elseif ( $last_receiver_message->pseudo != $r_pseudo ) {
				$array['pseudo'] = $last_receiver_message->pseudo;
			}
			// ... son statut
			elseif ( $last_receiver_message->fk_user_status != $r_status ) {
				$array['id_status'] = $last_receiver_message->fk_user_status;
			}
			else {
				$array['receiver_send_a_message'] = false;
				$array['receiver_message'] = '';
				$array['msg_hour'] = '';
				$array['msg_day'] = '';
			}
		}

		echo json_encode($array);

	}
	
	if ( isset( $_POST["id_friend"] ) && isset( $_POST["user_id"] ) ) {

		// Lancer la requête
		$request = $logged_user->getConversationWith($_POST["user_id"]);
		echo json_encode( $request->fetchAll() );

	}

	if ( isset( $_GET['id_color'] ) && isset( $_GET['id_user'] ) && isset( $_GET['color_name'] ) ) {

		$color = $bdd->quote($_GET['color_name']);
		$css = write_CSS( $_GET['color_name'] );
		echo json_encode( $css );

	}

	if ( isset($_POST["show_last_message"]) && isset($_POST["user_id"]) && isset($_POST["recepteur_id"]) ) {
		// Créer l'objet User
		$request = $logged_user->getConversationWith($_POST["recepteur_id"],1,"DESC");
		echo json_encode( $request->fetchAll() );
	}

?>
