<?php

	date_default_timezone_set('Europe/Paris');
	$time = date("Y-m-d H:i:s");

	require_once 'app/connexion_bdd.php';
	require_once 'app/session.php';
	require_once 'app/classes/user.class.php';

	// Créer l'objet User
	$logged_user = new User($bdd,$id_user);
	// Lancer la requête
	$logged_user->getUserProfile();
	// Récupérer les variables de la requête
	$user_firstname = $logged_user->getFirstname();
	$user_designID = $logged_user->getDesignID();
	$user_pseudo = $logged_user->getPseudo();
	$user_email = $logged_user->getEmail();
	$user_css_status = $logged_user->getCssStatus();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="author" content="Angélique">
	<meta name="description" content="Messagerie instantanée programmée avec : HTML5/CSS3/AJAX, jQuery/PHP7/MySQL par Angélique, développeuse web junior">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex, nofollow">
	<title>Messagerie instantanée</title>
	<link href="sass/style.css" rel="stylesheet">
</head>
<body>
	<main class="row">

		<aside class="col-m-3 col-l-3">
			<div>
				<h1><span class="user-<?php echo $id_user; ?> user-name"><?php echo $user_firstname; ?></span> | ALIAS <span class="user-<?php echo $id_user; ?> user-pseudo"><?php echo $user_pseudo; ?></span><div class="cercle <?php echo $user_css_status; ?>"></div></h1>
			</div>
			<nav id="menu">
				<ul>
					<li><button class="active menu-button" data-menu="friend-list">Amis</button></li>
					<li><button class="menu-button" data-menu="account">Compte</button></li>
					<li><button class="menu-button" data-menu="design">Design</button></li>
				</ul>
			</nav>

			<div id="friend-list-container" class="layout-container">
				<?php
					// Lancer les requêtes pour récupérer les amis
					$friends = $logged_user->getUserFriends();
					$first_friend = $logged_user->getUserFriends(1);

					if ( isset($_GET["id_friend"]) ) {
						$id_friend = $_GET["id_friend"];
					}
					else { // Afficher la conversation par défaut
						while ($first = $first_friend->fetch(PDO::FETCH_OBJ)) {
							$id_friend = $first->id_friend;
						}
					}
					include('inc/friend.php');
				?>
			</div>

			<div id="account-container" class="layout-container hidden">
				<?php include('inc/account.php'); ?>
			</div>

			<div id="design-container" class="layout-container hidden">
				<?php include('inc/affichage.php'); ?>
			</div>
		</aside>

		<section class="col-m-9 col-l-9">
			<?php
				// Lancer la requête pour afficher les données du destinataire
				$logged_user->getReceiverProfile($id_friend);
				// Variables issues de cette requête
				$id_receiver = $logged_user->getReceiverID();
				$receiver_firstname = $logged_user->getReceiverFirstname();
				$receiver_pseudo = $logged_user->getReceiverPseudo();
				$receiver_status_css = $logged_user->getReceiverCSS();
			?>
			<header>
				<h2 data-idreceiverstatus="<?php echo $logged_user->getReceiverIDStatus(); ?>"><span class="user-<?php echo $id_receiver; ?> user-name"><?php echo $receiver_firstname; ?></span> | ALIAS <span class="user-<?php echo $id_receiver; ?> user-pseudo"><?php echo $receiver_pseudo; ?></span>
					<div class="cercle <?php echo $receiver_status_css; ?>"></div>
				</h2>
				<a href="app/logout.php">Déconnexion</a>
				<input type="hidden" name="user_id" value="<?php echo $id_user; ?>"/>
				<input type="hidden" name="recepteur_id" value="<?php echo $id_receiver; ?>"/>
				<input type="hidden" name="scheme_id" value="<?php echo $user_designID; ?>"/>
				<input type="hidden" name="time" value="<?php echo $time; ?>"/>
			</header>
			<ul id="chat-box">
				<?php
					// Lancer la requête pour afficher la conversation
					$user_conversation = $logged_user->getConversationWith($id_friend);
					// $user_conversation = $logged_user->getUserConversation();
					foreach ($user_conversation->fetchAll(PDO::FETCH_OBJ) as $msg): ?>
				<li>
					<?php

					$css_class = "user-pseudo";
					if ( $msg->id_user == $id_user ) {
						$css_class = $css_class." user-".$id_user;
					}
					else {
						$css_class = $css_class." user-".$id_receiver;
					}
					$day = $msg->day;
					$hour = $msg->hour;
					if ( $day == date("d/m") ) {
						$day = "Aujourd'hui";
					}

					?>
					<h4 class="<?php echo $css_class; ?>"><?php echo $msg->pseudo_emetteur; ?></h4>
					<time class="msg-date"><?php echo $day; ?> à <?php echo $hour; ?></time>
					<p class="user-message"><?php echo $msg->user_message; ?></p>
				</li>
			<?php endforeach; ?>
			</ul>
			<div id="send-message">
				<label for="message">Répondre à <span class="user-<?php echo $id_receiver; ?> user-pseudo"><?php echo $receiver_pseudo; ?></span>. Appuyer sur la touche 'Entrer' pour envoyer votre message.</label>
				<textarea data-id-usermsg="<?php echo $id_user; ?>" id="message" placeholder="Votre message" name="message" value=""></textarea>
				<input type="hidden" id="receiver-send-a-message" name="user-<?php echo $id_receiver; ?>-send-a-message" value="false"/>
				<input type="hidden" id="user-send-a-message" name="user-<?php echo $id_user; ?>-send-a-message" value="false"/>
			</div>
		</section>
	</main>
	<footer>
		<?php include 'inc/footer.php'; ?>
	</footer>
	<script src="js/jQuery_v3.3.1.js"></script>
	<script src="js/ajax.js"></script>
	<script src="js/main.js"></script>
</body>
</html>