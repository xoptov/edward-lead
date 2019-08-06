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

    const ErrorView = Backbone.View.extend({
        tagName: 'li',
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
                    if (view.logotypeImage[0] instanceof HTMLDivElement) {
                        const $newLogotypeImage = $('<img>').addClass('logo-choose__img');
                        view.logotypeImage.replaceWith($newLogotypeImage);
                        view.logotypeImage = $newLogotypeImage;
                    }
                    view.logotypeImage.attr('src', resp.url);
                    view.logotypeField.val(resp.path);
                },
                error: function(xhr) {
                    debugger;
                    //todo: тут короче нужно рэндерить ошибки загрузки логотипа.
                },
                complete: function() {
                    view.button.enable();
                }
            });
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

    $('#company_phone').inputmask('(+7|8)(999)999-99-99');
});