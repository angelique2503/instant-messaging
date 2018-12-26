<?php
	require 'app/login.php';
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
<body id="connexion">
		<header>
			<h1>Messagerie instantanée (version 0.1)</h1>
			<h2>Connexion</h2>
		</header>
		<section>
		<form action="index.php" method="post" class="bounceIn">
			<label for="email">Email*</label>
			<input type="text" id="email" name="email"/>
			<label for="password">Mot de passe*</label>
			<input type="password" id="password" name="password"/>
			<?php 
				if ( isset($feedback) ) {
					echo '<strong>'.$feedback.'</strong>';
				}
			?>
			<input type="submit" value="Connexion"/>

		</form>
		</section>
		<footer>
			<?php include 'inc/footer.php'; ?>
		</footer>
</body>
</html>