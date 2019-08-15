Vue.component('thread-tab', {
    props: ['id', 'lead', 'date', 'status', 'type', 'item', 'thread'],
    template: '#component-thread-tab'
});

Vue.component('thread-message', {
    props: ['sender', 'time', 'logotype', 'body', 'target_in', 'target_out'],
    template: '#component-thread-message'
});

Vue.component('message-form', {
    props: ['children', 'thread', 'errors'],
    template: '#component-message-form',
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
        deleteImage(e) {
            Vue.delete(this.children.images.data, e)
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
    template: '#component-form-image',
    methods: {
        deleteImage(id, name) {
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
        }
    }
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
                this.currentThread.messages.push(event.target.response);
                this.currentThread.status = 'wait_support';
                let id = self.currentThread.id;
                this.openedThreads.forEach(function (thread, i) {
                    if (thread.id === id) {
                        Vue.set(vm.openedThreads, i, vm.currentThread)
                    }
                });
                this.form.children.body.data = '';
                this.form.children.images.data = {};
                this.form.errors = [];
            })
            .catch((event) => {
                this.form.errors = event.target.response.errors;
            });
        },
        onImageUploaded: function(imageData) {
            this.form.children.images.data.push(imageData);
        }
    }
});