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

// $(document).ready(function() {
//   startTime();

//   $('button').click(function() {
//     $('.pt-page-1').addClass('pt-page-scaleDown');
//     $('.pt-page-2').addClass('pt-page-scaleUpDown pt-page-delay300');
//   });
// });