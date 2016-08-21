// declare variables
var isAnimation = false;

$(document).ready(function()
{
  startTime();
  // reload page every 7 min to disallow session closing
  setTimeout(function () { location.reload(1); }, 420000);
});

// bind keydown and click
$(document).keydown(function(e) { event_corridor.call(this, e, $('.dashboard .card.selected')[0], e.which ); });
$('.dashboard').on("click", ".card",    function(e) { event_corridor(e, e.currentTarget, 'click'); });
// add location to body on start
$('body').attr('data-location', 'dashboard' );
// add random class to image get random
$(".dashboard .card img").each(function()
{
  if(this.src.indexOf('/default/') > -1)
  {
    $(this).addClass('random');
  }
});



/**
 * [startTime description]
 * @return {[type]} [description]
 */
function startTime()
{
  var today = new Date();
  var h     = today.getHours();
  var m     = today.getMinutes();
  var s     = today.getSeconds();
  // add zero to min and sec
  m = addZero(m);
  s = addZero(s);

  changetime(s, 'second');
  changetime(m, 'minute');
  changetime(h, 'hour');
  var t = setTimeout(startTime,500);
}


/**
 * [addZero description]
 * @param {[type]} i [description]
 */
function addZero(i)
{
  if (i < 10)
  {
    i = "0" + i
  };
  return i;
}


/**
 * [changetime description]
 * @param  {[type]} _new   [description]
 * @param  {[type]} _class [description]
 * @return {[type]}        [description]
 */
function changetime(_new, _class)
{
  // change time to persian if we are in rtl design
  if($('body').hasClass('rtl'))
  {
    _new = String(_new);
    // convert time to persian
    persian={0:'۰',1:'۱',2:'۲',3:'۳',4:'۴',5:'۵',6:'۶',7:'۷',8:'۸',9:'۹'};
    for(var i=0; i<=9; i++)
    {
        var re = new RegExp(i,"g");
        _new = _new.replace(re, persian[i]);
    }
  }

  // if time is not changed, return false
  if($('#time .'+ _class).text() == _new)
  {
    return false;
  }
  // change second without effect
  if(_class == 'second')
  {
    $('#time .second').html(_new);
    return;
  }

  var newel = $("<span class='"+_class+"'>"+_new+"</span>").hide();
  $('#time .'+_class).replaceWith(newel);

  $('#time .'+_class).fadeOut(500, function()
  {
   // $(this).replaceWith(newel);
   $('#time .'+_class).fadeIn(1000);
  });


}


/**
 * [transfer description]
 * @param  {[type]} _from [description]
 * @param  {[type]} _to   [description]
 * @return {[type]}       [description]
 */
function transfer(_from, _to)
{
  // do not run animation twice
  if(isAnimation)
  {
    return false;
  }

  // set from value
  if(!_from)
  {
    _from = $('.page.page-current').attr("data-id");
  }

  // if want go from home to home
  if($('body').attr('data-location') == 'dashboard' && _to == 'home')
  {
    return false;
  }
  // if want go from page to another page
  else if($('body').attr('data-location') == 'personal' && _to !== 'home')
  {
    return false;
  }

  // set location on each step
  if(_to == 'home')
  {
    $('body').attr('data-location', 'dashboard' );
  }
  else
  {
    $('body').attr('data-location', 'personal' );
  }

  // start page transition animation
  isAnimation = true;
  $('.page[data-id="'+ _from+ '"]').addClass('page-scaleDown');
  $('.page[data-id="'+ _to+ '"]').addClass('page-current page-scaleUpDown page-delay300');

  // remove animation effects after some time
  setTimeout(function()
  {
    $('.page').removeClass('page-scaleDown page-scaleUpDown page-delay300');
    // remove current page from all except new page
    $('.page:not([data-id="'+_to+'"])').removeClass('page-current');
    isAnimation = false;
  }, 700);
}


/**
 * [changePerson description]
 * @param  {[type]} _id [description]
 * @return {[type]}     [description]
 */
function changePerson(_id)
{
  $('.dashboard .card:not([data-id="'+_id+'"])').removeClass('selected');
  $('.dashboard .card[data-id="'+ _id+ '"]').addClass('selected');
}


