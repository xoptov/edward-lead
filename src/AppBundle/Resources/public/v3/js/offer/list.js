const offersList = new Vue({
    el: '#offers-list',
    methods: {
        onPublicOfferClick() {
            this.$emit('public-offer-clicked');
        },
        onNeedAdvertisersClick() {
            this.$emit('need-advertisers-clicked');
        }
    }
});

const modals = new Vue({
    el: '#modals',
    data: {
        publicOfferModal: false,
        needAdvertisersModal: false,
        requestResultModal: false,
        requestPending: false
    },
    computed: {
        isPublicOfferModalShow() {
            return this.publicOfferModal;
        },
        isNeedAdvertisersModalShow() {
            return this.needAdvertisersModal;
        },
        isRequestResultModalShow() {
            return this.requestResultModal;
        }
    },
    methods: {
        onPublicOfferClick(event) {
            this.closeAllModal();
            this.publicOfferModal = true;
        },
        onNeedAdvertisersClick() {
            this.closeAllModal();
            this.needAdvertisersModal = true;
        },
        onSendRequestClick() {
            if (this.requestPending) {
                return false;
            }
            // todo: Тут собственно необходимо сделать отправку запроса на бэкенд,
            //       предварительно заблокировав кнопку отправки от повторного нажатия.
            this.closeAllModal();
            this.requestResultModal = true;
        },
        onJoinToRoomClick(room) {
            return false;
        },
        closeAllModal() {
            this.publicOfferModal = false;
            this.needAdvertisersModal = false;
            this.requestResultModal = false;
        },
        onCancelClick(modal) {
            if (modal + 'Modal' in this) {
                this[modal + 'Modal'] = false;
            }
        }
    }
});

offersList.$on('public-offer-clicked', modals.onPublicOfferClick);
offersList.$on('need-advertisers-clicked', modals.onNeedAdvertisersClick);