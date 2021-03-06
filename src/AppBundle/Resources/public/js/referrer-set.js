function parseReferrerFromPath() {
    const query = document.location.search;
    if (query === '' || query.search(/ref=[\d\w]+?/) === -1) {
        return null;
    }
    const pairs = query.slice(1).split('&');
    if (!pairs.length) {
        return null;
    }
    const params = splitBy(pairs, '=');
    if (params['ref'] === 'undefined') {
        return null;
    }
    return params['ref'];
}

function setReferrerCookie(domain = 'localhost', days = 7)
{
    let ref = parseReferrerFromPath();
    if (!ref) {
        return false;
    }
    const cookie = document.cookie;
    if (cookie.search(/referrer=[\d\w]+;?/) >= 0) {
        const pairs = cookie.split('; ');
        if (!pairs.length) {
            return false;
        }
        const cookies = splitBy(pairs, '=');
        if (!cookies['referrer'] || ref === cookie['referrer']) {
            return false;
        }
    }
    const expireAt = new Date();
    expireAt.setDate(expireAt.getDate() + days);
    document.cookie = 'referrer=' + ref + ';path=/;domain=' + domain +  ';expires=' + expireAt.toUTCString();
    return true;
}

function splitBy(data, separator) {
    const result = [];
    let item;
    for (let i in data) {
        item = data[i].split(separator);
        result[item[0]] = item[1];

    }
    return result;
}

window.addEventListener('load', function(event) {
    setReferrerCookie('edward-lead.ru');
});
