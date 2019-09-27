$(function(){
    $('.room-reference').on('click', function(e){
        e.preventDefault();
        window.location = '/room/' + $(e.currentTarget).data('room');
    });
});
