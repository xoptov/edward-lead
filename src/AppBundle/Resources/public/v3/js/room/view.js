const RoomLeads = {
    props: {
        roomId: {
            type: Number,
            required: true
        }
    },
    data() {
        return {
            leads: []
        };
    },
    computed: {
        leadsCount: function() {
            return this.leads.length;
        }
    },
    created() {
        this.$http.get('/api/v1/leads/' + this.roomId).then(
            response => {
                this.leads = response.data;
                setInterval(() => {
                    this.$http.get('/api/v1/leads/' + this.roomId).then(
                        response => this.leads = response.data
                    );
                }, 10000);
            }
        );
    },
    methods: {
        leadViewUrl: function(lead) {
            return '/lead/' + lead.id;
        },
        dateFormat: function(value) {
            const createdAt = new Date(value);
            return createdAt.format('dd.mm.yyyy HH:MM:s');
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
        viewLead(id) {
            window.location.href = '/lead/' + id;
            return false;
        },
        splitString(str, splitter){
            return str.split(splitter);
        }
    }
};

const RoomMembers = {
    props: {
        roomId: {
            type: Number,
            required: true
        }
    },
    data() {
        return {
            members: {
                webmasters: [],
                companies: []
            }
        }
    },
    created() {
        this.$http.get('/api/v1/room/' + this.roomId + '/members').then(
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
            return '/bundles/app/v2/img/icon_2.png';
        },
        revokeMember(member) {
            this.$emit('revoke-member', member);
        }
    }
};

new Vue({
    el: '#app',
    components: {
        'room-members': RoomMembers,
        'room-leads': RoomLeads
    },
    data: {
        roomId: roomId,
        activated: roomEnabled,
        deactivationError: null,
        revokeMemberModal: false,
        deactivateModal: false
    },
    methods: {
        deactivate() {
            this.$http.get('/api/v1/room/' + this.roomId + '/deactivate')
                .then(response => {
                    this.deactivationError = null;
                    this.activated = false;
                })
                .catch(response => this.deactivationError = response.data[0]);
        },
        revokeMember(data) {
            console.log(data);
        }
    }
});