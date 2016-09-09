// declare variables
var isAnimation = false;

$(document).ready(function()
{
  startTime();
  datatable_fill();
  // reload page every 7 min to disallow session closing
  setTimeout(function () { location.reload(1); }, 420000);

});

// bind keydown and click
$(document).keydown(function(e) { event_corridor.call(this, e, $('.dashboard .card.selected')[0], e.which ); });
$(document).on("click", ".card", function(e) { event_corridor(e, e.currentTarget, 'click'); });
$(document).on("dblclick", ".card", function(e) { event_corridor(e, e.currentTarget, 'dblclick'); });
$(document).on("dblclick", "#poem", function(e) { location.reload(); });
$(document).bind("contextmenu",function(e) { e.preventDefault(); event_corridor(e, e.currentTarget, 'rightclick'); });

$(document).on("click", ".statistics .minus", function(e) { setExtra('minus', 5) });
$(document).on("click", ".statistics .plus",  function(e) { setExtra('plus', 10) });


$(".filters .year").change(function() {generateTimeFilter();});
$(".filters .month").change(function() {generateTimeFilter();});
$(".filters .day").change(function() {generateTimeFilter();});


/**
 * generate time from filter and navigate to new address to get data
 * @return {[type]} [description]
 */
function generateTimeFilter()
{
  var year  = $(".filters .year").val();
  var month = $(".filters .month").val();
  var day   = $(".filters .day").val();
  var date  = (year? year: '0000') + '-' + (month? month: '00') + '-' + (day? day: '00');

  $('.filters').attr('data-time', date);

  // get current path
  var CURRENTPATH = (location.pathname).replace(/^\/+/, '');
  var newLocation = CURRENTPATH;
  // if url has / remove it from end of url
  if (CURRENTPATH.substr(-1) == '/')
  {
    newLocation = newLocation.substr(0, newLocation.length - 2);
  }
  // splite url with slash into array
  newLocation = newLocation.split('/');
  // get only 2 slash of url
  newLocation = '/' + newLocation[0] + '/' + newLocation[1];

  navigate(newLocation + '/date='+date);
}

/**
 * navigate to specefic url with pushstate
 * @param  {[type]} _path [description]
 * @return {[type]}       [description]
 */
function navigate(_path)
{
  Navigate({ url: _path });
}





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


// up and down minus with scrool
$('.statistics .minus').bind('mousewheel', function(e){
  if(e.originalEvent.wheelDelta /120 > 0) {
    setExtra('minus', 5);
  }
  else{
    setExtra('minus', -5);
  }
});
// up and down plus with scrool
$('.statistics .plus').bind('mousewheel', function(e){
  if(e.originalEvent.wheelDelta /120 > 0) {
    setExtra('plus', 5);
  }
  else{
    setExtra('plus', -5);
  }
});



/**
 * fill datatable with ajax request
 * @return {[type]} [description]
 */
function datatable_fill()
{
  // $('.datatable').DataTable(
  // {
  //   destroy: true,
  //   ajax: "/"
  // } );
  // console.log(111);
}

/**
 * [startTime description]
 * @return {[type]} [description]
 */
function startTime()
{
  var today = new Date();

  changetime(addZero(today.getSeconds()), 'second');
  changetime(addZero(today.getMinutes()), 'minute');
  changetime(today.getHours(), 'hour');
  var t = setTimeout(startTime,500);
}



