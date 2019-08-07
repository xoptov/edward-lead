$(function() {
    $('#profile_phone').inputmask('(+7|8)(999)999-99-99');

    const $tokenField = $('#profile_token');
    const $tokenRenewButton = $('.js-renew-token');

    $tokenRenewButton.click(function(e) {
        e.preventDefault();
        $tokenRenewButton.attr('disabled', true);
        $tokenField.text('Новый ключ API генерируется');
        $.ajax({
            url: '/api/user/renew-token',
            processData: false,
            contentType: false,
            success: function(resp) {
                $tokenField.text(resp.token);
                $tokenRenewButton.attr('disabled', false);
            },
            error: function(xhr) {
                $tokenField.text(xhr.error);
                $tokenRenewButton.attr('disabled', false);
            }
        });
    });
});