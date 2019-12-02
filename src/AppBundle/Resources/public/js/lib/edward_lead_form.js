function drawForm(){
    
    const ext$from = document.getElementById("edward-lead-form");

    const div$0$0 = document.createElement("div");
    div$0$0.setAttribute("style", "margin-bottom: 20px;");
    div$0$0.setAttribute("id", "edward-lead-form-header");

    const h1$1$0 = document.createElement("h1");
    div$0$0.appendChild(h1$1$0);
    h1$1$0.appendChild(document.createTextNode("Оставьте свои контактные данные"));

    const p$1$1 = document.createElement("p");
    div$0$0.appendChild(p$1$1);
    p$1$1.appendChild(document.createTextNode("и наш менеджер свяжется с Вами"));
    const br$2$1 = document.createElement("br");
    p$1$1.appendChild(br$2$1);
    p$1$1.appendChild(document.createTextNode("в ближайшее время"));

    const div$1 = document.createElement("div");
    div$1.setAttribute("class","edward-lead-div-main-error");
    
    const text_main_error$1$0 = document.createElement("span");
    text_main_error$1$0.classList.add("text-error");
    text_main_error$1$0.setAttribute("id", "text-main-error");
    
    div$1.appendChild(text_main_error$1$0);
    div$0$0.appendChild(div$1);

    const div$0$1 = document.createElement("div");
    div$0$1.classList.add("edward-lead-form-fields");
    
    const form$1$0 = document.createElement("form");
    form$1$0.setAttribute("method", "POST");
    form$1$0.setAttribute("id", "form");
    div$0$1.appendChild(form$1$0);

    const div$2$0 = document.createElement("div");
    div$2$0.classList.add("form-item");

    const label$3$0 = document.createElement("label");
    label$3$0.setAttribute("for", "name");
    div$2$0.appendChild(label$3$0);

    label$3$0.appendChild(document.createTextNode("Ваше имя:"));

    const span$3$1 = document.createElement("span");
    span$3$1.classList.add("error");
    div$2$0.appendChild(span$3$1);

    const name$4$0 = document.createElement("input");
    name$4$0.setAttribute("id", "name");
    name$4$0.setAttribute("type", "text");
    name$4$0.setAttribute("name", "name");
    name$4$0.setAttribute("placeholder", "пример: Иван");
    span$3$1.appendChild(name$4$0);

    const text_name_error$4$1 = document.createElement("span");
    text_name_error$4$1.classList.add("text-error");
    text_name_error$4$1.setAttribute("id", "text-name-error");
    span$3$1.appendChild(text_name_error$4$1);

    const div$2$1 = document.createElement("div");
    div$2$1.classList.add("form-item");

    const label$3$1 = document.createElement("label");
    label$3$1.setAttribute("for", "lead-phone");
    label$3$1.appendChild(document.createTextNode("Ваш телефон: "));
    div$2$1.appendChild(label$3$1);

    const span$4$2 = document.createElement("span");
    span$4$2.setAttribute("style", "font-size: 14px; color:#6b6b6b;");
    label$3$1.appendChild(span$4$2);

    span$4$2.appendChild(document.createTextNode("(обязательное поле)"));

    const span$3$2 = document.createElement("span");
    span$3$2.classList.add("error");
    div$2$1.appendChild(span$3$2);

    const phone$4$0 = document.createElement("input");
    phone$4$0.setAttribute("id", "lead-phone");
    phone$4$0.setAttribute("type", "text");
    phone$4$0.setAttribute("name", "phone");
    phone$4$0.setAttribute("placeholder", "пример: +7(900)100-10-20");
    span$3$2.appendChild(phone$4$0);
    
    const phone_error$4$1 = document.createElement("span");
    phone_error$4$1.classList.add("text-error");
    phone_error$4$1.setAttribute("id", "text-phone-error");
    span$3$2.appendChild(phone_error$4$1);

    const div$2$2 = document.createElement("div");
    
    const submit$3$0 = document.createElement("input");
    submit$3$0.setAttribute("id", "btn-submit");
    submit$3$0.setAttribute("type", "button");
    submit$3$0.setAttribute("value", "Отправить заявку");
    div$2$2.appendChild(submit$3$0);
    
    form$1$0.appendChild(div$2$0);
    form$1$0.appendChild(div$2$1);
    form$1$0.appendChild(div$2$2);

    const div$footer = document.createElement("div");
    div$footer.setAttribute("id","edward-lead-form-footer");
    div$footer.classList.add("edward-lead-form-footer");
    div$footer.appendChild(document.createTextNode("Отправляя сведенья через электронную форму, вы соглашаетесь с условиями "));

    const a$agree = document.createElement("a");
    a$agree.setAttribute("href","#");
    a$agree.setAttribute("target","_blank");
    a$agree.appendChild(document.createTextNode("политики конфиденциальности"));
    
    div$footer.appendChild(a$agree);

    /* input mask */
    Inputmask({ 
        "mask": '(+7|8)(###)###-##-##',
        "definitions": {"#" : { validator: "[0-9]"}}
    }).mask(phone$4$0);

    let xhr = new XMLHttpRequest();

    // -- form submit event --
    submit$3$0.addEventListener("click" , function() {

        //Предохранитель от повторных запросов до получения статуса DONE
        if (xhr.readyState !== 0 && xhr.readyState !== 4) {
            return false;
        }

        let elmMainError = text_main_error$1$0;
        let elmNameError = text_name_error$4$1;
        let elmPhoneError = phone_error$4$1;
        let elmName = name$4$0;
        let elmPhone = phone$4$0;
        let elmSubmit = submit$3$0;

        elmMainError.innerText = "";
        elmMainError.style.cssText = "display:none;";

        elmNameError.innerHTML = "";
        elmNameError.style.cssText = "display:none;";

        elmPhoneError.innerHTML = "";
        elmPhoneError.style.cssText = "display:none;";

        let formData = new FormData();

        formData.append("name", elmName.value);
        formData.append("phone", elmPhone.value);
        formData.append("hasAgreement", true);
        
        xhr.open("POST", "/edward.php", true);
        // xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded; charset=utf-8');

        xhr.onreadystatechange = function (event) {

            if(event.target.readyState === XMLHttpRequest.DONE) {
                let msgMain = "";
                let msgName = "";
                let msgPhone = "";

                if (event.target.status === 200) { //if 200
                    //document.getElementsByClassName("text-error").html("");
                    document.querySelectorAll("text-error").innerText = "";
                    
                    elmNameError.style.cssText = "display:none;";
                    elmPhoneError.style.cssText = "display:none;";
                    
                    elmMainError.innerText = "Лид успешно добавлен";
                    
                    elmMainError.style.cssText = "color:green; display:block;";
                } 
                
                if( event.target.status === 400 || event.target.status === 403 || event.target.status ===500 ) { //if other ERROR cases

                    let obj = JSON.parse( event.target.response );
                    
                    console.log(obj);

                    msgMain = "";
                    msgName = "";
                    msgPhone = "";
    
                   
                    if(obj == null) return;
                    
                    for(let i in obj){ //Для всех элементов массива
                        if(typeof obj[i] === "string"){ // Если это просто текст
                            //добавить ошибку в заголовок
                            if(!msgMain.length){
                                msgMain = obj[i];
                            }else{
                                msgMain +="<br>"+obj[i];
                            }
                        }

                        if(typeof obj[i] === "object"){ //Если это объект
                            //показать ошибку в имени
                            if(typeof obj[i]["name"] === "object"){
                                for(p in obj[i]["name"]){ //содержание массива
                                    if(!msgName.length){
                                        msgName = obj[i]["name"][p];
                                    }else{
                                        msgName += "<br>"+obj[i]["name"][p];
                                    }
                                }
                            }

                            //показать ошибку в телефоне
                            if(typeof obj[i]["phone"] === "object"){
                                for(p in obj[i]["phone"]){ //содержание массива
                                    if(!msgPhone.length){
                                        msgPhone = obj[i]["phone"][p];
                                    }else{
                                        msgPhone += "<br>"+obj[i]["phone"][p];
                                    }
                                }
                            }
                        }
                    }
                }

                //Заполнение объектов на форме
                if(msgMain.length){ //Если основные ошибки заполнены
                    elmMainError.innerText = msgMain;
                    elmMainError.style.cssText = "color:red; display:block;";
                }
                
                if(msgName.length){ //Если сообщение с именем не пустое
                    elmNameError.innerText = msgName;
                    elmNameError.style.cssText = "color:red; display:block;";
                }

                if(msgPhone.length){ //Если ошибки поля заполнены
                    elmPhoneError.innerText = msgPhone;
                    elmPhoneError.style.cssText = "color:red;display:block;";
                }
                
                //todo: Резблокировать кнопку отправки!
                //Снятие блокировки с кнопки
                elmSubmit.disabled = false;
            }
        };

        //todo: Болкировать кнопку отправки!
        //Блокировка кнопки на время выполнения запроса
        elmSubmit.disabled = true;

        xhr.send(formData);

    });//end of button.click()

    ext$from.appendChild(div$0$0);
    ext$from.appendChild(div$0$1);
    ext$from.appendChild(div$footer);
}

window.addEventListener("load", (event) => { drawForm(); });
