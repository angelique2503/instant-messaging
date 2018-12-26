<?php

	function create_MySQL_event($bdd, $id_user, $id_receiver, $start_time) {
		$drop = "DROP EVENT IF EXISTS `Message send by user ".$id_user." to user ".$id_receiver."`; ";
		$create = "CREATE DEFINER = `root`@`localhost` 
		EVENT `Message send by user ".$id_user." to user ".$id_receiver."` ON SCHEDULE 
		EVERY 1 SECOND STARTS '".$start_time.".000000' ENDS '2018-09-09 18:30:00.000000' 
		ON COMPLETION NOT PRESERVE ENABLE DO 
		SELECT COUNT(user_message) AS nb_message FROM message WHERE fk_user = $id_receiver AND fk_user_recepteur = $id_user";
		$request = $bdd->query($drop);
		return $request;
	}

	function send_message($bdd,$message,$user_id,$recepteur_id) {
		$request = "INSERT INTO message (`fk_user`,`fk_user_recepteur`,`user_message`,`msg_date`) VALUES (:fk_user,:fk_user_recepteur,:user_message,NOW())";
		$prepare = $bdd->prepare( $request, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL) );
		$prepare->execute(array(
			":fk_user" => $user_id,
			":fk_user_recepteur" => $recepteur_id,
			":user_message" => $message
		));
	}

	function update_accountInfos($bdd,$column,$value,$id_user) {
		$request = "UPDATE user SET $column = :$column WHERE id_user = :id_user;";
		$prepare = $bdd->prepare( $request, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL) );
		$prepare->execute(array(
  			$column => $value,
  			':id_user' => $id_user
  		));
	}

	function update_status($bdd, $id_user, $id_status) {
		$update = $bdd->exec("UPDATE user SET fk_user_status = $id_status WHERE id_user = $id_user");
	}

	function write_CSS($color_name) {

		// Récupérer les nuances de la couleur du thème
		$colors = get_colorScheme( $color_name );
		foreach ($colors as $color) {
			$l = $color['light'];
			$m = $color['medium'];
			$sd = $color['semi_dark'];
			$d = $color['dark'];
		}
		
		// CSS
		$style = "

			/* Update colors */

			h1, h2, h3, .user-pseudo, p, .user-message, input, textarea#message, button, strong, a, select { color: $l!important; }
			label, [placeholder], .msg-date { color: $m!important; }
			.menu-button { background: $m!important; border-color: $d!important; }
			.menu-button:hover { background: $d!important; border-color: $m!important; }
			#friend-list a { background: $m; border-color: $m; }
			#friend-list a:hover { background: $d; }
			.active { border-color: $l!important; }
			ul#chat-box li { border-color: $m!important; }
			input, textarea, select { border-color: $m!important; }
			input:focus, textarea:focus { border-color: $l!important; }
			button { background: $m!important; border-color: $m; }
			ul#chat-box, main { background: $sd!important; }
			body, section { background: $d!important; }

			h1, h2, h3, .user-pseudo, p, .user-message, input, textarea#message, button, strong, a, select,
			label, [placeholder], .msg-date, .menu-button, .menu-button:hover, #friend-list a, #friend-list a:hover,
			.active, ul#chat-box li, button, ul#chat-box, body, main, section
			{ transition: all 0.5s; }

		";

		return $style;
		//$file = fopen($css_path,'a+');
		/*fwrite($file,$style);
		fclose($file);*/
	}

	function get_colorScheme($color) {

		$green[] = [
			'light'=>'#18FF92',
			'medium'=>'#124A34',
			'semi_dark'=>'#111D1C',
			'dark'=>'#111116'
		];

		$pink[] = [
			'light'=>'#FF1461',
			'medium'=>'#4A1228',
			'semi_dark'=>'#1D111A',
			'dark'=>'#111116'
		];

		$purple[] = [
			'light'=>'#FB89FB',
			'medium'=>'#492E4D',
			'semi_dark'=>'#1D1721',
			'dark'=>'#111116'
		];

		$blue[] = [
			'light'=>'#5EF3FB',
			'medium'=>'#24474D',
			'semi_dark'=>'#151C21',
			'dark'=>'#111116'
		];

		if ( $color == 'green' ) {
			return $green;
		}
		else if ( $color == 'pink' ) {
			return $pink;
		}
		else if ( $color == 'purple' ) {
			return $purple;
		}
		else if ( $color == 'blue' ) {
			return $blue;
		}

	}

?>