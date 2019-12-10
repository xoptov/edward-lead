const offersList = new Vue({
    el: '#offers-list',
    data: {
        sended: [],
        success: [],
        error: []
    },
    methods: {
        onConnectRequestClick(roomId) {
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
        onJoinClick(roomId) {
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
            return this.success.indexOf(roomId) !== -1;
        },
        isError(roomId) {
            return this.error.indexOf(roomId) !== -1;
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
        onOpenPublicationOfferModalClick() {
            this.publicationOffer = true;
        },
        onClosePublicationOfferModalClick() {
            this.publicationOffer = false;
        },
        onOpenNeedOfferModalClick() {
            this.needOffer = true;
        },
        onCloseNeedOfferModalClick() {
            this.needOffer = false;
        },
        onSendRequestClick() {
            //todo: вобщем остановился тут на реализации интерактива для офферов.
            this.closeAll();
            this.requestResult = true;
        },
        onCloseRequestResultModalClick() {
            this.requestResult = false;
        },
        closeAll() {
            this.publicationOffer = false;
            this.needOffer = false;
        }
    }
});

offersList.$on('publication-offer', modals.onOpenPublicationOfferModalClick);
offersList.$on('need-offer', modals.onOpenNeedOfferModalClick);
