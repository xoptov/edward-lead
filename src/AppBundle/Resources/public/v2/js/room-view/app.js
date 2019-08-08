const vm = new Vue({
    el: '#app',
    data: {
        roomId: roomId,
        activated: roomEnabled,
        deactivationError: null,
        members: {
            webmasters: [],
            companies: []
        },
        leads: []
    },
    created: function() {
        this.$http.get('/api/v1/room/' + this.roomId + '/members').then(
            response => {
                this.members.webmasters = response.data.webmasters;
                this.members.companies = response.data.companies;
            }
        );
        this.$http.get('/api/v1/leads/' + this.roomId).then(
            response => {
                this.leads = response.data;
                setInterval(() => {
                    this.$http.get('/api/v1/leads/' + this.roomId).then(
                        response => {
                            this.leads = response.data;
                        }
                    );
                }, 5000);
            }
        );
    },
    computed: {
        webmastersCount: function() {
            return this.members.webmasters.length;
        },
        companiesCount: function() {
            return this.members.companies.length;
        },
        leadsCount: function() {
            return this.leads.length;
        }
    },
    methods: {
        leadViewUrl: function(lead) {
            return '/lead/' + lead.id;
        },
        dateFormat: function(value) {
            const createdAt = new Date(value);
            return createdAt.format('dd.mm.yyyy');
        },
        getLogotype: function(member) {
            if (member.user.logotype) {
                return member.user.logotype;
            }
            return '/bundles/app/v2/img/user.png';
        },
        onDeactivateClick: function() {
            this.$http.get('/api/v1/room/' + this.roomId + '/deactivate')
                .then(response => {
                    this.deactivationError = null;
                    this.activated = false;
                })
                .catch(response => this.deactivationError = response.data.error);
        },
        onRevokeMemberClick: function(member) {
            this.$http.delete('/api/v1/room/' + roomId + '/revoke/' + member.id)
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