$(".header__dropdown").click(function(e){
    e.preventDefault();
   $(".header__dropdown").next(".notifications__list").toggleClass("show");
});

$(".icon-logo-top").hover(function(e){
   e.preventDefault();
   $(".icon-logo-top").next(".profile").toggleClass("show");
});

$(".logo").hover(function(e){
    e.preventDefault();
   $(".icon-logo-top").next(".profile").toggleClass("show");
});

$(".icon-logo-top").click(function(e){
    e.preventDefault();
   $(".icon-logo-top").next(".profile").toggleClass("show");
});

$(document).mouseup(function (e) {
   var profile = $(".profile");
   var notificationsList = $(".notifications__list");
   if (profile.has(e.target).length === 0 && $(".icon-logo-top").has(e.target).length === 0){
      profile.removeClass("show");
   }
   if (notificationsList.has(e.target).length === 0 && $(".header__dropdown").has(e.target).length === 0 ){
      notificationsList.removeClass("show");
   }

});

$(document)
   .one('focus.autoExpand', 'textarea.autoExpand', function(){
   var savedValue = this.value;
   this.value = '';
   this.baseScrollHeight = this.scrollHeight;
   this.value = savedValue;
})
   .on('input.autoExpand', 'textarea.autoExpand', function(){
   var minRows = this.getAttribute('data-min-rows')|0, rows;
   this.rows = minRows;
   rows = Math.ceil((this.scrollHeight - this.baseScrollHeight) / 16);
   this.rows = minRows + rows;
});

$(function() {
   var Accordion = function(el, multiple) {
      this.el = el || {};
      // more then one submenu open?
      this.multiple = multiple || false;

      var dropdownlink = this.el.find('.dropdownlink');
      dropdownlink.on('click',
                      { el: this.el, multiple: this.multiple },
                      this.dropdown);
   };

   Accordion.prototype.dropdown = function(e) {
      var $el = e.data.el,
          $this = $(this),
          //this is the ul.submenuItems
          $next = $this.next();

      $next.slideToggle();
      $this.parent().toggleClass('open');

      if(!e.data.multiple) {
         //show only one menu at the same time
         $el.find('.submenuItems').not($next).slideUp().parent().removeClass('open');
      }
   };

   var accordion = new Accordion($('.accordion-menu'), false);
});

$('.tabs-nav li').click(function (e) {
   var a = $(this),
      parent = a.parents('.tabs'),
      nav = parent.children('.tabs-nav').children('li'),
      box = parent.children('.tabs-box').children('div');

   if (!a.hasClass('active')) {
      a.addClass('active')
         .siblings().removeClass('active');

      box.eq(a.index()).addClass('active')
         .siblings().removeClass('active');
   }

   e.preventDefault();
});
