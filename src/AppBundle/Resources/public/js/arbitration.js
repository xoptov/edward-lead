Vue.component('thread-tab', {
    props: ['id', 'lead', 'date', 'status', 'type', 'item', 'thread'],
    template: '#thread-tab',
    computed: {
        label: function() {
            if ('arbitration' === this.type) {
                return 'Арбитраж #' + this.id;
            }
            return 'Обращение #' + this.id;
        },
        state: function() {
            if ('arbitration' === this.type) {
                if ('new' === this.status || 'closed' === this.status) {
                    return 'Лид #' + this.lead;
                }
            } else {
                if ('new' === this.status) {
                    return 'Новое обращение';
                }
                if ('closed' === this.status) {
                    return 'Закрыто';
                }
            }
            switch (this.status) {
                case 'wait_user':
                    return 'Вам ответила тех. поддержка';
                case 'wait_support':
                    return 'Ожидаем ответа от поддержки';
            }
            return 'Неизвестно';
        }
    }
});

Vue.component('thread-message', {
    props: ['sender', 'time', 'logotype', 'body', 'target_in', 'target_out', 'images'],
    template: '#thread-message'
});

Vue.component('message-form', {
    props: ['children', 'thread', 'errors'],
    template: '#message-form',
    created: function() {
        this.$root.$on('thread-changed', this.onThreadChanged);
    },
    methods: {
        openFileDialog() {
            this.$refs.file.click();
        },
        addedFiles(event) {
            $.each(event.target.files, (key, value) => {
                this.upload(value, this.children.images.uploadUrl)
                    .then(event => this.$emit('image-uploaded', {id: event.target.response.id, realName: value.name, serverName: event.target.response.name}))
                    .catch(event => console.log(event));
            });
        },
        upload(file, url, method = "POST", headers = {}, additionalData = {}) {
            let xhr = new XMLHttpRequest();

            // Headers
            xhr.open(method, url, true);
            xhr.responseType = 'json';
            this._setXhrHeaders(xhr, headers);

            // Events
            let promise = new Promise((resolve, reject) => {
                xhr.onload = e =>
                    xhr.status >= 200 && xhr.status < 400 ? resolve(e) : reject(e);
                xhr.onerror = e => reject(e);
            });

            // Start upload
            let formData = new FormData();
            formData.append('file', file);
            Object.keys(additionalData).forEach(p => {
                formData.append(p, additionalData[p]);
            });
            xhr.send(formData);

            return promise;
        },
        onThreadChanged: function() {
            this.$refs.messageTextarea.focus();
        },
        _setXhrHeaders(xhr, headers) {
            Object.keys(headers).forEach(p =>
                xhr.setRequestHeader(p, headers[p])
            )
        }
    }
});

Vue.component('form-image', {
    props: ['image', 'id', 'url'],
    template: '#form-image',
    methods: {
        deleteImage() {
            let xhr = new XMLHttpRequest(),
                url = this.url + '?id=' + id + '&fileName=' + name;

            xhr.open('GET', url, true);
            xhr.responseType = 'json';

            // Events
            let promise = new Promise((resolve, reject) => {
                xhr.onload = e =>
                    xhr.status >= 200 && xhr.status < 400 ? resolve(e) : reject(e);
                xhr.onerror = e => reject(e);
            });

            xhr.send();

            promise
                .then(e => {
                    this.$emit('delete-image', id);
                })
                .catch(e => {
                    console.log(e);
                })
            ;
        },
        onDeleteClicked: function(id, name) {
            let xhr = new XMLHttpRequest(),
                url = this.url + '?id=' + id + '&fileName=' + name;

            xhr.open('GET', url, true);
            xhr.responseType = 'json';

            // Events
            let promise = new Promise((resolve, reject) => {
                xhr.onload = e =>
                    xhr.status >= 200 && xhr.status < 400 ? resolve(e) : reject(e);
                xhr.onerror = e => reject(e);
            });

            xhr.send();

            promise.then(e => {
                    this.$root.$emit('image-deleted', this.id);
                })
                .catch(e => {
                    console.log(e);
                });
        }
    }
});

var vm = new Vue({
    el: '#arbitrate',
    data: data,
    computed: {
        isCurrentThreadOpen: function() {
            return this.currentThread && this.currentThread.status !== 'closed';
        },
        isOpenNewThreadError: function() {
            return this.openNewThreadError;
        }
    },
    methods: {
        changeTabBox: function (tab) {
            this.openTab = tab === 'open';
            this.archiveTab = tab === 'archive';
        },
        changeThread: function (item, thread) {
            if (thread === 'open') {
                this.currentThread = this.openedThreads[item];
            } else if (thread === 'archive') {
                this.currentThread = this.archiveThreads[item];
            }
            this.$emit('thread-changed');
        },
        formSubmit: function () {
            let formData = new FormData(document.querySelector('form')),
                xhr = new XMLHttpRequest();

            xhr.open('POST', this.form.action, true);
            xhr.responseType = 'json';

            let promise = new Promise((resolve, reject) => {
                xhr.onload = e =>
                    xhr.status >= 200 && xhr.status < 400 ? resolve(e) : reject(e);
                xhr.onerror = e => reject(e);
            });

            xhr.send(formData);

            promise.then((event) => {
                this.form.errors = [];
                this.currentThread.messages.push(event.target.response);
                this.currentThread.status = 'wait_support';
                let id = this.currentThread.id;
                this.openedThreads.forEach(function (thread, i) {
                    if (thread.id === id) {
                        Vue.set(vm.openedThreads, i, vm.currentThread)
                    }
                });
                this.form.children.body.data = '';
                this.form.children.images.data = [];
            })
            .catch((event) => {
                this.form.errors = [];
                this.form.errors = event.target.response.errors;
            });
        },
        onImageUploaded: function(imageData) {
            this.form.children.images.data.push(imageData);
        },
        onImageDeleted: function(id) {
            if (this.form.children.images.data[id]) {
                Vue.set(this.form.children.images.data, this.form.children.images.data.splice(id, 1));
            }
        },
        createNewThread: function() {
            // Проверяем есть ли таймер запущенный 60 секунд назад.
            if (this.openNewThreadTimer) {
                if (!this.openNewThreadError) {
                    this.openNewThreadError = 'Вы создали обращение меньше минуты назад';
                }
                return false;
            }

            this.$http.post('/api/v1/support')
                .then((response) => {
                    this.openNewThreadError = null;
                    this.openedThreads.push(response.data);
                    this.currentThread = response.data;
                    this.$emit('thread-changed');
                })
                .catch((event) => {
                    this.openNewThreadError = event.body.error;
                    if (!this.errorShowTimer) {
                        this.errorShowTimer = setTimeout(() => {
                            this.openNewThreadError = this.errorShowTimer = null;
                        }, 5000);
                    }
                });

            // Блокируем кнопку на фронте.
            this.openNewThreadTimer = setTimeout(
                () => {
                    this.openNewThreadTimer = this.openNewThreadError = null;
                },
                60000
            );
        }
    }
});

vm.$on('image-deleted', vm.onImageDeleted);