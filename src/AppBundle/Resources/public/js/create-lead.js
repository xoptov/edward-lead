class StepView {
    constructor(options) {
        this.$form = options.$form;
        this.el = this.$form.find(options.selector);
        this.nextBlock = options.nextBlock || null;
        this.alwaysFilled = options.alwaysFilled || false;

        const iconSelector = options.iconSelector || '.icon-star-new';
        this.starIcon = this.el.find(iconSelector).first();

        const showOnInit = options.showOnInit || false;

        this.fields = this.el.find('input,select,textarea');
        this.fields.on('change', null, this, StepView.onChange);

        const $inputs = this.fields.filter('input,textarea');
        $inputs.on('keyup', null, this, StepView.onChange);
        $inputs.filter('input[type=text],input[type=tel]').on('focusout', StepView.validate);

        if (!showOnInit && !this.isFilled()) {
            this.el.hide(250);
            this.showed = false;
        } else {
            this.showed = true;
        }

        if (this.isFilled()) {
            this.colorStarYellow();
            this.nextBlock && this.nextBlock.showElement();
        }

        options.initializer && options.initializer(this);
    }

    static onChange(e) {
        const t = e.target;
        const view = e.data;
        if (t.checkValidity()) {
            $(t).removeClass('invalid');
            if (view.isFilled() || view.alwaysFilled) {
                view.colorStarYellow();
                view.nextBlock && view.nextBlock.showElement();
                if (view.changeTimerId) {
                    clearTimeout(view.changeTimerId);
                }
                view.changeTimerId = setTimeout(view.requestCost, 1500, view);
            } else {
                view.colorStarGray();
            }
        }
    }

    static onFocusOut(e) {
        const t = e.target;
        const view = e.data;
        if (t.checkValidity()) {
            $(t).removeClass('invalid');
        } else {
            $(t).addClass('invalid');
        }
    }

    isFilled() {
        if (this.alwaysFilled) {
            return true;
        }
        for (let i = 0; i < this.fields.length; i++) {
            let $field = $(this.fields[i]);
            if ($field.val() === '') {
                return false;
            }
        }

        return true;
    }

    showElement() {
        if (!this.showed) {
            this.el.show(500);
        }
    }

    requestCost(view) {
        const formData = new FormData(view.$form[0]);
        $.ajax({
            url: '/exchange/calculate-lead-cost',
            method: 'POST',
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            success: function(resp) {
                view.updateStarsAndCost(resp);
            }
        });
    }

    updateStarsAndCost(data) {
        const $pointStars = this.$form.find('.js-points .icon-star-new');
        const $costLabel = this.$form.find('.js-cost');
        $costLabel.text(data.cost);
        $pointStars.each(function(i,o) {
            if (i + 1 <= data.stars) {
                $(o).removeClass('text-gray').addClass('text-yellow');
            } else {
                $(o).removeClass('text-yellow').addClass('text-gray');
            }
        });
    }

    colorStarYellow() {
        this.starIcon && this.starIcon.removeClass('text-gray').addClass('text-yellow');
    }

    colorStarGray() {
        this.starIcon && this.starIcon.removeClass('text-yellow').addClass('text-gray');
    }
}

$(function(){
    const $form = $('form');

    const $formStep6 = new StepView({
        $form: $form,
        selector: '#block-step-6',
        iconSelector: '.gold-label',
        initializer: function(block) {
            const $uploader = $('input.js-uploader');
            const $audioRecordBtn = $('button.binded__button');
            const $fileName = $('.binded__file');
            const $removeButton = $('.button-close');

            $audioRecordBtn.on('click', function(e) {
                e.preventDefault();
                $uploader.click();
            });
            $uploader.on('change', function(e) {
                e.preventDefault();
                const file = this.files[0];
                if (file instanceof File) {
                    $fileName.text(file.name);
                }
            });
            $removeButton.on('click', function(e) {
                e.preventDefault();
                $uploader.val('').trigger('change');
                $fileName.text('Прикрепить запись');
            });
        }
    });

    const $formStep5 = new StepView({
        $form: $form,
        selector: '#block-step-5',
        nextBlock: $formStep6
    });

    const $formStep4 = new StepView({
        $form: $form,
        selector: '#block-step-4',
        nextBlock: $formStep5
    });

    const $formStep3 = new StepView({
        $form: $form,
        selector: '#block-step-3',
        nextBlock: $formStep4,
        alwaysFilled: true
    });

    const $formStep2 = new StepView({
        $form: $form,
        selector: '#block-step-2',
        nextBlock: $formStep3
    });

    const $formStep1 = new StepView({
        $form: $form,
        selector: '#block-step-1',
        showOnInit: true,
        nextBlock: $formStep2
    });

    const $starsField = $('#stars').find('input[type=hidden]');

    $('.star-box__item').on('click', function(e) {
        const value = $(this).data('value');
        $starsField.val(value);
        $starsField.trigger('change');
    });
});