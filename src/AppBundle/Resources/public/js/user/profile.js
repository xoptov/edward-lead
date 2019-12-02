$(function() {

    const btnList = document.querySelectorAll('.profile-tabs__control');
    const contentList = document.querySelectorAll('.profile-tabs__content');

    for (let i = 0; i < btnList.length; i++) {
        btnList[i].addEventListener('click', function () {
            contentList.forEach(element => element.classList.remove('selected'));
            btnList.forEach(element => element.classList.remove('selected'));
            contentList[i].classList.add('selected');
            btnList[i].classList.add('selected');
        })
    }

    $('#profile_phone').inputmask('(+7|8)(999)999-99-99');

    const $tokenField = $('#profile_token');
    const $tokenRenewButton = $('.js-renew-token');

    $tokenRenewButton.click(function(e) {
        e.preventDefault();
        $tokenRenewButton.attr('disabled', true);
        $tokenField.text('Новый ключ API генерируется');
        $.ajax({
            url: '/api/v1/user/renew-token',
            processData: false,
            contentType: false,
            success: function(resp) {
                $tokenField.text(resp.token);
                $tokenRenewButton.attr('disabled', false);
            },
            error: function(xhr) {
                console.log(xhr);
                $tokenRenewButton.attr('disabled', false);
            }
        });
    });
});