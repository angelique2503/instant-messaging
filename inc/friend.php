
	<h2>Amis</h2>
			<!--<h3>Rechercher</h3>
			<label for="search-box">Rechercher des amis</label>
			<input id="search-box" type="search" name="search" value=""/>-->

			<ul id="friend-list">
				<?php

				foreach ($friends->fetchAll(PDO::FETCH_OBJ) as $friend) {
					echo '<li data-idfriend="'.$friend->id_friend.'"><a href="interface.php?id_friend='.$friend->id_friend.'" title="Afficher la conversation avec '.$friend->friend_pseudo.'">'.$friend->friend_pseudo.'</a><div class="cercle '.$friend->css_status.'" title="'.$friend->friend_status.'"></div></li>';
				}

				?>
			</ul>
