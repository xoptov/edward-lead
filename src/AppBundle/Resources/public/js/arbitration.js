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
    template: '<form class="arbitrate-form" method="post" enctype="multipart/form-data" name="message"' +
        '   v-on:open-dialog="openFileDialog"' +
        '>' +
        '   <textarea class="arbitrate-form__textarea autoExpand" id="message_body" name="message[body]" rows="3" data-min-rows="3" placeholder="Напишите свое сообщение" required="" v-model="children.body.data"></textarea>' +
        '   <div class="arbitrate-form__button-block">' +
        '       <button type="button" class="arbitrate-form__button" v-on:click="$emit(\'submit-form\')"><span class="icon-submit"></span></button>' +
        '       <button type="button" class="arbitrate-form__button" v-on:click="openFileDialog"><span class="icon-clip"></span></button>' +
        '   </div>' +
        '   <div class="arbitrate-form__selected-files binded">' +
        '       <form-image' +
        '           v-for="image, key in children.images.data"' +
        '           v-bind:image="image"' +
        '           v-bind:id="key"' +
        '           v-bind:url="children.images.deleteUrl"' +
        '           v-on:delete-image="deleteImage"' +
        '       ></form-image>' +
        '   </div>' +
        '   <input type="hidden" id="message_thread" name="message[thread]" v-bind:value="thread">' +
        '   <input type="hidden" id="message__token" name="message[_token]" v-bind:value="children._token.data">' +
        '   <input type="file" accept="image/jpeg,image/png" multiple style="display: none" ref="file" v-on:change="addedFiles">' +
        '</form>',
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
    template: '<div class="binded__item">' +
        '   <input type="hidden" v-bind:id="\'message_images_\' + image.id" v-bind:name="\'message[images][\' + image.id + \']\'" v-bind:value="id">' +
        '   <button class="binded__button" type="button" v-on:click="$emit(\'open-dialog\')"><span class="icon-clip"></span></button>' +
        '   <span class="binded__file">{{ image.realName }}</span>' +
        '   <button class="button-close" type="button" v-on:click="deleteImage(id, image.serverName)">×</button>' +
        '</div>',
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