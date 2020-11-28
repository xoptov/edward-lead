const CompanyComponent = {

};

const PersonalComponent = {

};

const advertiserProfile = new Vue({
    el: '#advertiser-profile',
    components: {
        'company-component': CompanyComponent,
        'personal-component': PersonalComponent
    },
    data: {
        user: null
    },
    mounted() {
        const userId = this.$el.dataset.userId;
        this.$http.get('/api/v1/user/me?deep=1')
            .then(
                resp => resp.json().then(data => this.user = data)
            );
    }
});