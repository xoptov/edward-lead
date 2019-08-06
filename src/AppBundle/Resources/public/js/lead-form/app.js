Vue.use(window.vuelidate.default);

const audioAllowedTypes = ['audio/webm', 'audio/ogg', 'audio/mpeg', 'audio/mp3', 'audio/mp4', 'audio/wave', 'audio/wav', 'audio/flac'];
const audioMaxSize = 1024 * 1024 *  2;

const phoneNumberValidator = function(value) {
    if (value !== null) {
        return /^(?:\+7|8)(?:\(?\d{3}\)?\d{3}|\(?\d{4}\)?\d{2}|\(?\d{5}\)?\d{1})\-?\d{2}\-?\d{2}$/.test(value);
    }
    return false;
};

const fileSizeValidator = function(maxFileSize) {
    return function(file) {
        if (file instanceof File) {
            return file.size <= maxFileSize;
        }
        return false;
    };
};

const fileTypeValidator = function(allowedTypes) {
    return function(file) {
        if (file instanceof File) {
            for (let i = 0; i < allowedTypes.length; i++) {
                if (file.type === allowedTypes[i]) {
                    return true;
                }
            }
        }
        return false;
    };
};

const vm = new Vue({
    el: '#lead-form-app',
    data:  {
        debugMode: false,
        leadId: leadId,
        lead: {
            room: roomId,
            name: null,
            phone: null,
            city: '',
            channel: '',
            orderDate: null,
            decisionMaker: null,
            madeMeasurement: null,
            interestAssessment: null,
            description: null,
            audioRecord: null,
            publicationRule: null,
            hasAgreement: null,
        },
        channels: null,
        cities: null,
        submitErrors: null,
        estimate: {
            stars: null,
            cost: null
        },
        uploadingFile: null,
        submitted: false
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
            publicationRule: {
                required: validators.required
            },
            hasAgreement: {
                required: validators.required
            }
        },
        uploadingFile: {
            fileSize: fileSizeValidator(audioMaxSize),
            fileType: fileTypeValidator(audioAllowedTypes)
        }
    },
    watch: {
        'lead.phone': function(oldValue, newValue) {
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
    beforeCreate: function() {
        this.$http.get('/api/v1/lead/form/settings')
            .then(response => {
                this.channels = response.data.channels;
                this.cities = response.data.cities;
            })
            .catch(error => console.debug(error));
    },
    created: function() {
        if (this.leadId) {
            this.$http.get('/api/v1/lead/' + this.leadId).then(
                response => {
                    this.$_lead_populateFromServer(response.data);
                }
            );
        }
    },
    mounted: function() {
        $(this.$refs.orderDate).datepicker({
            minDate: '-2m',
            maxDate: 'today',
            onSelect: this.orderDateChanged
        });
        $(this.$refs.phone).inputmask({mask: '(+7|8)(999)999-99-99', oncomplete: this.phoneChanged});
    },
    computed: {
        isFirstStepFilled: function() {
            return this.lead.name && this.lead.phone;
        },
        isSecondStepFilled: function() {
            return this.lead.channel && this.lead.orderDate;
        },
        isThirdStepFilled: function() {
            return this.lead.decisionMaker && this.lead.madeMeasurement;
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
        isAudioUploaded: function() {
            return this.lead.audioRecord !== null;
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
        },
        isReadyForSubmit: function() {
            if (this.$v.lead.$invalid) {
                return false;
            }
            return this.lead.hasAgreement && this.lead.publicationRule;
        }
    },
    methods: {
        orderDateChanged: function(newOrderDate) {
            this.lead.orderDate = newOrderDate;
        },
        phoneChanged: function(event) {
            this.lead.phone = event.target.value;
        },
        interestAssessmentChanged: function(star) {
            if (this.submitted) {
                return false;
            }
            this.lead.interestAssessment = star;
        },
        onDescriptionChanged: function() {
            this.makeEstimation();
        },
        makeEstimation: function()
        {
            this.$http.post('/api/v1/lead/estimate', this.lead)
                .then(response => this.estimate = response.data);
        },
        onUploadClicked: function() {
            if (this.submitted) {
                return false;
            }
            this.$refs.recordUploader.click();
        },
        onUploadChanged: function(event) {
            // Входим если нет файла в FileList
            if (!event.target.files.length) {
                return false;
            }

            this.uploadingFile = event.target.files[0];

            if (!this.$v.uploadingFile.$invalid) {
                const formData = new FormData();
                formData.append('uploader', this.uploadingFile, this.uploadingFile.filename);
                this.$http.post('/api/v1/upload/audio', formData)
                    .then(function(response) {
                        this.lead.audioRecord = response.data.url;
                    });
            }
        },
        onRemoveAudioClicked: function() {
            if (this.submitted) {
                return false;
            }
            this.uploadingFile = null;
            this.lead.audioRecord = null;
        },
        onSubmit: function() {
            if (!this.$v.lead.$invalid) {
                if (this.leadId) {
                    this.$http.put('/api/v1/lead/' + this.leadId, this.lead)
                        .then(
                            response => {
                                this.submitted = true;
                                setTimeout(() => {
                                    window.location.href = '/lead/' + this.leadId;
                                }, 1000);
                            }
                        )
                        .catch(
                            response => this.submitErrors = response.data.errors
                        );
                } else {
                    this.$http.post('/api/v1/lead', this.lead)
                        .then(
                            response => {
                                this.submitted = true;
                                setTimeout(() => {
                                    if (this.lead.room) {
                                        window.location.href = '/room/' + this.lead.room;
                                    } else {
                                        window.location.href = '/exchange';
                                    }
                                }, 1000);
                            }
                        )
                        .catch(
                            response => this.submitErrors = response.data.errors
                        );
                }
            }
        },
        $_lead_populateFromServer: function(data) {
            for (const field in data) {
                if (!data.hasOwnProperty(field)) {
                    continue;
                }
                if (!this.lead.hasOwnProperty(field)) {
                    continue;
                }
                if (data[field] === null) {
                    continue;
                }
                if (typeof data[field] === 'object' && data[field].hasOwnProperty('id')) {
                    this.lead[field] = data[field].id;
                } else {
                    this.lead[field] = data[field];
                }
            }
        }
    }
});