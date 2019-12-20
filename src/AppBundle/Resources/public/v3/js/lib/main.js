$(function() {

    // Show menu
    $('.navbar-toggle').click(function() {
        $(this).toggleClass('active');
        $('.nav').toggleClass('nav_open');
    });

    $(document).click(function(event) {
        if ($(event.target).closest('.navbar-toggle').length
            || $(event.target).closest('.nav').length ) return;
        $('.nav').removeClass('nav_open');
        $('.navbar-toggle').removeClass('active');
        event.stopPropagation();
    });

    // Navdrop
    $('.nav__drop').click(function(e) {
        $(this.parent).toggleClass('nav__drop_open');
        $('.nav__subnav').slideToggle( "slow", function() {});
    });
});