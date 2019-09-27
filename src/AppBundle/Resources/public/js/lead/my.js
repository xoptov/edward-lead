$(function(){
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
    new Vue({
        el: '#my-leads',
        components: {
            'result-modal': resultModal
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
});