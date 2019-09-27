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
});


