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




// $('.card').eq(0).addClass('selected');
$(document).keydown(function(e) { event_corridor.call(this, e, $('.dashboard .card.selected')[0], e.which ); });
$('.dashboard').on("click", ".card",    function(e) { event_corridor(e, e.currentTarget, 'click'); });

function transfer(_from, _to)
{
  if(!_from)
  {
    _from = $('.page.page-current').attr("data-id");
    if(_from == 'home')
    {
      return false;
    }
  }

  $('.page[data-id="'+ _from+ '"]').addClass('page-scaleDown');
  $('.page[data-id="'+ _to+ '"]').addClass('page-current page-scaleUpDown page-delay300');

  setTimeout(function()
  {
    console.log('from: '+ _from +'  to: ' + _to);
    $('.page[data-id="'+ _from+ '"]').removeClass('page-current page-scaleDown');
    $('.page[data-id="'+ _to+ '"]').removeClass('page-scaleUpDown page-delay300');
    // remove current page from all except new page
    $('.page:not([data-id="'+_to+'"])').removeClass('page-current');

  }, 700);
}
