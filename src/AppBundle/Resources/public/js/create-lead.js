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
