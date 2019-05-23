$(function(){
    const Lead = Backbone.Model.extend({});

    const Leads = Backbone.Collection.extend({
        model: Lead,
        url: '/exchange/leads'
    });
    const leads = new Leads();

    Backgrid.QualityCell = Backgrid.StringCell.extend({
        className: 'quality-cell',
        render: function() {
            this.$el.empty();
            const model = this.model;
            const columnName = this.column.get("name");
            this.$el.addClass(columnName);
            const value = model.get(columnName);
            if (model.get('audio_record') && value >= 5) {
                this.$el.append($("<span>", {class: "table__span-gold"}).text('GOLD'));
            } else {
                for (let i = 0; i < value; i++) {
                    this.$el.append($("<span>", {
                        class: "icon-star-new text-yellow"
                    }));
                }
            }

            this.updateStateClassesMaybe();
            this.delegateEvents();

            return this;
        }
    });

    Backgrid.ButtonCell = Backgrid.UriCell.extend({
        className: 'button-cell',
        render: function () {
            this.$el.empty();
            const rawValue = this.model.get(this.column.get("name"));
            const formattedValue = this.formatter.fromRaw(rawValue, this.model);
            this.$el.append($("<a>", {
                tabIndex: -1,
                href: '/exchange/lead/' + this.model.get('id'),
                title: this.title || formattedValue,
                target: this.target
            }).text('Подробнее'));
            this.delegateEvents();
            return this;
        }
    });

    const columns = [
        {
            name: 'created_at',
            label: 'Дата публикации',
            cell: Backgrid.DatetimeCell.extend({
                includeTime: false
            }),
            editable: false,
            sortable: false,
            includeTime: false
        },
        {
            name: 'stars',
            label: 'Качество лида',
            cell: 'quality',
            editable: false,
            sortable: false
        },
        {
            name: 'city',
            label: 'Город',
            cell: 'string',
            editable: false,
            sortable: false
        },
        {
            name: 'channel',
            label: 'Источник',
            cell: 'string',
            editable: false,
            sortable: false
        },
        {
            name: 'price',
            label: 'Стоимость',
            cell: 'integer',
            editable: false,
            sortable: false,
            formatter: _.extend({}, Backgrid.CellFormatter.prototype, {
                fromRaw: function(number, model) {
                    let formattedValue = Backgrid.NumberFormatter.prototype.fromRaw(number, model);
                    if (formattedValue !== '') {
                        return formattedValue + ' руб.';
                    }
                    return formattedValue;
                }
            })
        },
        {
            name: '',
            cell: 'button',
            editable: false,
            sortable: false
        }
    ];

    const leadGrid = new Backgrid.Grid({
        columns: columns,
        collection: leads
    });

    $('#lead-grid').append(leadGrid.render().el);

    leads.fetch({reset:true});

    setInterval(function() {
        leads.fetch({reset:true});
    }, 10000);
});