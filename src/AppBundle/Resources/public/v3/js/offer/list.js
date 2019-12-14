const offersList = new Vue({
    el: '#offers-list',
    data: {
        sended: [],
        success: [],
        error: [],
        offerRequested: false,
        advertisersRequested: false
    },
    methods: {
        connectRequest(roomId) {
            if (this.isSended(roomId)) {
                return false;
            }
            this.$http.get('/api/v1/offer/' + roomId + '/connect-request')
                .then(() => {
                    this.success.push(roomId);
                })
                .catch(() => {
                    this.error.push(roomId);
                });
            this.sended.push(roomId);
        },
        joinToRoom(roomId) {
            if (this.isSended(roomId)) {
                return false;
            }
            this.$http.get('/api/v1/room/' + roomId + '/join')
                .then(() => {
                    this.success.push(roomId);
                    window.location.href = '/room/' + roomId;
                })
                .catch(() => {
                    this.error.push(roomId);
                });
            this.sended.push(roomId);
        },
        isSended(roomId) {
            return this.sended.indexOf(roomId) !== -1;
        },
        isSuccess(roomId) {
            return this.isSended(roomId) && this.success.indexOf(roomId) !== -1;
        },
        isError(roomId) {
            return this.isSended(roomId) && this.error.indexOf(roomId) !== -1;
        },
        onOfferRequested() {
            this.offerRequested = true;
        },
        onAdvertisersRequested() {
            this.advertisersRequested = true;
        }
    }
});

const advertisersRequestModal = new Vue({
    el: '#advertisers-request-modal',
    data: {
        visible: false,
        submitted: false
    },
    methods: {
        open() {
            this.visible = true;
        },
        close() {
            this.visible = false;
        },
        submit() {
            if (this.submitted) {
                return false;
            }
            this.$http.get('/api/v1/offer/create')
                .then(() => {
                    this.close();
                    this.$emit('advertisers-requested');
                })
                .catch(resp => {
                    //todo: Need show server error validations.
                    this.submitted = false;
                });
            this.submitted = true;
        }
    }
});

const offerRequestModal = new Vue({
    el: '#offer-request-modal',
    data: {
        visible: false,
        agreement: true,
        error: null,
        submitted: false
    },
    methods: {
        open() {
            this.visible = true;
        },
        close() {
            this.error = null;
            this.visible = false;
        },
        submit() {
            this.validate();
            if (this.submitted || this.error) {
                return false;
            }
            this.$http.get('/api/v1/offer/create')
                .then(() => {
                    this.close();
                    this.$emit('offer-requested');
                })
                .catch(resp => {
                    //todo: Need show server error validations.
                    this.submitted = false;
                });
            this.submitted = true;
        },
        validate() {
            if (!this.agreement) {
                this.error = 'Чтобы отправить запрос на добавление, прочитайте и согласитесь с правилами и критериями публичных офферов';
                return;
            }

            this.error = null;
        }
    }
});

const requestResultModal = new Vue({
    el: '#request-result-modal',
    data: {
        visible: false
    },
    methods: {
        open() {
            this.visible = true;
        },
        close() {
            this.visible = false;
        },

    }
});

offersList.$on('offer-request', () => offerRequestModal.open());
offersList.$on('advertisers-request', () => advertisersRequestModal.open());

offerRequestModal.$on('offer-requested', () => {
    offersList.onOfferRequested();
    requestResultModal.open();
})

advertisersRequestModal.$on('advertisers-requested', () => {
    offersList.onAdvertisersRequested();
    requestResultModal.open();
});