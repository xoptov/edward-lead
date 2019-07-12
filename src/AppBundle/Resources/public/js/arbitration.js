Vue.component('thread-tab', {
    props: ['id', 'lead', 'date', 'status', 'type', 'item', 'thread'],
    template: '<div v-on:click="$emit(\'change-thread\', item, thread)">' +
            '<div class="tabs-box-arbitrate__number"v-if="type == \'arbitration\'">Арбитраж #{{ id }}</div>' +
            '<div class="tabs-box-arbitrate__number"v-if="type == \'support\'">Обращение #{{ id }}</div>' +
            '<div class="tabs-box-arbitrate__date">{{ date }}</div>' +
            '<div class="tabs-box-arbitrate__state text-blue" v-if="status == \'new\'">Лид #{{ lead }}</div>' +
            '<div class="tabs-box-arbitrate__state text-green" v-if="status == \'wait_user\'">Вам ответила тех. поддержка</div>' +
            '<div class="tabs-box-arbitrate__state" v-if="status == \'wait_support\'">Ожидаем ответа от поддержки</div>' +
            '<div class="tabs-box-arbitrate__state text-blue" v-if="status == \'closed\'">Лид #{{ lead }}</div>' +
        '</div>'
});

Vue.component('thread-message', {
    props: ['sender', 'time', 'logo', 'body', 'target_in', 'target_out'],
    template: '<div class="message" v-bind:class="{arbitrate__message: target_in, arbitrate__comment: target_out}">' +
        '   <div class="message__img"><span v-if="logo"><img v-bind:src="logo"></span></div>' +
        '   <div class="message__content">' +
        '       <p class="message__header"><b>{{ sender }}</b>' +
        '           <span class="message__date">&nbsp; {{ time }}</span>' +
        '       </p>' +
        '       <br>' +
        '       <p class="message__text">{{ body }}</p>' +
        '   </div>' +
        '</div>'
});


var vm = new Vue({
    el: '#arbitrate',
    data: data,
    methods: {
        changeTabBox: function (event, tab) {
            this.openTab = tab === 'open';
            this.archiveTab = tab === 'archive';
        },
        changeThread: function (item, thread) {
            if (thread === 'open') {
                this.currentThread = this.openedThreads[item];
            } else if (thread === 'archive') {
                this.currentThread = this.archiveThreads[item];
            }
        }
    }
});