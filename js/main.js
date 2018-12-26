$(document).ready(function() {

	$(window).resize(function(){
		detect_chatBox_height();
	});

	function detect_chatBox_height() {
		var viewport_height = $(window).height(),
			// Textarea et label
			div_height = $('#send-message').outerHeight(true),
			// Le reste
			chat_mheight = $('#chat-box').css('margin-top').substr(0,2),
			padding_height = $('section').css('padding-top').substr(0,2)*2,
			li_mheight = $('#chat-box li:first-child').css('margin-top').substr(0,2)*2,
			footer_height = $('footer').outerHeight(true),
			header_height = $('header').outerHeight(true),
			elts_height = Number(div_height)+Number(footer_height)+Number(padding_height)+Number(header_height)+Number(chat_mheight)+Number(li_mheight);
		$("#chat-box").css('max-height',viewport_height-elts_height);
		$("#chat-box").css('min-height',viewport_height-elts_height);
	}

	function show_page() {
		$('[data-menu]').each(function() {
			$(this).removeClass('active');
		});
		$(this).addClass('active');
		var id_container = $(this).attr('data-menu')+'-container';
		
		/*$(".layout-container").each(function() {
			if ( $(this).attr('id') == id_container ) {
			}
		});*/
		if ( $(this).data('menu') == 'friend-list' ) {
			show_hide($('#friend-list-container'),$('.layout-container:not(#friend-list-container)'));
		}
		else if ( $(this).data('menu') == 'account' ) {
			show_hide($('#account-container'),$('.layout-container:not(#account-container)'));
		}
		else if ( $(this).data('menu') == 'design' ) {
			show_hide($('#design-container'),$('.layout-container:not(#design-container)'));
		}
	}

	function show_hide(show,hide) {
		show.removeClass('hidden');
		show.addClass('show bounceIn');
		hide.removeClass('show');
		hide.addClass('hidden');
	}

	detect_chatBox_height();
	$('[data-menu]').click(show_page);

});