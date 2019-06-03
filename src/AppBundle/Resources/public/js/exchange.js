$(function(){
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
            if (this.model.has('id')) {
                this.$el.append($("<a>", {
                    tabIndex: -1,
                    href: '/exchange/lead/' + this.model.get('id'),
                    title: this.title || formattedValue,
                    target: this.target
                }).text('Подробнее'));
                this.delegateEvents();
            }
            return this;
        }
    });

    const Lead = Backbone.Model.extend({});
    const Leads = Backbone.Collection.extend({
        model: Lead,
        url: '/exchange/leads'
    });
    const leads = new Leads();
    const leadColumns = [
        {
            name: 'id',
            label: 'Номер',
            cell: 'integer',
            editable: false,
            sortable: false
        },
        {
            name: 'created_at',
            label: 'Дата',
            cell: Backgrid.DatetimeCell.extend({
                includeTime: false
            }),
            editable: false,
            sortable: false
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

    const Trade = Backbone.Model.extend({});
    const Trades = Backbone.Collection.extend({
        model: Trade,
        url: '/exchange/trades'
    });

    const trades = new Trades();
    const tradeColumns = [
        {
            name: 'id',
            label: 'Номер',
            cell: 'integer',
            editable: false,
            sortable: false
        },
        {
            name: 'created_at',
            label: 'Дата',
            cell: Backgrid.DatetimeCell.extend({
                includeTime: false
            }),
            editable: false,
            sortable: false
        },
        {
            name: 'lead',
            label: 'Лид',
            cell: 'integer',
            editable: false,
            sortable: false
        },
        {
            name: 'buyer',
            label: 'Покупатель',
            cell: 'integer',
            editable: false,
            sortable: false
        },
        {
            name: 'seller',
            label: 'Продавец',
            cell: 'integer',
            editable: false,
            sortable: false
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
        }
    ];

    const leadOffersGrid = new Backgrid.Grid({
        columns: leadColumns,
        collection: leads,
        emptyText: 'Для вашей локации нет предложений по лидам.'
    });

    const tradeHistoryGrid = new Backgrid.Grid({
        columns: tradeColumns,
        collection: trades,
        emptyText:  'В вашей локации нет истории сделок.'
    });

    const $leadsGridArea = $('#lead-offers-grid');
    const $tradesGridArea = $('#trade-history-grid');

    leads.fetch({
        success: function (resp) {
            $leadsGridArea.append(leadOffersGrid.render().el);
            setInterval(function(){
                leads.fetch({reset: true});
            }, 5000);
        }
    });

    trades.fetch({
        success: function (resp) {
            $tradesGridArea.append(tradeHistoryGrid.render().el);
            setInterval(function () {
                trades.fetch({reset: true});
            }, 10000)
        }
    });
});