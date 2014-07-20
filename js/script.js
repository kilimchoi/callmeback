/**
 * script.js
 * 
 */
// Typeahead


var substringMatcher = function(strs) {
  return function findMatches(q, cb) {
    var matches, substrRegex;
    console.log('Enters the substringMatcher');
    // an array that will be populated with substring matches
    matches = [];
 
    // regex used to determine if a string contains the substring `q`
    substrRegex = new RegExp(q, 'i');
 
    // iterate through the pool of strings and for any string that
    // contains the substring `q`, add it to the `matches` array
    $.each(strs, function(i, str) {
      if (substrRegex.test(str)) {
        // the typeahead jQuery plugin expects suggestions to a
        // JavaScript object, refer to typeahead docs for more info
        matches.push({ value: str });
      }
    });
 
    cb(matches);
  };
};
 
var states = ['Comcast', 'DMV', 'Verizon Wireless', "PG&E", "Time Warner Cable"];
 
$('#outbound').typeahead({
  hint: true,
  highlight: true,
  minLength: 1
},
{
  name: 'states',
  displayKey: 'value',
  source: substringMatcher(states)
});




var sequence = [];

function appendOptions (event) {
  console.log("does it enter appendOptions?")
  $(event.target).parent().nextAll().remove()
  sequence = sequence.slice(0, $(this).parent().index() - 3);
  sequence.push($(this).find('span.num').html());
  var options = $(this).data('options');
  console.log('unpack options', options);
  if (options) {
    var op_li = $('<li/>').addClass('optgroup');
    for (var option in options) {
      var new_a = $('<a href="#"><span class="num">'+options[option].button+'</span> '+options[option].title+'</a>').data('options', options[option].options).click(appendOptions);
      new_a.appendTo(op_li);
    }
    $('ul.form-fields').append(op_li);
  }
  else {
    optionsComplete();
  }
  event.preventDefault();
  return false;
}

function optionsComplete() {
  var new_button = $('<li><label></label><a href="#" class="text-input btn btn-primary" id="submit">Call</a></li>');
  new_button.appendTo('ul.form-fields');

  // Submit data
  $('#submit').on('click', function(e){
	
    // Build the payload
    var payload = {};
    $('form#app').find("input, textarea, .text-input").each(function() {
      console.log("this.name is:", this.name);
      if (this.name === "outbound") {
        payload[this.name] = '4084776294' //comcast customer service number
      } else { 
        payload[this.name] = $(this).val();
      }
    });
    payload['phone'] = '+1 ' + payload['phone'];
    payload['sequence'] = sequence;
    payload = JSON.stringify(payload);

    // Hide field entry
    $('#intro').fadeOut();
    $('.form-fields li').each(function(){ $(this).fadeOut(); });

	/*
    // POST to the application
    var jqxhr = $.ajax({
      type: 'POST',
      url: 'index.php/user/call',
      contentType: "application/json;charset=UTF-8",  // request
      data: payload,
      accepts: "application/json",  // response
      cache: false
    })
    .done(function() {
      console.log('done.');
      $('.wrapper').append("<p>Success! Call Me Maybe will give you a call when the rep is on the line. And keep an eye on your email for the conversation.</p>");
    })
    .fail(function(response) {
      console.log('fail.');
      $('.form-fields li').each(function(){ $(this).fadeIn(); });
      $('.form-fields li').last().append("<label>Something bad happened :(</label>");
    })
    .always(function(response) {
      console.log('request: '  + payload);
      console.log('response: ' + response);
    });
	*/
  
  $('#loading_image').show(); // show animation
  document.getElementById("app").submit();
    

  });

}

// Serialize form for submission as JSON object.
//   Snippet via http://jsfiddle.net/sxGtM/3/
$.fn.serializeObject = function(){
  var o = {};
  var a = this.serializeArray();
  $.each(a, function() {
    if (o[this.name] !== undefined) {
      if (!o[this.name].push) {
          o[this.name] = [o[this.name]];
      }
      o[this.name].push(this.value || '');
    } else {
      o[this.name] = this.value || '';
    }
  });
  return o;
};

