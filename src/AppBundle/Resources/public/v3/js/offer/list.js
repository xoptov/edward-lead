const offersList = new Vue({
    el: '#offers-list',
    data: {
        sended: [],
        success: [],
        error: []
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
        }
    }
});

const modals = new Vue({
    el: '#modals',
    data: {
        publicationOffer: false,
        needOffer: false,
        requestResult: false
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
            //todo: вобщем остановился тут на реализации интерактива для офферов.
            this.closeAll();
            this.openModal('requestResult');
        }
    }
});

offersList.$on('publication-offer', () => modals.openModal('publicationOffer'));
offersList.$on('need-offer', () => modals.openModal('needOffer'));
