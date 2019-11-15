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
            this.archiveConfirmShowed = true;
        },
        hideArchiveConfirm() {
            this.archiveConfirmShowed = false;
        },
        sendToArchive(leadId) {
            this.$http.get('/api/v1/lead/' + leadId + '/archive')
                .then(resp => {
                    window.location.href = '/leads/my';
                })
                .catch(resp => {
                    this.error = resp.data.error;
                });
        }
    }
});