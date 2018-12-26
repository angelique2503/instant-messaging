$(document).ready(function() {

	var my_msg = $('textarea').val(),
		my_user_id = $('[name="user_id"]').val(),
		my_design_id = $('[name="scheme_id"]').val(),
		the_user_id = $('[name="recepteur_id"]').val(),
		$textarea = $('textarea'),
		$box = $('#chat-box');

	$('select#color-choice').find('option[value="'+my_design_id+'"]');

	// Mettre à jour la valeur en temps réel
	$textarea.keyup(function() {
		my_msg = $(this).val();
	});

	// Insérer le message en bdd puis afficher le dernier message dans la chat-box
	function send_message(message) {

		message.keypress(function(e) {
		
			if (e.which == 13) { // Si la touche ENTER est pressée

				e.preventDefault(); // Annuler la saisie d'un retour chariot

				if ( my_msg.trim() == '' ) { // Si le message est vide, ne rien envoyer
					alert('Merci d\'écrire un message avant de l\'envoyer');
				}

				else {
					var textarea_value = $(this).val('');
					// Insérer le message dans la bdd
					$.post("app/ajax.php", { msg: my_msg, user_id: my_user_id, recepteur_id: the_user_id }, function(data, status) {
						// Si la requête a fonctionné
						if (status === "success") {
							// Faire apparaître le dernier message envoyé dans la chat-box
							$.post("app/ajax.php", { show_last_message: 'OK', user_id: my_user_id, recepteur_id: the_user_id }, function(data, status) {
								
								if (status === "success") {
									var obj = JSON.parse(data);
									scroll_down_chatbox();
									build_html(obj, $box, my_user_id);
								}

							});
						}
					});
				}
			}
		});
	}

	// Construire le html avec les données envoyées par PHP
	function build_html(obj, html_elt, id_user, iterator = true) {

		if ( iterator ) {
			for (var i in obj) {
				var pseudo = obj[i].pseudo_emetteur,
					message = obj[i].user_message,
					hour = obj[i].hour,
					day = obj[i].day;		
			}
		}
		else {
			var pseudo = obj.pseudo,
				message = obj.receiver_message,
				hour = obj.msg_hour,
				day = obj.msg_day;
		}

		var d = new Date();
			hour = d.getHours(),
   			minutes = d.getMinutes(),
			hour = add_zero(hour),
			minutes = add_zero(minutes),
			h_min = hour+':'+minutes;

		var str = '<h4 class="user-'+id_user+' user-pseudo">'+pseudo+'</h4>'+'<time class="msg-date">Aujourd\'hui à '+h_min+'</time>'+'<p class="user-message bounceIn">'+message+'</p>';
		html_elt.append('<li>'+str+'</li>');

	}

	function add_zero(i) {
    	if (i < 10) {
       		i = "0" + i;
    	}
    	return i;
	}

	// Mettre à jour les informations concernant l'utilisateur en temps réel
	function update_infosUser() {

		var value = $(this).parent().find('input').val(), // Prénom, pseudo, email...
			column_bddname = $(this).parent().find('input').attr('id'), // Convention : l'id de l'élément correspond au nom de la colonne de la table dans le bdd
			my_user_id = $('[name="user_id"]').val(),
			button = $(this);

		$.post("app/ajax.php", { column_name: column_bddname, user_value: value, user_id: my_user_id }, function(data, status) {
			if (status === "success") {

				$('.user-'+my_user_id).each(function() {

					var $this = $(this);

					if ( column_bddname == 'pseudo' ) {
						if ( $this.hasClass('user-pseudo') ) {
							$this.text(value);
						}
					}

					else if ( column_bddname == 'firstname' ) {
						if ( $this.hasClass('user-name') ) {
							$this.text(value);
						}
					}

				});

				$('#account-container').find('strong').remove();
				$('#account-container').append('<strong>'+button.parent().find('label').text()+' modifié avec succès !</strong>');

			}
		});

	}

	function update_statusUser() {
		var $checked = $(this).find('option:checked');
		if ( $checked.val() ) {

		}
		$.get("app/ajax.php", { user_id: my_user_id, id_status: $checked.val() }, function(data, status) {
		});
	}

	// Lorsque le destinataire modifie une information le concernant (pseudo/prénom/statut) ou envoie un message, le navigateur doit la mettre à jour en temps réel du côté de l'utilisateur
	function refresh() {

		var receiver_name = $('.user-'+the_user_id+'.user-name:first').text(),
			receiver_pseudo = $('.user-'+the_user_id+'.user-pseudo:first').text(),
			receiver_id_status = $('[data-idreceiverstatus]').data('idreceiverstatus'),
			user_time = $('[name="time"]').val();

			$.get("app/ajax.php", {
				user_time: user_time,
				id_user: my_user_id,
				id_receiver: the_user_id,
				receiver_id_status: receiver_id_status,
				receiver_name: receiver_name,
				receiver_pseudo: receiver_pseudo
			}, function(data, status) {
				
				var obj = JSON.parse(data);

					if ( obj.firstname != receiver_name ) { // Si le destinataire met à jour son prénom...
						$('.user-'+the_user_id+'.user-name').each(function() {
							// Mise à jour des éléments HTML de l'émetteur
							$(this).text(obj.firstname);
						});
					}

					if ( obj.pseudo != receiver_pseudo ) { // Si le destinataire met à jour son pseudo...
						$('.user-'+the_user_id+'.user-pseudo').each(function() {
							// Mise à jour des éléments HTML de l'émetteur
							$(this).text(obj.pseudo);
						});
					}

					if ( obj.id_status != receiver_id_status ) { // Si le destinataire met à jour son statut...
						var $cercle = $('[data-idreceiverstatus] .cercle');
						$('[data-idfriend]').each(function() {
							if ( $(this).data('idfriend') == the_user_id ) {
								var $elt = $(this).find('.cercle');
								remove_CSS_status($elt);
								$elt.addClass(obj.css_class);
							}
						});
						$cercle.each(function() {
							remove_CSS_status($cercle);
							$cercle.addClass(obj.css_class);
						});
					}

					if ( obj.receiver_send_a_message == true ) { // Si le destinataire envoie un message...
						var date = new Date(),
							year = date.getFullYear(),
							month = date.getMonth()+1,
							day = date.getDate(),
							hour = add_zero(date.getHours()),
							minutes = add_zero(date.getMinutes()),
							secondes = add_zero(date.getSeconds()),
							refresh_date = year+'-'+month+'-'+day+' '+hour+':'+minutes+':'+secondes;
						// Mettre à jour la date dans l'input caché
						$('[name="time"]').val(refresh_date);
						build_html(obj, $box, the_user_id, false);
						scroll_down_chatbox();
					}
					else {
						console.log('Aucun nouveau message');
					}

			});

	}

	function remove_CSS_status(elt) {
		elt.removeClass('inactive');
		elt.removeClass('busy');
		elt.removeClass('offline');
		elt.removeClass('online');
	}

	// Scroller en bas de la chat-box
	function scroll_down_chatbox() {
    	var n = $('#chat-box').prop("scrollHeight"); // Hauteur de l'élément avec scrollbar
    	$('#chat-box').animate({ scrollTop: n }, 25);
	}

	// Mettre à jour le thème
	function update_colorScheme() {

		var $color_checked = $(this).find('option:checked'),
			color_name = $color_checked.attr('data-colorname'),
			id_color = $color_checked.val();

		$.get("app/ajax.php", { id_user: my_user_id, id_color: id_color, color_name: color_name }, function(data, status) {
			var css = JSON.parse(data);
			$('head #update_scheme').remove();
			$('head').append('<style id="update_scheme" type="text/css">'+css+'</style>');
		});

	}

	function reload_page() {
		location.reload(true);
	}

	setInterval(refresh, 1000);
	setInterval(reload_page, 30000);
	send_message($textarea);
	scroll_down_chatbox();
	$('.account-infos button').click(update_infosUser);
	$('.account-infos select#status').change(update_statusUser);
	$('select#color-choice').click(update_colorScheme);

});