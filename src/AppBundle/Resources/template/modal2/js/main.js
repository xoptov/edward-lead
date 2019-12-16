$(document).ready(function () {

  // Show menu
  $('.navbar-toggle').click(function() {
    $(this).toggleClass('active');
    $('.nav').toggleClass('nav_open');
  });

  $(document).click(function(event) {
    if ($(event.target).closest('.navbar-toggle').length 
      || $(event.target).closest('.nav').length ) return;
      $('.nav').removeClass('nav_open');
      $('.navbar-toggle').removeClass('active');
      event.stopPropagation();
  });

  // Navdrop
  $('.nav__drop').click(function(e) {
    e.preventDefault();
    $(this).toggleClass('nav__drop_open');
    $('.nav__subnav').slideToggle( "slow", function() {});
  })

  // Select
  $('select').niceSelect();

  // Days
  $('.create-room__sett__days__item').click(function() {
    $(this).toggleClass('create-room__sett__days__item_active');
  })

  // Modal
  $('.modal_deactivate__open').click(function() {
    $('.modal_deactivate').fadeIn();
  });

  $('.modal_exclude__open').click(function() {
    $('.modal_exclude').fadeIn();
  });

  $('.modal_impossible__open').click(function() {
    $('.modal_impossible').fadeIn();
  });

  $('.modal__close').click(function() {
    $('.modal').fadeOut();
  });

  $('.modal__assistant .modal__box__foot .btn, .btn.modal__bell__btn').click(function(e) {
    e.preventDefault();
    $('.modal').fadeOut();
  })

  // Modal example - аПаОаЖаЕбаЕ аПаОбаОаМ баДаАаЛаИбб
  $('.modal_errors__open').click(function() {
    $('.modal_errors').fadeIn();
  });
  $('.modal_start__open').click(function() {
    $('.modal_start').fadeIn();
  });
  $('.modal_company__open').click(function() {
    $('.modal_company').fadeIn();
  });
  $('.modal_profile__open').click(function() {
    $('.modal_profile').fadeIn();
  });
  $('.modal_office__open').click(function() {
    $('.modal_office').fadeIn();
  });
  $('.modal_room__open').click(function() {
    $('.modal_room').fadeIn();
  });
  $('.modal_invitations__open').click(function() {
    $('.modal_invitations').fadeIn();
  });
  $('.modal_leads__open').click(function() {
    $('.modal_leads').fadeIn();
  });
  $('.modal_basket__open').click(function() {
    $('.modal_basket').fadeIn();
  });
  $('.modal_append__open').click(function() {
    $('.modal_append').fadeIn();
  });
  $('.modal_bell__open').click(function() {
    $('.modal_bell').fadeIn();
  });
  $('.modal_rating__open').click(function() {
    $('.modal_rating').fadeIn();
  });
  $('.modal_rating1__open').click(function() {
    $('.modal_rating1').fadeIn();
  });
  $('.modal_inappropriate__open').click(function() {
    $('.modal_inappropriate').fadeIn();
  });

  $('.modal_publish__open').click(function() {
    $('.modal_publish').fadeIn();
  });
  $('.modal_request__open').click(function() {
    $('.modal_request').fadeIn();
  });
  $('.modal_not-found__open').click(function() {
    $('.modal_not-found').fadeIn();
  });

  $(document).on('click touchstart', function(event) {
    if ($(event.target).closest('.modal__box__wrap').length 
      || $(event.target).closest('.modal__box__errors').length
      || $(event.target).closest('.modal__bell').length
      || $(event.target).closest('.modal__rating').length
      || $(event.target).closest('.modal__inappropriate').length
      || $(event.target).closest('.modal__assistant').length
      || $(event.target).closest('.modal__offer').length
      || $(event.target).closest('.btn__open__modal').length ) return;
      $('.modal').fadeOut();
      event.stopPropagation();
  });

  // Table link
  // $('.clickable-row').click(function() {
  //   window.location = $(this).data("href");
  // });

  $('.clickable-row, .clickable-row a').on('click', function(){
  if($(this).hasClass('table__btn') /* || $(this).hasClass('table__link') */){
      return false;
    }
    else {
      window.location = $(this).data("href");
    }
  });

  // Switch
  $('.c-settings__switch_entity').click(function() {
    $('.c-settings__form__step__one').hide();
    $('.c-settings__entity').show();
  });

  $('.c-settings__switch_individual').click(function() {
    $('.c-settings__form__step__one').hide();
    $('.c-settings__individual').show();
  });

  // Sip
  $('.c-settings__office__sip__link').click(function(e) {
    e.preventDefault();
    $('.c-settings__office__data').slideToggle( "slow", function() {});
  })

  // Regions
  $('.c-settings__regions__item__box').click(function() {
    $(this).closest('.c-settings__regions__item__box').next().slideToggle("slow");
    let checkbox = $(this).find('input[name="check"]');
    !checkbox.prop('checked') ? checkbox.prop('checked', true) : checkbox.prop('checked', false)
  });

  // Stars
  $('.stars__icon_click').click(function() {
    $(this).toggleClass('stars__icon_orange');
  });

  // Arbitration nav type
  $('.arbitration__nav__type__item').click(function(e) {
    e.preventDefault();
    $('.arbitration__nav__type__item').removeClass('arbitration__nav__type__item_active');
    $(this).addClass('arbitration__nav__type__item_active');    
  });

  // Arbitration nav
  $('.arbitration__nav__link').click(function(e) {
    e.preventDefault();
    $('.arbitration__nav__link').removeClass('arbitration__nav__link_active');
    $(this).addClass('arbitration__nav__link_active');
  });

  // Show arbitration nav
  $('.arbitration__nav__toggle').click(function() {
    $(this).toggleClass('active');
    $('body').toggleClass('no-scroll');
    $('.arbitration__nav__box').toggleClass('arbitration__nav__box_open');
  });

  $('.arbitration__nav__link').click(function() {
    $('.arbitration__nav__box').removeClass('arbitration__nav__box_open');
    $('.arbitration__nav__toggle').removeClass('active');
    $('body').removeClass('no-scroll');
  });

  $(document).click(function(event) {
    if ($(event.target).closest('.arbitration__nav__toggle').length 
      || $(event.target).closest('.arbitration__nav__box').length ) return;
      $('.arbitration__nav__box').removeClass('arbitration__nav__box_open');
      $('.arbitration__nav__toggle').removeClass('active');
      $('body').removeClass('no-scroll');
      event.stopPropagation();
  });

  // Switch money
  $('.money__switch__transfer').click(function() {
    $('.money__way__form').hide();
    $('.money__way_transfer').show();
  });

  $('.money__switch__yd').click(function() {
    $('.money__way__form').hide();
    $('.money__way_yd').show();
  });

  $('.money__switch__wb').click(function() {
    $('.money__way__form').hide();
    $('.money__way_webmoney').show();
  });

  $('.money__switch__qiwi').click(function() {
    $('.money__way__form').hide();
    $('.money__way_qiwi').show();
  });

  // Validation form
  $('.room-validation').submit(function(e) {
    e.preventDefault();
    var valid = 0;
    var room_title = $('.room_title').val();
    var room_activity = $('.room_activity').val();
    var room_criteria = $('.room_criteria').val();
    
    $('.form__input_error__item').remove();
    $('.form__input').removeClass('form__input_error');

    if (room_title.length < 1) {
      $('.room_title').parents('.form__input').addClass('form__input_error');
      $('.room_title').after('<span class="form__input_error__item">ааЕаПбаАаВаИаЛбаНаО баКаАаЗаАаНаО аНаАаЗаВаАаНаИаЕ аКаОаМаНаАбб</span>');
      valid = 1;
    }
    if (room_activity.length < 1) {
      $('.room_activity').parents('.form__input').addClass('form__input_error');
      $('.room_activity').after('<span class="form__input_error__item">ааЕаПбаАаВаИаЛбаНаО баКаАаЗаАаНаА ббаЕбаА аДаЕббаЕаЛбаНаОббаИ</span>');
      valid = 1;
    }
    if (room_criteria.length < 1) {
      $('.room_criteria').parents('.form__input').addClass('form__input_error');
      $('.room_criteria').after('<span class="form__input_error__item">ааЕаПбаАаВаИаЛбаНаО аЗаАаПаОаЛаНаЕаНаО ТЋабаИбаЕбаИаЙ т1ТЛ</span>');
      valid = 1;
    }
    if (valid != 0) {
      return false
    }

  });

  // Party form
  $('.party-validation').submit(function(e) {
    e.preventDefault();
    var valid = 0;
    var email = $('.form_email').val();
    
    $('.form__input_error__item').remove();
    $('.form__input').removeClass('form__input_error');

    if (email.length < 1) {
      $('.form_email').parents('.form__input').addClass('form__input_error');
      $('.form_email').after('<span class="form__input_error__item">ааАаПаОаЛаНаИбаЕ аПаОаЛаЕ e-mail</span>');
      valid = 1;
    } else {
      var regEx = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;
      var validEmail = regEx.test(email);
      if (!validEmail) {
        $('.form_email').after('<span class="form__input_error__item">ааЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН e-mail</span>');
        valid = 1;
      }
    }
    if (valid != 0) {
      return false
    }

  });

  // Login form
  $('.login-validation').submit(function(e) {
    e.preventDefault();
    var valid = 0;
    var email = $('.form_email').val();
    var pass = $('.form_pass').val();
    
    $('.form__input_error__item').remove();
    $('.form__input').removeClass('form__input_error');

    if (email.length < 1) {
      $('.form_email').parents('.form__input').addClass('form__input_error');
      $('.form_email').after('<span class="form__input_error__item">ааАаПаОаЛаНаИбаЕ аПаОаЛаЕ e-mail</span>');
      valid = 1;
    } else {
      var regEx = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;
      var validEmail = regEx.test(email);
      if (!validEmail) {
        $('.form_email').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН e-mail аИаЛаИ аПаАбаОаЛб</span>');
        valid = 1;
      }
    }
    if (pass.length < 1) {
      $('.form_pass').parents('.form__input').addClass('form__input_error');
      valid = 1;
    }
    if (valid != 0) {
      return false
    }

  });

  // Login form
  $('.registration-validation').submit(function(e) {
    e.preventDefault();
    var valid = 0;
    var email = $('.form_email').val();
    var pass = $('.form_pass').val();
    var pass1 = $('.form_pass1').val();
    var user_name = $('.form_user').val();
    
    $('.form__input_error__item').remove();
    $('.form__input').removeClass('form__input_error');

    if (email.length < 1) {
      $('.form_email').parents('.form__input').addClass('form__input_error');
      $('.form_email').after('<span class="form__input_error__item">ааАаПаОаЛаНаИбаЕ аПаОаЛаЕ e-mail</span>');
      valid = 1;
    } else {
      var regEx = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;
      var validEmail = regEx.test(email);
      if (!validEmail) {
        $('.form_email').after('<span class="form__input_error__item">ааЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН e-mail</span>');
        valid = 1;
      }
    }
    if (pass.length < 1) {
      $('.form_pass').parents('.form__input').addClass('form__input_error');
      $('.form_pass').after('<span class="form__input_error__item">аПаАбаОаЛаИ аНаЕ баОаВаПаАаДаАбб</span>');
      valid = 1;
    }
    if (pass1.length < 1) {
      $('.form_pass1').parents('.form__input').addClass('form__input_error');
      $('.form_pass1').after('<span class="form__input_error__item">аПаАбаОаЛаИ аНаЕ баОаВаПаАаДаАбб</span>');
      valid = 1;
    }
    if (user_name.length < 1) {
      $('.form_user').parents('.form__input').addClass('form__input_error');
      $('.form_user').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаНаО аИаМб</span>');
      valid = 1;
    }
    if (valid != 0) {
      return false
    }

  });

  // Change-password form
  $('.change-password-validation').submit(function(e) {
    e.preventDefault();
    var valid = 0;
    var pass = $('.form_pass').val();
    var pass1 = $('.form_pass1').val();
    
    $('.form__input_error__item').remove();
    $('.form__input').removeClass('form__input_error');

    if (pass.length < 1) {
      $('.form_pass').parents('.form__input').addClass('form__input_error');
      $('.form_pass').after('<span class="form__input_error__item">аПаАбаОаЛаИ аНаЕ баОаВаПаАаДаАбб</span>');
      valid = 1;
    }
    if (pass1.length < 1) {
      $('.form_pass1').parents('.form__input').addClass('form__input_error');
      valid = 1;
    }
    if (valid != 0) {
      return false
    }

  });

  // Mail request form
  $('.mail__form').submit(function(e) {
    e.preventDefault();
    var valid = 0;
    var email = $('.form_email').val();
    
    $('.form__input_error__item').remove();
    $('.form__input').removeClass('form__input_error');

    if (email.length < 1) {
      $('.form_email').parents('.form__input').addClass('form__input_error');
      $('.form_email').after('<span class="form__input_error__item">аЂаАаКаОаЙ email аНаЕ аЗаАбаЕаГаИббаИбаОаВаАаН аНаА аПаЛаАббаОбаМаЕ</span>');
      valid = 1;
    } else {
      var regEx = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;
      var validEmail = regEx.test(email);
      if (!validEmail) {
        $('.form_email').after('<span class="form__input_error__item">ааЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН e-mail</span>');
        valid = 1;
      }
    }
    if (valid != 0) {
      return false
    }

  });

  // аЁ-settings__entity
  $('.c-settings__entity').submit(function(e) {
    e.preventDefault();
    var valid = 0;
    var c_inn = $('.c_inn').val();
    var c_name_min = $('.c_name_min').val();
    var c_name_full = $('.c_name_full').val();
    var c_cpp = $('.c_cpp').val();
    var c_ogrn = $('.c_ogrn').val();
    var c_ur_adres = $('.c_ur_adres').val();
    var c_index = $('.c_index').val();
    var c_score = $('.c_score').val();
    var c_bik = $('.c_bik').val();
    
    $('.form__input_error__item').remove();
    $('.form__input').removeClass('form__input_error');

    if (c_inn.length < 1) {
      $('.c_inn').parents('.form__input').addClass('form__input_error');
      $('.c_inn').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН ааа</span>');
      valid = 1;
    }
    if (c_name_min.length < 1) {
      $('.c_name_min').parents('.form__input').addClass('form__input_error');
      $('.c_name_min').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаНаО аНаАаЗаВаАаНаИаЕ аОбаГаАаНаИаЗаАбаИ</span>');
      valid = 1;
    }
    if (c_name_full.length < 1) {
      $('.c_name_full').parents('.form__input').addClass('form__input_error');
      $('.c_name_full').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаНаО аНаАаЗаВаАаНаИаЕ аОбаГаАаНаИаЗаАбаИ</span>');
      valid = 1;
    }
    if (c_cpp.length < 1) {
      $('.c_cpp').parents('.form__input').addClass('form__input_error');
      $('.c_cpp').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН ааа</span>');
      valid = 1;
    }
    if (c_ogrn.length < 1) {
      $('.c_ogrn').parents('.form__input').addClass('form__input_error');
      $('.c_ogrn').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН ааа а</span>');
      valid = 1;
    }
    if (c_ur_adres.length < 1) {
      $('.c_ur_adres').parents('.form__input').addClass('form__input_error');
      $('.c_ur_adres').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН ббаИаДаИбаЕбаКаИаЙ аАаДбаЕб</span>');
      valid = 1;
    }
    if (c_index.length < 1) {
      $('.c_index').parents('.form__input').addClass('form__input_error');
      $('.c_index').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН аИаНаДаЕаКб</span>');
      valid = 1;
    }
    if (c_score.length < 1) {
      $('.c_score').parents('.form__input').addClass('form__input_error');
      $('.c_score').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН баАббаЕбаНбаЙ ббаЕб</span>');
      valid = 1;
    }
    if (c_bik.length < 1) {
      $('.c_bik').parents('.form__input').addClass('form__input_error');
      $('.c_bik').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН ааа</span>');
      valid = 1;
    }
    if (valid != 0) {
      return false
    }

  });

  // аЁ-settings__individual
  $('.c-settings__individual').submit(function(e) {
    e.preventDefault();
    var valid = 0;
    var c_fio = $('.c_fio').val();
    var c_date_birth = $('.c_date_birth').val();
    var c_passport = $('.c_passport').val();
    var c_issued = $('.c_issued').val();
    var c_issued_date = $('.c_issued_date').val();
    var c_adres = $('.c_adres').val();
    var c_index1 = $('.c_index1').val();
    
    $('.form__input_error__item').remove();
    $('.form__input').removeClass('form__input_error');

    if (c_fio.length < 1) {
      $('.c_fio').parents('.form__input').addClass('form__input_error');
      $('.c_fio').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаНаО аЄаа</span>');
      valid = 1;
    }
    if (c_date_birth.length < 1) {
      $('.c_date_birth').parents('.form__input').addClass('form__input_error');
      $('.c_date_birth').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаНаА аДаАбаА баОаЖаДаЕаНаИб</span>');
      valid = 1;
    }
    if (c_passport.length < 1) {
      $('.c_passport').parents('.form__input').addClass('form__input_error');
      $('.c_passport').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаНаА баЕбаИб аИ аНаОаМаЕб</span>');
      valid = 1;
    }
    if (c_issued.length < 1) {
      $('.c_issued').parents('.form__input').addClass('form__input_error');
      $('.c_issued').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаНаО аПаОаЛаЕ</span>');
      valid = 1;
    }
    if (c_issued_date.length < 1) {
      $('.c_issued_date').parents('.form__input').addClass('form__input_error');
      $('.c_issued_date').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаНаА аДаАбаА аВбаДаАбаИ</span>');
      valid = 1;
    }
    if (c_adres.length < 1) {
      $('.c_adres').parents('.form__input').addClass('form__input_error');
      $('.c_adres').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН аАаДбаЕб аПбаОаПаИбаКаИ</span>');
      valid = 1;
    }
    if (c_index1.length < 1) {
      $('.c_index1').parents('.form__input').addClass('form__input_error');
      $('.c_index1').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН аИаНаДаЕаКб</span>');
      valid = 1;
    }
    if (valid != 0) {
      return false
    }

  });

  // C-settings__office__form
  $('.c-settings__office').submit(function(e) {
    e.preventDefault();
    var valid = 0;
    var c_tel = $('.c_tel').val();
    
    $('.form__input_error__item').remove();
    $('.form__input').removeClass('form__input_error');

    if (c_tel.length < 1) {
      $('.c_tel').parents('.form__input').addClass('form__input_error');
      $('.c_tel').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН баЕаЛаЕбаОаН</span>');
      valid = 1;
    }
    
    if (valid != 0) {
      return false
    }

  });

  // P-about__form
  $('.p-about__form').submit(function(e) {
    e.preventDefault();
    var valid = 0;
    var p_skype = $('.p_skype').val();
    var p_vk = $('.p_vk').val();
    var p_fb = $('.p_fb').val();
    var p_tg = $('.p_tg').val();
    
    $('.form__input_error__item').remove();
    $('.form__input').removeClass('form__input_error');

    if (p_skype.length < 1) {
      $('.p_skype').parents('.form__input').addClass('form__input_error');
      $('.p_skype').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН skype</span>');
      valid = 1;
    }
    if (p_vk.length < 1) {
      $('.p_vk').parents('.form__input').addClass('form__input_error');
      $('.p_vk').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН vk</span>');
      valid = 1;
    }
    if (p_fb.length < 1) {
      $('.p_fb').parents('.form__input').addClass('form__input_error');
      $('.p_fb').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН facebook</span>');
      valid = 1;
    }
    if (p_tg.length < 1) {
      $('.p_tg').parents('.form__input').addClass('form__input_error');
      $('.p_tg').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН telegram</span>');
      valid = 1;
    }
    
    if (valid != 0) {
      return false
    }

  });

  // P-security__form
  $('.p-security__form').submit(function(e) {
    e.preventDefault();
    var valid = 0;
    var p_pass = $('.p_pass').val();
    var p_pass1 = $('.p_pass1').val();
    var p_pass2 = $('.p_pass2').val();
    
    $('.form__input_error__item').remove();
    $('.form__input').removeClass('form__input_error');

    if (p_pass.length < 1) {
      $('.p_pass').parents('.form__input').addClass('form__input_error');
      $('.p_pass').after('<span class="form__input_error__item">ббаАббаЙ аПаАбаОаЛб аНаЕаВаЕбаЕаН</span>');
      valid = 1;
    }
    if (p_pass1.length < 1) {
      $('.p_pass1').parents('.form__input').addClass('form__input_error');
      valid = 1;
    }
    if (p_pass2.length < 1) {
      $('.p_pass2').parents('.form__input').addClass('form__input_error');
      $('.p_pass2').after('<span class="form__input_error__item">аПаАбаОаЛаИ аНаЕ баОаВаПаАаДаАбб</span>');
      valid = 1;
    }
    
    if (valid != 0) {
      return false
    }

  });

  // P-leads add-leads
  $('.add-leads').submit(function(e) {
    e.preventDefault();
    var valid = 0;
    var l_name = $('.l_name').val();
    var l_tel = $('.l_tel').val();
    var l_date = $('.l_date').val();
    var l_txt = $('.l_txt').val();
    
    $('.form__input_error__item').remove();
    $('.form__input').removeClass('form__input_error');

    if (l_name.length < 1) {
      $('.l_name').parents('.form__input').addClass('form__input_error');
      $('.l_name').after('<span class="form__input_error__item">ааЕаПбаАаВаИаЛбаНаО баКаАаЗаАаНаО аИаМб</span>');
      valid = 1;
    }
    if (l_tel.length < 1) {
      $('.l_tel').parents('.form__input').addClass('form__input_error');
      $('.l_tel').after('<span class="form__input_error__item">ааЕаПбаАаВаИаЛбаНаО аЗаАаПаОаЛаНаЕаН баЕаЛаЕбаОаН <br /><span>баОбаМаАб +7 (900) 000-00-00</span></span>');
      valid = 1;
    }
    if (l_date.length < 1) {
      $('.l_date').parents('.form__input').addClass('form__input_error');
      $('.l_date').after('<span class="form__input_error__item">ааЕаПбаАаВаИаЛбаНаО баКаАаЗаАаНаА аДаАбаА <br />баОбаМаАб аДаД.аМаМ.аГаГаГаГ</span>');
      valid = 1;
    }
    if (l_txt.length < 1) {
      $('.l_txt').parents('.form__input').addClass('form__input_error');
      $('.l_txt').after('<span class="form__input_error__item">ааАаКбаИаМаАаЛбаНаОаЕ аКаОаЛаИбаЕббаВаО баИаМаВаОаЛаОаВ - 3000</span>');
      valid = 1;
    }
    
    if (valid != 0) {
      return false
    }

  });

  // Score__replenish__form 
  $('.score__replenish__form').submit(function(e) {
    e.preventDefault();
    var valid = 0;
    var sc_rep = $('.sc_rep').val();
    var sc_tel = $('.sc_tel').val();
    
    $('.form__input_error__item').remove();
    $('.form__input').removeClass('form__input_error');

    if (sc_rep.length < 1) {
      $('.sc_rep').parents('.form__input').addClass('form__input_error');
      $('.sc_rep').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаНаА ббаМаМаА</span>');
      valid = 1;
    }
    if (sc_tel.length < 1) {
      $('.sc_tel').parents('.form__input').addClass('form__input_error');
      $('.sc_tel').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН баЕаЛаЕбаОаН</span></span>');
      valid = 1;
    }
    
    if (valid != 0) {
      return false
    }

  });

  // Promo
  $('.promo__form').submit(function(e) {
    e.preventDefault();
    var valid = 0;
    var pr_code = $('.pr_code').val();
    
    $('.form__input_error__item').remove();
    $('.form__input').removeClass('form__input_error');

    if (pr_code.length < 1) {
      $('.pr_code').parents('.form__input').addClass('form__input_error');
      $('.pr_code').after('<span class="form__input_error__item">аПбаОаМаО-аКаОаД аНаЕаДаЕаЙббаВаИбаЕаЛаЕаН</span>');
      valid = 1;
    }
    
    if (valid != 0) {
      return false
    }

  });

  // Money way transfer
  $('.money__way_transfer').submit(function(e) {
    e.preventDefault();
    var valid = 0;
    var way_inn = $('.way_inn').val();
    var way_org = $('.way_org').val();
    var way_org1 = $('.way_org1').val();
    var way_kpp = $('.way_kpp').val();
    var way_ogrn = $('.way_ogrn').val();
    var way_sc = $('.way_sc').val();
    var way_tel = $('.way_tel').val();
    var way_mail = $('.way_mail').val();
    var way_address = $('.way_address').val();
    var way_in = $('.way_in').val();
    var way_bik = $('.way_bik').val();
    
    $('.form__input_error__item').remove();
    $('.form__input').removeClass('form__input_error');

    if (way_inn.length < 1) {
      $('.way_inn').parents('.form__input').addClass('form__input_error');
      $('.way_inn').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН ааа</span>');
      valid = 1;
    }
    if (way_org.length < 1) {
      $('.way_org').parents('.form__input').addClass('form__input_error');
      $('.way_org').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаНаО аНаАаЗаВаАаНаИаЕ аОбаГаАаНаИаЗаАбаИ</span>');
      valid = 1;
    }
    if (way_org1.length < 1) {
      $('.way_org1').parents('.form__input').addClass('form__input_error');
      $('.way_org1').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаНаО аНаАаЗаВаАаНаИаЕ аОбаГаАаНаИаЗаАбаИ</span>');
      valid = 1;
    }
    if (way_kpp.length < 1) {
      $('.way_kpp').parents('.form__input').addClass('form__input_error');
      $('.way_kpp').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН ааа</span>');
      valid = 1;
    }
    if (way_ogrn.length < 1) {
      $('.way_ogrn').parents('.form__input').addClass('form__input_error');
      $('.way_ogrn').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН ааа а</span>');
      valid = 1;
    }
    if (way_sc.length < 1) {
      $('.way_sc').parents('.form__input').addClass('form__input_error');
      $('.way_sc').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН баАббаЕбаНбаЙ ббаЕб</span>');
      valid = 1;
    }
    if (way_tel.length < 1) {
      $('.way_tel').parents('.form__input').addClass('form__input_error');
      $('.way_tel').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН баЕаЛаЕбаОаН</span>');
      valid = 1;
    }
    if (way_mail.length < 1) {
      $('.way_mail').parents('.form__input').addClass('form__input_error');
      $('.way_mail').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН e-mail</span>');
      valid = 1;
    }
    if (way_address.length < 1) {
      $('.way_address').parents('.form__input').addClass('form__input_error');
      $('.way_address').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН ббаИаДаИбаЕбаКаИаЙ аАаДбаЕб</span>');
      valid = 1;
    }
    if (way_in.length < 1) {
      $('.way_in').parents('.form__input').addClass('form__input_error');
      $('.way_in').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН аИаНаДаЕаКб</span>');
      valid = 1;
    }
    if (way_bik.length < 1) {
      $('.way_bik').parents('.form__input').addClass('form__input_error');
      $('.way_bik').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН ааа</span>');
      valid = 1;
    }
    
    if (valid != 0) {
      return false
    }

  });

  // Money way yandex
  $('.money__way_yd').submit(function(e) {
    e.preventDefault();
    var valid = 0;
    var way_yd = $('.way_yd').val();
    
    $('.form__input_error__item').remove();
    $('.form__input').removeClass('form__input_error');

    if (way_yd.length < 1) {
      $('.way_yd').parents('.form__input').addClass('form__input_error');
      $('.way_yd').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН аКаОбаЕаЛаЕаК аЏаНаДаЕаКб.ааИбаЕаКб</span>');
      valid = 1;
    }
    
    if (valid != 0) {
      return false
    }

  });

  // Money way webmoney
  $('.money__way_webmoney').submit(function(e) {
    e.preventDefault();
    var valid = 0;
    var way_wb = $('.way_wb').val();
    
    $('.form__input_error__item').remove();
    $('.form__input').removeClass('form__input_error');

    if (way_wb.length < 1) {
      $('.way_wb').parents('.form__input').addClass('form__input_error');
      $('.way_wb').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН аКаОбаЕаЛаЕаК WMR</span>');
      valid = 1;
    }
    
    if (valid != 0) {
      return false
    }

  });

  // Money way qiwi
  $('.money__way_qiwi').submit(function(e) {
    e.preventDefault();
    var valid = 0;
    var way_qiwi = $('.way_qiwi').val();
    
    $('.form__input_error__item').remove();
    $('.form__input').removeClass('form__input_error');

    if (way_qiwi.length < 1) {
      $('.way_qiwi').parents('.form__input').addClass('form__input_error');
      $('.way_qiwi').after('<span class="form__input_error__item">аНаЕаПбаАаВаИаЛбаНаО баКаАаЗаАаН аКаОбаЕаЛаЕаК QIWI</span>');
      valid = 1;
    }
    
    if (valid != 0) {
      return false
    }

  });

  // Scroll spee
  // $('.address').on('click','a', function (event) {
  //   event.preventDefault();
  //   var id  = $(this).attr('href'),
  //     top = $(id).offset().top;
  //     $('body,html').animate({scrollTop: top}, 800);
  // });

  // Show menu mobail
  // $('.navbar-toggle').click(function () {
  //   $(this).toggleClass('active');
  //   $('.nav').toggleClass('open');
  // });

  // Header add class
  // $(window).scroll(function() { 
  //   var top = $(document).scrollTop();
  //   if (top > 0) {
  //     $('.header').addClass('scroll-page');
  //   }
  //   else {
  //     $('.header').removeClass('scroll-page');
  //   }
  // });

  // Hidden nav
  // $(document).click(function(event) {
  //   if ($(event.target).closest('.navbar-toggle').length 
  //     || $(event.target).closest('.nav').length ) return;
  //     $('.nav').removeClass('open');
  //     $('.navbar-toggle').removeClass('active');
  //     event.stopPropagation();
  // });

  // Maskedinput
  // $(function($){
  //   $('.phone-mask').mask(('+7 ') + '(999) 999-99-99');
  // });

  // Accardion
  // var accordion = function() {
  //   var data = $('.accordion').attr('data-accordion')
  //   $('.accordion-header').on('click', function(){
  //     $(this).next('.accordion-body').not(':animated').slideToggle()
  //   })
  //   $('.accordion-header').click(function () {
  //     $(this).parent('.accordion li').toggleClass('active');
  //   });
  // }
  // accordion();

  // Slick sl
  // $('.class').slick({
  //   arrows: false,
  //   autoplay: true,
  //   slidesToShow: 4,
  //   autoplaySpeed: 5000,
  //   dots: false,
  //   responsive: [
  //     {
  //       breakpoint: 767,
  //       settings: {
  //         slidesToShow: 4
  //       }
  //     }
  //   ]
  // });

});

// Copy txt
function copyTxt() {
  var copyText = document.getElementById("copy-link");
  copyText.select();
  document.execCommand("copy");
}