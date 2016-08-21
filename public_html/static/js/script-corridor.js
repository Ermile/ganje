/**
 * corridor of all events on keyboard and mouse
 * @param  {[type]} e     the element that event doing on that
 * @param  {[type]} _self seperated element for doing jobs on it
 * @param  {[type]} _key  the key pressed or click or another events
 * @return {[type]}       void func not returning value! only doing job
 */
function event_corridor(e, _self, _key)
{
  _self = $(_self);
  var cid    = parseInt($('.dashboard .card.selected').attr("data-id"));
  var lastid = parseInt($('.dashboard .card').length);

  var ctrl   = e.ctrlKey  ? 'ctrl'  : '';
  var shift  = e.shiftKey ? 'shift' : '';
  var alt    = e.altKey   ? 'alt'   : '';
  var mytxt  = String(_key) + ctrl + alt + shift;
  var keyp   = String.fromCharCode(_key);
  // handle numpad
  if(_key >= 96 && _key <= 105)
  {
    keyp = _key - 96;
  }

  // select item with number
  if($('body').attr('data-location') == 'dashboard' && keyp > 0 && keyp < 9)
  {
    $('.card').removeClass('selected');
    $('.card:eq('+ (keyp-1) +')').addClass('selected');
  }


  switch(mytxt)
  {
    // ---------------------------------------------------------- BackSpace
    case '8':               // Back Space
      break;


    // ---------------------------------------------------------- Enter
    case '13':              // Enter
        var selected = $('.dashboard .card.selected').attr("data-id");
        if(selected)
        {
          transfer('home', selected);
        }
      break;


    // ---------------------------------------------------------- Escape
    case '27':              //Escape
      // $('.page').removeClass('page-current');
      // $('.page[data-id="home"]').addClass('page-current');
          transfer(null, 'home');
      break;


    // ---------------------------------------------------------- Space
    case '32':              // space
    case '32shift':         // space + shift
    case '32ctrl':          // space + ctrl
    case '32ctrlshift':     // space + ctrl + shift
      break;


    // ---------------------------------------------------------- Page Up
    case '33':              // PageUP
      break;

    // ---------------------------------------------------------- Page Down
    case '34':              // PageDown
      break;

    // ---------------------------------------------------------- End
    case '35':              // End
      break;

    // ---------------------------------------------------------- Home
    case '36':              // Home
      break;

    // ---------------------------------------------------------- Left
    case '37':              // left
      if(!cid)
      {
        _id = 1;
      }
      else
      {
        _id = cid + 1;
      }
      if(_id > lastid)
      {
        _id = 1;
      }
      $('.dashboard .card:not([data-id="'+_id+'"])').removeClass('selected');
      $('.dashboard .card[data-id="'+ _id+ '"]').addClass('selected');
      break;

    // ---------------------------------------------------------- Up
    case '38':              // up
      break;

    // ---------------------------------------------------------- Right
    case '39':              // right
      if(!cid)
      {
        _id = lastid;
      }
      else
      {
        _id = cid - 1;
      }
      if(_id < 1)
      {
        _id = lastid;
      }
      $('.dashboard .card:not([data-id="'+_id+'"])').removeClass('selected');
      $('.dashboard .card[data-id="'+ _id+ '"]').addClass('selected');
      break;

    // ---------------------------------------------------------- Down
    case '40':              // down
      break;

    // ---------------------------------------------------------- Delete
    case '46':              // delete
      break;

    // ---------------------------------------------------------------------- shortcut
    case '65ctrl':          // a + ctrl
      break;

    case '68shift':         // d + shift
      break;

    case '70':              // f
      break;

    case '72shift':         // h + shift (Home page)
      break;

    case '112':             // f1
      break;

    case '113':             // f2
      break;

    case '114':             // f3
      break;

    case '116':             // f5
      break;

   case '122shift':         // f11 + shift
      break;

   case '123':              // f12
      break;

    // ---------------------------------------------------------------------- mouse
    case 'click':           // click
      _id = _self.attr('data-id');
      $('.dashboard .card:not([data-id="'+_id+'"])').removeClass('selected');
      $('.dashboard .card[data-id="'+ _id+ '"]').addClass('selected');
      break;

    case 'dblclick':        // Double click
      break;

    default:                // exit this handler for other keys
      return;
  }
}

