new Vue({
    el: '#lead-description',
    data: {
        archiveConfirmShowed: false,
        error: null
    },
    computed: {
        isError() {
            return !!this.error;
        }
    },
    methods: {
        showArchiveConfirm() {
            this.$model.archiveConfirmShowed = true;
        },
        hideArchiveConfirm() {
            this.$model.archiveConfirmShowed = false;
        },
        sendToArchive(leadId) {
            this.$http.get('/api/v1/lead/' + leadId + '/archive')
                .then(response => {
                    this.$model.error = null;
                    window.location.href = '/leads/my';
                })
                .catch(response => {
                    if (response.data.length) {
                        this.error = response.data[0];
                    } else {
                        this.error = 'Ошибка отправки лида в архив';
                    }
                });
        }
    }
});