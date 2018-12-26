	<h2>Design</h2>

	<ul id="design-list">
		<li><label for="color-choice">Thème</label> 
			<div class="select-wrapper">
			<select id="color-choice" name="color-choice">
				<?php 
					$colorScheme = $bdd->query('SELECT * FROM color_scheme ORDER BY color_name ASC;');
					foreach ($colorScheme->fetchAll(PDO::FETCH_OBJ) as $color):
				?>
				<?php 
					$checked = '';
					if ($color->color_name == 'Vert') {
						$checked = 'selected="selected"';
					}
				?>
				<option <?php echo $checked;?> value="<?php echo $color->id_color_scheme; ?>" data-colorname="<?php echo $color->data_name; ?>"><?php echo $color->color_name; ?></option>
				<?php endforeach; ?>
			</select>
			</div>
		</li>
	</ul>
