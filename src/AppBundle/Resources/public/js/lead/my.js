const resultModal = {
    template: '#result-modal',
    props: ['trade'],
    computed: {
        acceptUrl: function(){
            if (this.trade) {
                return '/trade/accept/' + this.trade.id;
            }
            return null;
        },
        rejectUrl: function(){
            if (this.trade) {
                return '/trade/reject/' + this.trade.id;
            }
            return null;
        },
        leadName: function() {
            if (this.trade) {
                return this.trade.lead.name;
            }
            return null;
        },
        leadPhone: function() {
            if (this.trade) {
                return this.trade.lead.phone;
            }
            return null;
        }
    },
    methods: {
        close: function() {
            this.$emit('close-modal');
        }
    }
};

const makeCallButton = {
    template: '<button class="phone_link" @click.prevent="requestPhoneCall" v-if="!isCallRequested"></button><span class="phone_connect" v-else></span>',
    props: ['trade'],
    data: function() {
        return {
            callRequestStatus: null
        };
    },
    computed: {
        isCallRequestSuccess: function() {
            return 'success' === this.callRequestStatus;
        },
        isCallRequested: function () {
            return !!this.callRequestStatus;
        }
    },
    methods: {
        requestPhoneCall: function() {
            this.callRequestStatus = 'requested';
            this.$http.get('/api/v1/telephony/call/' + this.trade.id)
                .then(() => {
                    this.callRequestStatus = 'success';
                })
                .catch(() => {
                    this.callRequestStatus = 'fail';
                });
        }
    }
};

new Vue({
    el: '#my-leads',
    components: {
        'result-modal': resultModal,
        'make-call-button': makeCallButton
    },
    data: {
        trade: null
    },
    methods: {
        openResultModal: function(data) {
            this.trade = data;
        },
        closeResultModal: function() {
            this.trade = null;
        }
    }
});