$(document).ready(function(){

  // Faster scrolling
  //   via <thecssninja.com/javascript/pointer-events-60fps>.
  var body = document.body, timer;
  window.addEventListener('scroll', function(){
    clearTimeout(timer);
    if(!body.classList.contains('disable-hover'))
      body.classList.add('disable-hover')
    timer = setTimeout(function(){
      body.classList.remove('disable-hover')
    }, 500);
  }, false);

  // General focus forward/back
  $("form#app li").focusin(function(){
    $(this).removeClass('hidden');
    $(this).addClass('active');
    var target = $(this).find('.text-input').first();
    $.smoothScroll({
      offset: -focusHeight,
      scrollTarget: target,
      afterScroll: focus(target),
      speed: 200
    });
    $(this).nextAll('li').addClass('hidden');
    console.log('form#app li focusin() done.')
  });
  var phoneCheckmarkSpan = document.createElement('span');
  var emailCheckmarkSpan = document.createElement('span');
  var outboundCheckmarkSpan = document.createElement('span');
  $("form#app li").focusout(function(){
    $(this).removeClass('active');
    
    if ($("#phone").val() != "" && phoneCheckmarkSpan.innerHTML.length == 0) {
      console.log("enters phone validation.");
      var phone = document.getElementById('li_phone');
      var spanIdName = 'checkmark';
      phoneCheckmarkSpan.setAttribute('id', spanIdName);
      phoneCheckmarkSpan.setAttribute('style', 'color: green')
      phoneCheckmarkSpan.innerHTML = "✓";
      console.log(phoneCheckmarkSpan);
      phone.appendChild(phoneCheckmarkSpan);
    } else if ($("#email").val() != "" && emailCheckmarkSpan.innerHTML.length == 0) {
      var email = document.getElementById('li_email');
      var spanIdName = 'checkmark';
      emailCheckmarkSpan.setAttribute('id', spanIdName);
      emailCheckmarkSpan.setAttribute('style', 'color: green')
      emailCheckmarkSpan.innerHTML = "✓";
      console.log(emailCheckmarkSpan);
      email.appendChild(emailCheckmarkSpan);
    } else if ($("#outbound").val() != "" && outboundCheckmarkSpan.innerHTML.length == 0) {
      var outbound = document.getElementById('li_outbound');
      var spanIdName = 'checkmark';
      outboundCheckmarkSpan.setAttribute('id', spanIdName);
      outboundCheckmarkSpan.setAttribute('style', 'color: green')
      outboundCheckmarkSpan.innerHTML = "✓";
      console.log(outboundCheckmarkSpan);
      outbound.appendChild(outboundCheckmarkSpan);
    }
    console.log('form#app li focusout() done.')
  });


  $('.form-fields .next').on('click', function(e){
    console.log('button click will result in focus.');
    $(this).parentsUntil('li.active').first().parent()
      .next('li.hidden .text-input').focus();
  });
  $(document).on('typeahead:opened', function() {
    $('.company-not-found').html("");
  });
  $(document).on('typeahead:closed', function(){
    console.log('typeahead:closed');
    $('li.active').removeClass('active')
      .next('li').find('.text-input').first()
      .focus();
    console.log("Did it remove?");
    /* Retrieves the list of companies' names. */
    var company_name = $('#outbound').val();
    $(this).next('li').removeClass('hidden');
    console.log("about to call function to create call button.");
    optionsComplete();
    /* End the company retrieval. */

  });
  $('.form-fields .text-input').bind('keydown', function(event){
    if((event.keyCode==9 && event.shiftKey) || event.keyCode==38) {
      
      $(this).parent().prev('li').trigger('focusin');
     
    } else if(event.keyCode==9 || event.keyCode==13 || event.keyCode==40) {
      
      var dataNext = $(this).data('next');;
      //console.log("dataNext is: ", dataNext);
      if(!(dataNext=="")) {
        $(this).parent().trigger('focusout');
        
        $(dataNext).trigger('focusin');
      } 
     
    } 
  });

  // Initialize state
  var focusHeight = $('.form-fields input').first().offset().top;
  console.log('focusHeight = ' + focusHeight);
  
  $('form#app>ul>li').first()
    .removeClass('.hidden')
    .addClass('active');
  $('form#app .text-input').first().focus();

  // Mask input
  $("[type='tel']").mask("(999) 999-9999", {placeholder: " ", completed: function(){
    $(this).parent().next('li')
      .removeClass('hidden')
      .find('.text-input').first().focus();
  }});

});
