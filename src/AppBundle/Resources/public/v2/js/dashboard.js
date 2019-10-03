$(function(){

    // Делает кликабельными строки в списке комнат.
    $('.room-reference').on('click', function(e){
        e.preventDefault();
        window.location.href = '/room/' + $(e.currentTarget).data('room');
    });

    // Делает кликабельными строки в списке лидов.
    $('.lead-reference').on('click', function(e){
        e.preventDefault();
        window.location.href = '/lead/' + $(e.currentTarget).data('lead');
    });
});