function setExtra(_type, _increase)
{
  if(_type === false)
  {
    $('body').removeAttr('data-editing');
    // remove real value of times to zero
    $('.page .minus').attr("data-time", 0);
    $('.page .plus').attr("data-time", 0);
    // remove show value of times to zero
    $('.page .minus span').text(0);
    $('.page .plus span').text(0);
  }
  else
  {
    _increase = parseInt(_increase);
    if(_increase === undefined || isNaN(_increase))
    {
      _increase = 5;
    }
    // goto editing mode
    $('body').attr('data-editing', _type);

    // set variabels
    var _this  = $('.statistics .' + _type);

    if(_this.hasClass('minus') )
    {

    }
    else if(_this.hasClass('plus'))
    {

    }
    else
    {
      return false;
    }
    var newVal     = parseInt(_this.attr('data-time')) + _increase;
    var newValReal = newVal;
    if(newVal < 0)
    {
      newVal     = ':|';
      newValReal = 0;
    }
    // set new period for date
    _this.attr('data-time', newValReal);
    _this.children('span').text(newVal);
  }

  // finally calc total time
  calcTotalTime();
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
function changetime(_value, _class)
{
  _new = String(_value);
  // change time to persian if we are in rtl design
  if($('body').hasClass('rtl'))
  {
    // convert time to persian
    persian={0:'۰',1:'۱',2:'۲',3:'۳',4:'۴',5:'۵',6:'۶',7:'۷',8:'۸',9:'۹'};
    for(var i=0; i<=9; i++)
    {
        var re = new RegExp(i,"g");
        _new = _new.replace(re, persian[i]);
    }
  }
  // if time is not changed, return false
  if($('.time .'+ _class).attr('data-time') == _value)
  {
    return false;
  }
  // set new value with effect
  $('.time .'+ _class).html(_new).attr('data-time', _value);
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
    $('body .dashboard').attr('data-last', _from);
    // remove editing mode
    setExtra(false);
  }
  else
  {
    $('body').attr('data-location', 'personal' );
    fillTimes(_to);
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
  if($('body').attr('data-location') === 'dashboard')
  {
    $('.dashboard .card:not([data-id="'+_id+'"])').removeClass('selected');
    $('.dashboard .card[data-id="'+ _id+ '"]').addClass('selected');
  }
}


/**
 * [fillTimes description]
 * @param  {[type]} _id [description]
 * @return {[type]}     [description]
 */
function fillTimes(_id)
{
  var today  = new Date();
  var enter  = $('.page[data-id="'+ _id+ '"] .enter span').text();
  var exit   = today.getHours() + ":" + today.getMinutes();
  var tenter = String(enter).split(':');
  tenter     = new Date(today.getFullYear(), today.getMonth(), today.getDate(), tenter[0], tenter[1]);
  var diff  = Math.round((today - tenter) / 1000 / 60);

  $('.page[data-id="'+ _id+ '"] .diff span').text(diff);
  $('.page[data-id="'+ _id+ '"] .diff').attr('data-time', diff);

  calcTotalTime(_id);
}


function calcTotalTime(_id)
{
  if(_id === undefined)
  {
    _id = parseInt($('.dashboard .card.selected').attr("data-id"));
  }
  var diff       = parseInt($('.page[data-id="'+ _id+ '"] .diff').attr('data-time'));
  var minus      = parseInt($('.page[data-id="'+ _id+ '"] .minus').attr('data-time'));
  var plus       = parseInt($('.page[data-id="'+ _id+ '"] .plus').attr('data-time'));
  var total      = diff - minus + plus;
  var totalHuman = Math.floor(total / 60) + ":" + total % 60;
  if(total < 0)
  {
    totalHuman = ":/";
  }

  $('.page[data-id="'+ _id+ '"] .total span').attr('data-time', total);
  $('.page[data-id="'+ _id+ '"] .total span').text(totalHuman);
}

/**
 * [setTime description]
 * @param {[type]} _id [description]
 */
function setTime(_id)
{
  if(isAnimation)
  {
    return false;
  }
  minus = $('.page[data-id="'+ _id+ '"] .minus').attr('data-time');
  plus  = $('.page[data-id="'+ _id+ '"] .plus').attr('data-time');

  // send ajax and do best work on respnse
  $('.page.detail .statistics').ajaxify({
    ajax:
    {
      data:
      {
        userId: _id,
        minus: minus,
        plus: plus,
      },
      abort: false,
      success: function(e, data, x)
      {
        var myResult   = x.responseJSON.result;
        var elSelected = $('.dashboard .card[data-id="'+_id+'"]');
        var elStatus   = $('.page[data-id="'+_id+'"]');

        if(myResult == undefined)
        {
          return false;
        }
        else if(myResult == 'enter')
        {
          // set status for this user on dashboard
          elSelected.addClass('present');
          elStatus.attr('data-status', 'on');
        }
        else if(myResult == 'exit')
        {
          // set status for this user on dashboard
          elSelected.removeClass('present');
          elStatus.attr('data-status', 'off');
        }
        // remove selected item after setting time
        $('.dashboard .card').removeClass('selected');
      }
    }
  });

  // after set time, transfer to home
  transfer(_id, 'home');
}

