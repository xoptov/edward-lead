const vm = new Vue({
    el: '#app',
    data: {
        roomId: roomId,
        activated: roomEnabled,
        deactivationError: null,
        members: {
            webmasters: [],
            companies: []
        }
    },
    created: function() {
        this.$http.get('/room/' + this.roomId + '/members').then(
            response => {
                this.members.webmasters = response.data.webmasters;
                this.members.companies = response.data.companies;
            }
        );
    },
    computed: {
        webmastersCount: function() {
            return this.members.webmasters.length;
        },
        companiesCount: function() {
            return this.members.companies.length;
        }
    },
    methods: {
        getLogotype: function(member) {
            if (member.user.logotype) {
                return member.user.logotype;
            }
            return '/bundles/app/v2/img/user.png';
        },
        onDeactivateClick: function() {
            this.$http.get('/room/' + this.roomId + '/deactivate')
                .then(response => {
                    this.deactivationError = null;
                    this.activated = false;
                })
                .catch(response => this.deactivationError = response.data.error);
        },
        onRevokeMemberClick: function(member) {
            this.$http.get('/room/' + roomId + '/revoke/' + member.id)
                .then(response => {
                    for (let i = 0; i < this.members.companies.length; i++) {
                        if (this.members.companies[i].id === member.id) {
                            this.members.companies.splice(i, 1);
                            return true;
                        }
                    }
                    for (let i = 0; i < this.members.webmasters.length; i++) {
                        if (this.members.webmasters[i].id === member.id) {
                            this.members.webmasters.splice(i, 1);
                            return true;
                        }
                    }
                    return false;
                });
        }
    }
});