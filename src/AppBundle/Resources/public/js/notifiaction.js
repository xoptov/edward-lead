////////////// TOGGLE POPUP WITH NOTIFICATIONS ///////////////

var popup = $('.notifications__popup')

$(".notifications__menu").click(function (e) {
    e.preventDefault();
    popup.toggle()
    e.stopPropagation();
})

$(document).on("click", function () {
    popup.hide();
});

popup.on("click", function (event) {
    event.stopPropagation();
});

////////////// TOGGLE POPUP WITH NOTIFICATIONS ///////////////

////////////// API - READ/UNREAD ///////////////

$('.notifications__popup__item_read').click(function () {

    var notificationId = $(this).data('id');
    hideRead(notificationId)
    removeRedBorder(notificationId)
    showPreloader(notificationId)

    $.ajax({
        type: 'PATCH',
        url: '/api/v1/notifications/read/' + notificationId,
        processData: false,
        contentType: 'application/merge-patch+json',

        success: function (response) {
            hidePreloader(notificationId)
            hideRead(notificationId)
            showUnread(notificationId)
            degressNotificationCount()

        },
        error: function (error) {
            hidePreloader(notificationId)
            showRead(notificationId)
            addRedBorder(notificationId)
        }
    });

})

$('.notifications__popup__item_unread').click(function () {

    var notificationId = $(this).data('id');
    hideUnread(notificationId)
    addRedBorder(notificationId)
    showPreloader(notificationId)

    $.ajax({
        type: 'PATCH',
        url: '/api/v1/notifications/unread/' + notificationId,
        processData: false,
        contentType: 'application/merge-patch+json',

        success: function (response) {
            hidePreloader(notificationId)
            hideUnread(notificationId)
            showRead(notificationId)
            increaseNotificationCount()

        },
        error: function (error) {
            hidePreloader(notificationId)
            showUnread(notificationId)
            removeRedBorder(notificationId)
        }
    });

})

function hidePreloader(id){
    $('.notifications__popup__item_preloader[data-id="' + id + '"]').hide()
}

function showPreloader(id){
    $('.notifications__popup__item_preloader[data-id="' + id + '"]').show()
}

function hideRead(id){
    $('.notifications__popup__item_read[data-id="' + id + '"]').hide()
}

function showRead(id){
    $('.notifications__popup__item_read[data-id="' + id + '"]').show()
}

function hideUnread(id){
    $('.notifications__popup__item_unread[data-id="' + id + '"]').hide()
}

function showUnread(id){
    $('.notifications__popup__item_unread[data-id="' + id + '"]').show()
}

function addRedBorder(id) {
    $('.notifications__popup__item[data-id="' + id + '"]').addClass('red-border')
}
function removeRedBorder(id) {
    $('.notifications__popup__item[data-id="' + id + '"]').removeClass('red-border')
}

function degressNotificationCount(){

    var count = $('.notifications__menu__new_icon').text()

    $('.notifications__menu__new_icon').text(parseInt(count) - 1)

}
function increaseNotificationCount(){

    var count = $('.notifications__menu__new_icon').text()

    $('.notifications__menu__new_icon').text(parseInt(count) + 1)
}


////////////// API - READ/UNREAD ///////////////