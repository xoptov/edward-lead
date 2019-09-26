$(function() {
    const UploadButton = Backbone.View.extend({
        events: {
            'click': 'onButtonClick'
        },
        onButtonClick: function(e) {
            e.preventDefault();
            this.trigger('click', e);
        },
        isDisabled: function() {
            return this.el.disabled;
        },
        disable: function() {
            this.$el.attr('disabled', true);
        },
        enable: function() {
            this.$el.attr('disabled', false);
        }
    });

    const LogotypeUploader = Backbone.View.extend({
        initialize: function(options) {
            this.logotypeImage = this.$el.find(options.logotypeImage);
            this.logotypeField = this.$el.find(options.logotypeField);
            this.errorsArea = this.$el.find(options.errorsArea);
            this.uploader = this.$el.find(options.uploader);
            this.uploader.on('change', null, {view: this}, this.onUploaderChange);
            this.button = new UploadButton({el: options.button});
            this.listenTo(this.button, 'click', this.onButtonClick);
        },
        onButtonClick: function(e) {
            e.preventDefault();
            this.uploader.click();
        },
        onUploaderChange: function(e) {
            e.preventDefault();
            const target = e.target;
            const view = e.data.view;
            if (!target.files.length) {
                return false;
            }
            const formData = new FormData();
            formData.append('uploader', target.files[0]);
            view.button.disable();
            $.ajax({
                url: '/api/v1/upload/logotype/logotype_202x202',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(resp) {
                    view.errorsArea.empty();
                    if (view.logotypeImage[0] instanceof HTMLDivElement) {
                        const $newLogotypeImage = $('<img>').addClass('logo-choose__img');
                        view.logotypeImage.replaceWith($newLogotypeImage);
                        view.logotypeImage = $newLogotypeImage;
                    }
                    view.logotypeImage.attr('src', resp.url);
                    view.logotypeField.val(resp.path);
                },
                error: function(xhr) {
                    view.errorsArea.empty();
                    if (413 === xhr.status) {
                        view.errorsArea.append($('<li>Файл слишком большой для загрузки</li>'));
                    }
                    for (let i = 0; i < xhr.responseJSON.errors.length; i++) {
                        view.errorsArea.append('<li>' + xhr.responseJSON.errors[i]  + '</li>');
                    }
                },
                complete: function() {
                    view.button.enable();
                }
            });
        }
    });

    const CompanySettingsView = Backbone.View.extend({
        fields: null,
        initialize: function () {
            this.fields = {
                inn: this.$el.find('#company_inn'),
                shortName: this.$el.find('#company_shortName'),
                largeName: this.$el.find('#company_largeName'),
                ogrn: this.$el.find('#company_ogrn'),
                kpp: this.$el.find('#company_kpp'),
                address: this.$el.find('#company_address'),
                zipcode: this.$el.find('#company_zipcode'),
                phone: this.$el.find('#company_phone')
            };

            this.fields.phone.inputmask('(+7|8)(999)999-99-99');

            this.fields.inn.suggestions({
                token: "3fcb1201bae5ada0e1d53b5c11d5c68764084cc7",
                type: "PARTY",
                formatSelected: function(suggestion) {
                    return suggestion.data.inn;
                },
                onSelect: (suggestion) => {
                    this.setShortName(suggestion);
                    this.setLargeName(suggestion);
                    this.setOGRN(suggestion);
                    this.setKPP(suggestion);
                    this.setAddress(suggestion);
                    this.setZipcode(suggestion);
                }
            });
        },
        setShortName: function(suggestion) {
            if (suggestion.data && suggestion.data.name && suggestion.data.name.short_with_opf) {
                this.fields.shortName.val(suggestion.data.name.short_with_opf);
            }
        },
        setLargeName: function(suggestion) {
            if (suggestion.data && suggestion.data.name && suggestion.data.name.full_with_opf) {
                this.fields.largeName.val(suggestion.data.name.full_with_opf);
            }
        },
        setOGRN: function(suggestion) {
            if (suggestion.data && suggestion.data.ogrn) {
                this.fields.ogrn.val(suggestion.data.ogrn);
            }
        },
        setKPP: function(suggestion) {
            if (suggestion.data && suggestion.data.kpp) {
                this.fields.kpp.val(suggestion.data.kpp);
            }
        },
        setAddress: function(suggestion) {
            if (suggestion.data && suggestion.data.address && suggestion.data.address.value) {
                this.fields.address.val(suggestion.data.address.value);
            }
        },
        setZipcode: function(suggestion) {
            if (suggestion.data && suggestion.data.address && suggestion.data.address.data && suggestion.data.address.data.postal_code) {
                this.fields.zipcode.val(suggestion.data.address.data.postal_code);
            }
        }
    });

    new LogotypeUploader({
        el: '.company__logo-choose',
        logotypeImage: '.logo-choose__img',
        logotypeField: 'input#company_logotypePath',
        errorsArea: '.errors',
        uploader: 'input#company_uploader',
        button: '.js-choice-logotype'
    });

    new CompanySettingsView({el: '.office-settings__left-side'});
});