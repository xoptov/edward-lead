class FileUpload {
  constructor(url, headers = {}, onProgress = () => {}, type = 'POST') {
    this.url = url
    this.headers = headers
    this.onProgress = onProgress
    this.type = type
  }

  upload(file, additionalData = {}) {
    let xhr = new XMLHttpRequest()

    // Headers
    xhr.open(this.type, this.url, true)
    xhr.responseType = 'json'
    this._setXhrHeaders(xhr)

    // Events
    xhr.upload.addEventListener('progress', this.onProgress, false)
    let promise = new Promise((resolve, reject) => {
      xhr.onload = e =>
        xhr.status >= 200 && xhr.status < 400 ? resolve(e) : reject(e)
      xhr.onerror = e => reject(e)
    })

    // Start upload
    let formData = new FormData()
    formData.append('file', file)
    Object.keys(additionalData).forEach(p => {
      formData.append(p, additionalData[p])
    })
    xhr.send(formData)

    return promise
  }

  _setXhrHeaders(xhr) {
    Object.keys(this.headers).forEach(p =>
      xhr.setRequestHeader(p, this.headers[p])
    )
  }
}

export default FileUpload
