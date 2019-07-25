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
	$(function($) {
	  $('#invite').on('submit', function(event) {
	    if ( validateForm() ) {
	      event.preventDefault();
	    }
	  });
	  
	  function validateForm() {
	    $(".text-error").remove();	    
	    
	    var reg = /^\w+([\.-]?\w+)*@(((([a-z0-9]{2,})|([a-z0-9][-][a-z0-9]+))[\.][a-z0-9])|([a-z0-9]+[-]?))+[a-z0-9]+\.([a-z]{2}|(com|net|org|edu|int|mil|gov|arpa|biz|aero|name|coop|info|pro|museum))$/i;
	    var el_e = $("#email");
	    var v_email = el_e.val()?false:true;

		if ( v_email ) {
			el_e.after('<span class="text-error for-email">Неправильно указан e-mail</span>');
			$(".for-email").css({top: el_e.position().top + el_e.outerHeight() + 2});
		} else if ( !reg.test( el_e.val() ) ) {
			v_email = true;
			el_e.after('<span class="text-error for-email">Неправильно указан e-mail</span>');
			$(".for-email").css({top: el_e.position().top + el_e.outerHeight() + 2});
		}
		$("#email").toggleClass('error', v_email );
	        
	    return ( v_email );
	  }
	   
	});
	 

	//  form create validation
	$(function($) {
	  $('#create').on('submit', function(event) {
	    if ( validateForm() ) {
	      event.preventDefault();
	    }
	  });
	  
	  function validateForm() {
	    $(".text-error").remove();
	     
	    // Проверка Название комнаты    
		var el_l = $("#field_room");
		if ( el_l.val().length < 2 ) {
			var v_room = true;
			el_l.after('<span class="text-error">Неправильно указанно название <span class="hint" data-name="Минимум 2 символа">подсказка</span></span>');
			$(".for-login").css({top: el_l.position().top + el_l.outerHeight() + 2});
			$("#field_room").parent().addClass('error');
		} else {
			$("#field_room").parent().removeClass('error');
		}

	    // Проверка Сфера деятельности    
		var el_l = $("#field_activity");
		if ( el_l.val().length < 2 ) {
			var v_activity = true;
			el_l.after('<span class="text-error">Неправильно указана сфера деятельности <span class="hint" data-name="Минимум 2 символа">подсказка</span></span>');
			$(".for-login").css({top: el_l.position().top + el_l.outerHeight() + 2});
			$("#field_activity").parent().addClass('error');
		} else {
			$("#field_activity").parent().removeClass('error');
		};
	     
        // Проверка Критерий целевого лида  
    	var el_l = $("#field_criteria");
    	if ( el_l.val().length < 2 ) {
    		var v_criteria = true;
    		el_l.after('<span class="text-error for_criteria">Неправильно заполненно описание <span class="hint" data-name="Минимум 2 символа">подсказка</span></span>');
    		$(".for-login").css({top: el_l.position().top + el_l.outerHeight() + 2});
    		$("#field_criteria").parent().addClass('error');
    	} else {
			$("#field_criteria").parent().removeClass('error');
		};

	    // Проверка Стоимость лида
		var el_l  = $("#field_price");
		if ($.isNumeric($(el_l).val()) == false) {
        	var v_price = true;
        	el_l.after('<span class="text-error">Неправильно указанна стоимость лида <span class="hint" data-name="описание ошибки">подсказка</span></span>');
        	$(".for-login").css({top: el_l.position().top + el_l.outerHeight() + 2});
            $("#field_price").parent().addClass('error');
        } else {
			$("#field_price").parent().removeClass('error');
		};

	  	
	    return ( v_room || v_activity || v_criteria || v_price );
	  }
	   
	});




});


