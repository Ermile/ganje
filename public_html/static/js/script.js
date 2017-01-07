// declare variables
var isAnimation = false;

$(document).ready(function()
{
  startTime();
  if($('body').hasClass('register'))
  {
    // reload page every 7 min to disallow session closing
    setTimeout(function() {location.reload(1);}, (1000*60*30));
  }
});


route('*', function()
{
  calcTotalRow();
  // up and down func of ermile table with with scrool
  $('.et tfoot td').bind('mousewheel', function(e)
  {
    var newFunc = calcNextFunc( $(this).attr('data-func') );
    $(this).attr('data-func', newFunc);
    calcTotalRow();
  });
});


function changeTypeOfRecod(_this, _field)
{
  var row      = $(_this).parents('tr');
  var recordId = row.attr('data-id');
 // send ajax and do best work on respnse
  $('.et').ajaxify({
    ajax:
    {
      data:
      {
        recordId: recordId,
        field: _field,
        type: 'change'
      },
      abort: true,
      success: function(e, data, x)
      {
        var myResult   = x.responseJSON.result;
        if(myResult == undefined)
        {
          return false;
        }
        else
        {
          row.attr('data-type', myResult);
          row.attr('data-status', calcNewStatus(myResult));
        }
      }
    }
  });
}

function calcNewStatus(_type)
{
  newStatus = null;
  switch(_type)
  {
    case 'all':
      newStatus = 'active';
      break;

    case 'base':
    case 'wplus':
    case 'wminus':
      newStatus = 'filter';
      break;

    default:
      newStatus = 'deactive';
      break;
  }

  return newStatus;
}


function addEndTime(_this)
{
  // if data is not set, then updateit
  if($(_this).text())
  {
    return false;
  }
  console.log($(_this).text());
  $(".et .val_end").removeClass('edit');
  $(_this).addClass('edit');
  $('body').addClass('editingTable');

  // $('#edit-data').copyData(this, ['modal']);


  console.log(_this);
}



/**
 * this function calculate total row of ermile tables
 * @return {[type]} [description]
 */
function calcTotalRow()
{
  $('.et tfoot td').each(function()
  {
    var func     = $(this).attr('data-func');
    var type     = $(this).attr('data-type');
    var col      = $(this).attr('class').substr(6);
    var counter  = 0;
    var result   = 0;
    var funcName = '';

    // foreach item in this row do function
    $('.et tbody .val_' + col).each( function()
    {
      var val = parseInt($(this).attr('data-val'));
      if(!val)
      {
        val = 0;
      }

      switch (func)
      {
        case 'avg':
        case 'avg-hour':
          counter += 1;
        case 'sum':
        case 'sum-hour':
        case 'total':
          result += val;
          funcName = 'Sum'
          break;

        case 'count':
        case 'count-hour':
          result += 1;
          funcName = 'Count'
          break;

        default:
          result = '-';
          funcName = '';
          break;
      }
    });
    // calc average time
    if(func === 'avg' || func === 'avg-hour')
    {
      result = Math.round(result / counter);
      funcName = 'Average';
    }
    // console.log(func);
    // console.log(type);

    // show times in hour
    if(func === 'sum-hour' || func === 'avg-hour' || type === 'time')
    {
      result = Math.floor(result/60) + ':' + Math.round(result%60);
    }

    // fill footer column with calculated value
    if($(this).html() != result && result)
    {
      $(this).html(result);
      $(this).attr('title', funcName + ': ' + result);
    }
  });
}


/**
 * [changeTotalRow description]
 * @return {[type]} [description]
 */
function changeTotalRow()
{
  $('.et tfoot td').each(function()
  {
    var func    = $(this).attr('data-func');
    var newFunc = calcNextFunc(func);

    $(this).attr('data-func', newFunc);
  });
  calcTotalRow();
}


/**
 * [calcNextFunc description]
 * @param  {[type]} _func [description]
 * @return {[type]}       [description]
 */
