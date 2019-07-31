$(document).ready(function() {

	// dropdown menu
	$(window).on('load resize', function() {
		if ($(window).width() <= 767) {
			$('.droparrow').click(function() {
				var $this = $(this);
				$this.find('.dropdownbox').toggleClass('active');
			});
		} else {
			$(".droparrow").on('mouseenter',function() {
				var $this = $(this);
				$this.find('.dropdownbox').fadeIn(200);
			}).on('mouseleave',function() {
				var $this = $(this);
				$this.find('.dropdownbox').hide();
			});
		}
	});

	//  header top menu
	$('.main_nav .nav_title').click(function() {
		$(this).find('.sub').toggleClass('active');
		$(this).parent().find('.nav_sub').slideToggle(100);
	});

	//  form invite validation
	const $form = $('form#invite');
	const $inputEmail = $form.find('input#email');
	const $inputToken = $form.find('input#token');
	const $button = $form.find('button[type=submit]');
	
	$form.on('submit', function(event) {
		event.preventDefault();

        let data = JSON.stringify({
            form: {
                email: $inputEmail.val(),
                token: $inputToken.val()
            }
        });

		if (validateForm()) {
			$.ajax({
				method: 'POST',
				url: '/room/send/invite',
				contentType: 'application/json',
				dataType: 'json',
				data: data,
				beforeSend: function() {
					disableInputAndButton();
				},
				success: function(resp) {
					destroyError('.text-error');
					alert(resp.message);
				},
				error: function (xhr) {
					renderError($inputEmail, 'Ошибка отправки приглашения по Email');
					enableInputAndButton();
				}
			});
		}
	});

	function disableInputAndButton()
	{
		$inputEmail.attr('disabled', true);
        $button.attr('disabled', true);
	}

	function enableInputAndButton()
	{
        $inputEmail.attr('disabled', false);
        $button.attr('disabled', false);
	}

	function validateForm() {
		$(".text-error").remove();

		var pattern = /^\w+([\.-]?\w+)*@(((([a-z0-9]{2,})|([a-z0-9][-][a-z0-9]+))[\.][a-z0-9])|([a-z0-9]+[-]?))+[a-z0-9]+\.([a-z]{2}|(com|net|org|edu|int|mil|gov|arpa|biz|aero|name|coop|info|pro|museum))$/i;
		var emailValue = !!$inputEmail.val();

		if (!emailValue || !pattern.test($inputEmail.val()))  {
			renderError($inputEmail, 'Неправильно указан e-mail');
			return false;
		}

		$inputEmail.toggleClass('error', emailValue);
		return emailValue;
	}

	function renderError($el, message) {
		let $errorEl = $form.find('.text-error');
		if (!$errorEl.length) {
			$errorEl = $('<span class="text-error for-email"></span>');
            $errorEl.css({top: $el.position().top + $el.outerHeight() + 2});
			$el.after($errorEl);
		}
		$errorEl.text(message);
	}

	function destroyError(selector) {
		let $errorEl = $form.find(selector);
		if ($errorEl) {
			$errorEl.remove();
		}
	}
});


