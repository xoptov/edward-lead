new Vue({
    el: '#lead-description',
    data: {
        requestCallResult: null
    },
    methods: {
        requestCall(tradeId, e) {
            e.target.disabled = true;
            jQuery.ajax({
                url: '/api/v1/telephony/call/' + tradeId,
                method: 'GET',
                context: this,
                success: function() {
                    this.$data.requestCallResult = 'success';
                },
                error: function(xhr) {
                    if (xhr.responseJSON && 'message' in xhr.responseJSON) {
                        this.$data.requestCallResult = xhr.responseJSON['message'];
                    } else {
                        this.$data.requestCallResult = 'Произошла ошибка запроса соединения с лидом';
                    }
                }
            });
        }
    }
});