function calcNextFunc(_func)
{
  var newFunc = _func;
  switch (_func)
  {
    // change class of hours
    case 'count-hour':
      newFunc = 'sum-hour';
      break;

    case 'sum-hour':
      newFunc = 'avg-hour';
      break;

    case 'avg-hour':
      newFunc = 'count-hour';
      break;

    // change func
    case 'count':
      newFunc = 'sum';
      break;

    case 'sum':
      newFunc = 'avg';
      break;

    case 'avg':
      newFunc = 'count';
      break;

    default:
      newFunc = 'count';
      break;
  }
  return newFunc;
}








// bind keydown and click
$(document).keydown(function(e) { event_corridor.call(this, e, $('.dashboard .card.selected')[0], e.which ); });
$(document).on("click", ".card", function(e) { event_corridor(e, e.currentTarget, 'click'); });
$(document).on("dblclick", ".card", function(e) { event_corridor(e, e.currentTarget, 'dblclick'); });
$(document).on("dblclick", ".msg.info", function(e) { event_corridor(e, e.currentTarget, 'dblclick'); });
$(document).on("dblclick", ".time", function(e) { location.reload(); });
$(document).bind("contextmenu",function(e) { e.preventDefault(); event_corridor(e, e.currentTarget, 'rightclick'); });

// $(document).on("click", "body", function(e) {console.log(e); changePerson(0); });
$(document).on("click", ".statistics .calcremote", function(e) { setExtra('plus', $(this).attr('data-time'), true); });
$(document).on("click", ".statistics .minus", function(e) { setExtra('minus', 5) });
$(document).on("click", ".statistics .plus",  function(e) { setExtra('plus', 10) });

$(document).on("click", ".cardList .card",  function(e) { generateUserFilter(this) });
$(document).on("click", ".filters .removeFilter", function(e) { removeFilter(); });
$(document).on("click", ".back", function(e) { transfer(null, 'home'); });


$(document).on("dblclick", ".et .val_end", function(e) {addEndTime(this);});
$(document).on("dblclick", ".et .val_diff", function(e) {changeTypeOfRecod(this, 'diff');});
$(document).on("dblclick", ".et .val_plus", function(e) {changeTypeOfRecod(this, 'plus');});
$(document).on("dblclick", ".et .val_minus", function(e) {changeTypeOfRecod(this, 'minus');});
$(document).on("dblclick", ".et .val_accepted", function(e) {changeTypeOfRecod(this, 'accept');});



$(".filters .year").change(function() {generateFilter();});
$(".filters .month").change(function() {generateFilter();});
$(".filters .month").select(function() {generateFilter();});
$(".filters .day").change(function() {generateFilter();});


// up and down minus with scrool
$(document).on("click", ".filters .datepicker span", function(e) { changeTimeValue.call(this, true) });
$('.filters .datepicker span').bind('mousewheel', function(e)
{
  if(e.originalEvent.wheelDelta / 120 < 0)
  {
    changeTimeValue.call(this, false);
  }
  else
  {
    changeTimeValue.call(this, true);
  }
});

function changeTimeValue(_inc)
{
  var val         = parseInt($(this).html());
  var val_min     = parseInt($(this).attr('data-min'));
  var val_max     = parseInt($(this).attr('data-max'));
  var val_changed = parseInt($(this).attr('data-value'));
  var val_char    = parseInt($(this).attr('data-char'));
  var val_new     = val;

  // set val if is not exist use changed value if not exist use min value
  if(!val_new)
  {
    if(val_changed)
    {
      val_new = val_changed;
    }
    else
    {
      val_new = val_min -1;
    }
  }

  // on scroll up or down, increase or decrease number
  if(_inc)
  {
    val_new += 1;
  }
  else{
    val_new -= 1;
  }

  // scroll from max to min
  if(val_new > val_max && isNaN(val))
  {
    val_new = val_min;
  }
  // scroll from min to max
  if(val_new < val_min && isNaN(val))
  {
    val_new = val_max;
  }

  // change value if is valid
  if(val_new >= val_min && val_new <= val_max)
  {
    $(this).html(addZero(val_new));
    $(this).attr('data-value', addZero(val_new));
  }
  // set dash for zero value
  else if(val_new < val_min || val_new > val_max)
  {
    if(val_char == 4)
    {
      $(this).html('----');
      $(this).attr('data-value', '0000');
    }
    else
    {
      $(this).html('--');
      $(this).attr('data-value', '00');
    }
  }
  generateFilter();
}






