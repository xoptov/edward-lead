Vue.component('thread-tab', {
    props: ['id', 'lead', 'date', 'status', 'type', 'item', 'thread'],
    template: '#component-thread-tab'
});

Vue.component('thread-message', {
    props: ['sender', 'time', 'logo', 'body', 'target_in', 'target_out'],
    template: '#component-thread-message'
});

Vue.component('message-form', {
    props: ['children', 'thread'],
    template: '#component-message-form',
    methods: {
        openFileDialog() {
            this.$refs.file.click();
        },
        addedFiles(event) {
            if (typeof this.fileCount === 'undefined') {
                this.fileCount = 0;
            }
            let self = this;
            $.each(event.target.files, function (key, value) {
                self.fileCount++;
                self.upload(value, self.children.images.uploadUrl)
                    .then(e => {
                        let element = {id: self.fileCount, realName: value.name, serverName: e.target.response.name};
                        Vue.set(vm.form.children.images.data, e.target.response.id, element);
                    })
                    .catch(e => {
                        console.log(e);
                    });
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
                xhr = new XMLHttpRequest(),
                self = this;

            xhr.open('POST', this.form.action, true);
            xhr.responseType = 'json';

            let promise = new Promise((resolve, reject) => {
                xhr.onload = e =>
                    xhr.status >= 200 && xhr.status < 400 ? resolve(e) : reject(e);
                xhr.onerror = e => reject(e);
            });

            xhr.send(formData);

            promise
                .then(function (e) {
                    self.currentThread.messages.push(e.target.response);
                    self.currentThread.status = 'wait_support';
                    let id = self.currentThread.id;
                    self.openedThreads.forEach(function (thread, i) {
                        if (thread.id === id) {
                            Vue.set(vm.openedThreads, i, vm.currentThread)
                        }
                    });
                    self.form.children.body.data = '';
                    self.form.children.images.data = {};
                })
                .catch(function (e) {
                    console.log(e);
                });
        }
    }
});