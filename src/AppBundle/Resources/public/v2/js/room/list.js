$(function(){
    $('.room-reference').on('click', function(e){
        e.preventDefault();
        window.location.href = '/room/' + $(e.currentTarget).data('room');
    });
});
