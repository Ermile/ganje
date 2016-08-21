function startTime() {
  var today = new Date();
  var h     = today.getHours();
  var m     = today.getMinutes();
  var s     = today.getSeconds();

  m = checkTime(m);
  s = checkTime(s);

  $('.hour').html(h);
  $('.minute').html(m);
  $('.second').html(s);

  var t = setTimeout(startTime, 500);
}

function checkTime(i) {
  if (i < 10) {
    i = "0" + i
  };
  return i;
}

$(document).ready(function() {
  startTime();
});


var isAnimation = false;


// $('.card').eq(0).addClass('selected');
$(document).keydown(function(e) { event_corridor.call(this, e, $('.dashboard .card.selected')[0], e.which ); });
$('.dashboard').on("click", ".card",    function(e) { event_corridor(e, e.currentTarget, 'click'); });

$('body').attr('data-location', 'dashboard' );

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


function changePerson(_id)
{
  $('.dashboard .card:not([data-id="'+_id+'"])').removeClass('selected');
  $('.dashboard .card[data-id="'+ _id+ '"]').addClass('selected');
}
