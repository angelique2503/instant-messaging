	<h2>Compte</h2>

		<div class="account-infos">
			<label for="status">Statut</label>
			<select id="status" name="status">
				<option value="1" data-cssstatus="online">En ligne</option>
				<option value="2" data-cssstatus="inactive">Inactif</option>
				<option value="3" data-cssstatus="busy">Occupé</option>
				<option value="4" data-cssstatus="offline">Hors ligne</option>
			</select>
		</div>

		<div class="account-infos">
			<label for="firstname">Prénom</label>
			<input type="text" id="firstname" name="firstname" value="<?php echo $user_firstname; ?>"/>
			<button title="Modifier votre prénom">Enregistrer</button>
		</div>

		<div class="account-infos">
			<label for="pseudo">Pseudo</label>
			<input type="text" id="pseudo" name="pseudo" value="<?php echo $user_pseudo; ?>"/>
			<button title="Modifier votre pseudo">Enregistrer</button>
		</div>

		<div class="account-infos">
			<label for="email">Email</label>
			<input type="text" id="email" name="email" value="<?php echo $user_email; ?>"/>
			<button title="Modifier votre email">Enregistrer</button>
		</div>
