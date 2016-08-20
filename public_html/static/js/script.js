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
$(document).keydown(function(e) { event_corridor.call(this, e, $('.card.selected')[0], e.which ); });


function transferUser(_id)
{
  transfer('home', _id);
  console.log(12);


}

function transferHome()
{
  transfer(2, 'home');
  console.log(34);
}

function transfer(_from, _to)
{
  $('.pt-page[data-id="'+ _from+ '"]').addClass('pt-page-current pt-page-scaleDown');
  $('.pt-page[data-id="'+ _to+ '"]').addClass('pt-page-current pt-page-scaleUpDown pt-page-delay300');

  setTimeout(function()
  {
    console.log('timeeeeeer');
    $('.pt-page[data-id="'+ _from+ '"]').removeClass('pt-page-scaleDown');
    $('.pt-page[data-id="'+ _to+ '"]').removeClass('pt-page-scaleUpDown pt-page-delay300');
  }, 1000);
}
