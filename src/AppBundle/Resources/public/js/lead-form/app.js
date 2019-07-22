Vue.use(window.vuelidate.default);

const phoneNumberValidator = function(value) {
    return /^(?:\+7|8)\(?9\d{2}\)?[\-\s]?\d{3}[\-\s]?\d{2}[\-\s]?\d{2}$/.test(value);
};

const vm = new Vue({
    el: '#lead-form-app',
    data:  {
        lead: {
            name: null,
            phone: null,
            city: null,
            channel: null,
            orderDate: null,
            decisionMaker: null,
            madeMeasurement: null,
            interestAssessment: null,
            description: null,
            audioRecord: null,
            publicationRule: null,
            hasAgreement: null,
        },
        channels: [
            {id: 1, value: 'Через сайт'},
            {id: 2, value: 'Прямой контакт'}
        ],
        cities: [
            {id: 1, name: 'Краснодар'},
            {id: 2, name: 'Апшеронск'},
            {id: 3, name: 'Анапа'}
        ],
        errors: {
            name: null,
            phone: null,
            city: null,
            audioRecord: null
        },
        estimate: {
            stars: null,
            cost: null
        }
    },
    validations: {
        lead: {
            name: {
                required: validators.required
            },
            phone: {
                required: validators.required,
                phoneNumber: phoneNumberValidator
            },
            city: {
                required: validators.required
            }
        }
    },
    watch: {
        'lead.city': function(oldValue, newValue) {
            if (oldValue !== newValue && this.isFirstStepFilled) {
                this.makeEstimation();
            }
        },
        'lead.channel': function(oldValue, newValue) {
            if (oldValue !== newValue && this.isSecondStepFilled) {
                this.makeEstimation();
            }
        },
        'lead.orderDate': function(oldValue, newValue) {
            if (oldValue !== newValue && this.isSecondStepFilled) {
                this.makeEstimation();
            }
        },
        'lead.decisionMaker': function(oldValue, newValue) {
            if (oldValue !== newValue && this.isThirdStepFilled) {
                this.makeEstimation();
            }
        },
        'lead.madeMeasurement': function(oldValue, newValue) {
            if (oldValue !== newValue && this.isThirdStepFilled) {
                this.makeEstimation();
            }
        },
        'lead.interestAssessment': function() {
            this.makeEstimation();
        },
        'lead.audioRecord': function() {
            this.makeEstimation();
        }

    },
    mounted: function() {
        $(this.$refs.orderDate).datepicker({onSelect: this.orderDateChanged});
    },
    computed: {
        isFirstStepFilled: function() {
            return this.lead.name
                && this.lead.phone
                && this.lead.city;
        },
        isSecondStepFilled: function() {
            return this.lead.channel
                && this.lead.orderDate;
        },
        isThirdStepFilled: function() {
            return this.lead.decisionMaker
                && this.lead.madeMeasurement;
        },
        isFoursStepFilled: function() {
            return this.lead.interestAssessment;
        },
        isFifthStepFilled: function() {
            return this.lead.description;
        },
        isSixthStepFilled: function() {
            return this.lead.audioRecord;
        },
        canShowSecondStep: function() {
            return this.isFirstStepFilled;
        },
        canShowThirdStep: function() {
            return this.canShowSecondStep && this.isSecondStepFilled;
        },
        canShowFoursStep: function() {
            return this.canShowSecondStep
                && this.canShowThirdStep
                && this.isThirdStepFilled;
        },
        canShowFifthStep: function() {
            return this.canShowSecondStep
                && this.canShowThirdStep
                && this.canShowFoursStep
                && this.isFoursStepFilled;
        },
        canShowSixthStep: function() {
            return this.canShowSecondStep
                && this.canShowThirdStep
                && this.canShowFoursStep
                && this.canShowFifthStep
                && this.isFifthStepFilled;
        }
    },
    methods: {
        orderDateChanged: function(newOrderDate) {
            this.lead.orderDate = newOrderDate;
        },
        interestAssessmentChanged: function(star) {
            this.lead.interestAssessment = star;
        },
        onSubmit: function() {
            debugger;
        },
        onDescriptionChanged: function() {
            this.makeEstimation();
        },
        makeEstimation: function()
        {
            this.$http.post('/lead/estimate', this.lead)
                .then(response => response.json())
                .then(data => this.estimate = data);
        }
    }
});