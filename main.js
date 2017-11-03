$(function()
{
	$('.item-drop-menu').click(function(e) {
		e.preventDefault();
		e.stopPropagation();

		var menu = $('#'+$(this).attr('data-menu-parent'));

		menu.slideToggle({duration: 150});
	});

	$('.dropdown-menu li a').click(function(e) {
		e.preventDefault();
		e.stopPropagation();

		var value = $(this).attr('data-value');
		var text = $(this).text();

		var menu = $(this).attr('data-menu');

		var menu_parent = $('a.item-drop-menu[data-menu-parent="'+menu+'"]');
		
		$(menu_parent).text(text).attr('data-value',value);
		$('#id_'+menu).val(value);
		$('.dropdown-menu').slideUp({duration: 150});
	});

	$('#frm-save-block').submit(function(e) {

		e.preventDefault();
		e.stopPropagation();

		var form = new FormData(this);

		$.ajax({
			url: 'carregar_infos.php?info=save_block',
			type: 'POST',
			dataType: 'json',
			data: form,
			processData: false,
			contentType: false,
			cache: false,
		})
		.done(function(data)
		{
			var obj,
			json = JSON.stringify(data);

			if(obj = $.parseJSON(json))
			{
				$('#snack-message').hide();
				$('#snack-message').show();
				$('#message').html(obj[0].mesg);
				setTimeout(function() { $('#snack-message').hide(); },3000);
			}
		})
		.fail(function() {
			alert('Error');
		});
	});

	$('button[data-action="delete-block"]').click(function(e)
	{
		e.preventDefault();
		e.stopPropagation();

		var id = $(this).attr('data-block-id');

		$.ajax({
			url: 'carregar_infos.php?info=delete_block',
			type: 'POST',
			dataType: 'json',
			data: {id_block: id},
		})
		.done(function(data)
		{
			var obj,
			json = JSON.stringify(data);

			if(obj = $.parseJSON(json))
			{
				if(obj[0].mesg)
				{
					$('#snack-message').hide();
					$('#snack-message').show();
					$('#message').html(obj[0].mesg);
					setTimeout(function() { $('#snack-message').hide(); },3000);
				}
			}
		})
		.fail(function() {
			alert('Error');
		});
	});

	$('.close-modal').click(function() {
		$('.modal').fadeOut('fast');
	});

	$('#add-classification').click(function(e) {
		e.preventDefault();
		e.stopPropagation();

		$('.classificacoes').append('<div class="item"><input type="text" name="classificacao[]" placeholder="Nome classificação" class="classification" required><input type="color" name="cor_tema[]" placeholder="Cor" class="input-color" required></div>');
	});

	$('#frm-create-theme').submit(function(e) {
		e.preventDefault();
		e.stopPropagation();

		var form = new FormData(this);

		$.ajax({
			url: 'carregar_infos.php?info=create_theme',
			type: 'POST',
			dataType: 'json',
			data: form,
			cache: false,
			contentType: false,
			processData: false,
		})
		.done(function(data)
		{
			var obj,
			json = JSON.stringify(data);

			if(obj = $.parseJSON(json))
			{
				if(obj[0].mesg) {
					$('#snack-message').hide();
					$('#snack-message').show();
					$('#message').html(obj[0].mesg);
					setTimeout(function() { $('#snack-message').hide(); },3000);
				}
			}
		})
		.fail(function() {
			alert('Error');
		});
	});

	$('#add-theme').click(function() {
		$('#modal-create-theme').fadeIn('fast');
	});

	$('.description-value').click(function() {
		$(this).children('.dropdown-value').fadeToggle('fast');
	});

	$('.description-value .dropdown-value a').click(function(e) {
		e.preventDefault();
		e.stopPropagation();

		var quadra = $(this).attr('data-block-id'),
		campo = $(this).attr('data-item-session'),
		valor = $(this).attr('data-item-value');

		$.ajax({
			url: 'carregar_infos.php?info=edit_block',
			type: 'POST',
			dataType: 'json',
			data: {quadra: quadra, campo: campo, valor: valor},
		})
		.done(function(data) {
			var obj,
			json = JSON.stringify(data);

			if(obj = $.parseJSON(json))
			{
				if(obj[0].status == 200) {
					$('.dropdown-value').fadeOut('fast');

					$('p[id="'+quadra+'-'+campo+'"]').text(valor);
				}
				else if(obj[0].status == 107) {
					$('#snack-message').hide();
					$('#snack-message').show();
					$('#message').html(obj[0].mesg);
					setTimeout(function() { $('#snack-message').hide(); },3000);
				}
			}
		})
		.fail(function() {
			alert('Error');
		});
		
	});
});