function removeFilter()
{
  console.log('removeFilters');
  $('.cardList .card').removeClass('present');
  $('.cardList').attr('data-selected-id', null);
  $(".filters .datepicker span").attr('data-value', null);

}

function generateUserFilter(_this)
{
  var id = $(_this).attr('data-user-id');
  $('.cardList .card').not('[data-user-id='+id+']').removeClass('present');
  $(_this).toggleClass('present');

  if($(_this).hasClass('present'))
  {
    $('.cardList').attr('data-selected-id', id);
  }
  else
  {
    $('.cardList').attr('data-selected-id', null);
  }
  generateFilter();
}

function generateFilter()
{
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
  // get date filter and add
  var date = generateTimeFilter();
  if(date)
  {
    newLocation += date;
  }

  // generate user filter and add
  var user = $('.cardList').attr('data-selected-id');
  if(user)
  {
    newLocation += '/user=' + user;
  }
  navigate(newLocation);
}

/**
 * generate time from filter and navigate to new address to get data
 * @return {[type]} [description]
 */
function generateTimeFilter()
{
  var year  = $(".filters .datepicker .year").attr('data-value');
  var month = $(".filters .datepicker .month").attr('data-value');
  var day   = $(".filters .datepicker .day").attr('data-value');

  if(!year)
  {
    return null;
  }
  // create date
  var date  = (year? year: '0000') + '-' + (month? month: '00') + '-' + (day? day: '00');

  $('.filters').attr('data-time', date);
  if(date)
  {
    date = '/date='+date;
    return date;
  }

  return null;
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


/**
 * [setExtra description]
 * @param {[type]} _type     [description]
 * @param {[type]} _increase [description]
 */
function setExtra(_type, _increase, _exact)
{
  if(_type === false)
  {
    var delay = _increase === false? 0: 700;
    setTimeout(function()
    {
      $('body').removeAttr('data-editing');
      // remove real value of times to zero
      $('.page .minus').attr("data-time", 0);
      $('.page .plus').attr("data-time", 0);
      // remove show value of times to zero
      $('.page .minus span').text(0);
      $('.page .plus span').text(0);
    }, delay);
  }
  else
  {
    _increase = parseInt(_increase);
    if(_increase === undefined || isNaN(_increase))
    {
      _increase = 5;
    }

    var userStatus = $('.detail.page-current').attr('data-status');
    if(userStatus === 'off')
    {
      _increase = (_type !== 'plus')? -_increase: _increase;
      _type = 'plus';
    }
    else if(userStatus === 'on')
    {
      _increase = (_type !== 'minus')? -_increase: _increase;
      _type = 'minus';
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
    var newVal = parseInt(_this.attr('data-time')) + _increase;
    if(_exact)
    {
      newVal   = _increase;
    }
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
  if($('body').hasClass('loading-form'))
  {
    return false;
  }
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

  // calc remote time on enters
  var exit_hour  = $('.page[data-id="'+ _id+ '"] .off .calcremote').attr('data-time-exit');
  exit_hour      = String(exit_hour).split(':');
  exit_hour      = new Date(today.getFullYear(), today.getMonth(), today.getDate(), exit_hour[0], exit_hour[1]);
  var remotediff = Math.round((today - exit_hour) / 1000 / 60) - 5;
  $('.page[data-id="'+ _id+ '"] .off .calcremote').attr('data-time', remotediff);

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
  if(isAnimation || $('body').hasClass('loading-form'))
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
      abort: true,
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

