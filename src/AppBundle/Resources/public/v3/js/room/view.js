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
                }, 10000);
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
            return createdAt.format('dd.mm.yyyy HH:MM:s');
        },
        getLogotype: function(member) {
            if (member.user.logotype) {
                return member.user.logotype;
            }
            return '/bundles/app/v2/img/icon_2.png';
        },
        getTimerOrCompanyLabel: function(lead) {
            return '';
        },
        getStatusObject: function(status) {
            const statusObj = {
                class: null,
                label: null,
                color: null
            };
            switch(status) {
                case 'expect':
                    statusObj.class = 'status__gray';
                    statusObj.label = 'Ожидает';
                    statusObj.color = 'gray';
                    break;
                case 'in_work':
                    statusObj.class = 'status__yellow';
                    statusObj.label = 'В работе';
                    statusObj.color = 'yellow';
                    break;
                case 'not_target':
                    statusObj.class = 'status__red';
                    statusObj.label = 'Не целевой';
                    statusObj.color = 'red';
                    break;
                case 'arbitration':
                    statusObj.class = 'status__pink';
                    statusObj.label = 'Арбитраж';
                    statusObj.color = 'pink';
                    break;
                case 'target':
                    statusObj.class = 'status__green';
                    statusObj.label = 'Целевой';
                    statusObj.color = 'green';
                    break;
                case 'archive':
                    statusObj.class = 'status__black';
                    statusObj.label = 'Архив';
                    statusObj.color = 'black';
                    break;
                default:
                    statusObj.class = 'status__gray';
                    statusObj.label = 'Не извесно';
                    statusObj.color = 'gray';
            }

            return statusObj;
        },
        onDeactivateClick: function() {
            this.$http.get('/api/v1/room/' + this.roomId + '/deactivate')
                .then(response => {
                    this.deactivationError = null;
                    this.activated = false;
                })
                .catch(response => this.deactivationError = response.data[0]);
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
        },
        onRowClick(id) {
            window.location.href = '/lead/' + id;
            return false;
        }
    }
});