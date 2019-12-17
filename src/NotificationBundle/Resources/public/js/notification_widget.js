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

    $.ajax({
        type: 'GET',
        url: '/api/v1/notifications/read/' + notificationId,
        processData: false,
        contentType: 'application/merge-patch+json',

        success: function (response) {
            hideRead(notificationId)
            showUnread(notificationId)
            degreesNotificationCount()

        },
        error: function (error) {
            showRead(notificationId)
            addRedBorder(notificationId)
        }
    });

})

$('.notifications__popup__item_unread').click(function () {

    var notificationId = $(this).data('id');
    hideUnread(notificationId)
    addRedBorder(notificationId)

    $.ajax({
        type: 'GET',
        url: '/api/v1/notifications/unread/' + notificationId,
        processData: false,
        contentType: 'application/merge-patch+json',

        success: function (response) {
            hideUnread(notificationId)
            showRead(notificationId)
            increaseNotificationCount()

        },
        error: function (error) {
            showUnread(notificationId)
            removeRedBorder(notificationId)
        }
    });

})

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
    $('.notifications__popup__item[data-id="' + id + '"]').addClass('notice__item_red')
}
function removeRedBorder(id) {
    $('.notifications__popup__item[data-id="' + id + '"]').removeClass('notice__item_red')
}

function degreesNotificationCount(){

    var count = $('.notifications__menu__new_icon').text()

    $('.notifications__menu__new_icon').text(parseInt(count) - 1)

}
function increaseNotificationCount(){

    var count = $('.notifications__menu__new_icon').text()

    $('.notifications__menu__new_icon').text(parseInt(count) + 1)
}


////////////// API - READ/UNREAD ///////////////