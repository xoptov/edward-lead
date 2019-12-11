const offersList = new Vue({
    el: '#offers-list',
    data: {
        sended: [],
        success: [],
        error: [],
        requestStatus: null
    },
    computed: {
        isRequestSuccess() {
            return this.requestStatus === 'success';
        },
        isRequestError() {
            return this.requestStatus === 'error';
        },
        isRequestSended() {
            return this.requestStatus !== null;
        }
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
        setRequestStatus(status) {
            this.requestStatus = status;
        }
    }
});

const modals = new Vue({
    el: '#modals',
    data: {
        publicationOffer: false,
        needOffer: false,
        requestResult: false,
        requestPending: false,
        requestStatus: null
    },
    computed: {
        isPublicationOfferShow() {
            return this.publicationOffer;
        },
        isNeedOfferShow() {
            return this.needOffer;
        },
        isRequestResultShow() {
            return this.requestResult;
        },
        isRequestSuccess() {
            return this.requestStatus === 'success';
        },
        isRequestError() {
            return this.requestStatus === 'error';
        }
    },
    methods: {
        openModal(name)
        {
            if (name in this)
                this[name] = true;
        },
        closeModal(name)
        {
            if (name in this)
                this[name] = false;
        },
        closeAll() {
            this.closeModal('publicationOffer');
            this.closeModal('needOffer');
        },
        sendRequest() {
            if (this.requestPending) {
                return false;
            }

            this.$http.get('/api/v1/offer/create')
                .then(() => this._handleResult('success'))
                .catch(() => this._handleResult('error'));

            this.requestPending = true;
        },
        _handleResult(status) {
            this.closeAll();
            this.openModal('requestResult');
            this.requestPending = false;
            this.requestStatus = status;
            this.$emit('request-' + status);
        }
    }
});

offersList.$on('publication-offer', () => modals.openModal('publicationOffer'));
offersList.$on('need-offer', () => modals.openModal('needOffer'));

modals.$on('request-success', () => offersList.setRequestStatus('success'))
modals.$on('request-error', () => offersList.setRequestStatus('error'))
