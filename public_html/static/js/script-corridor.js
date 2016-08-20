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
  var cid    = parseInt(_self.index());
  var lastid = parseInt($('.card').length) - 1;

  var ctrl   = e.ctrlKey  ? 'ctrl'  : '';
  var shift  = e.shiftKey ? 'shift' : '';
  var alt    = e.altKey   ? 'alt'   : '';
  var mytxt  = String(_key) + ctrl + alt + shift;
  var keyp   = String.fromCharCode(e.keyCode);

  // select item with number
  if(keyp > 0 || keyp < 9)
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
      break;


    // ---------------------------------------------------------- Escape
    case '27':              //Escape
      break;


    // ---------------------------------------------------------- Space
    case '32':              // space
    case '32shift':         // space + shift
      // if player exist do right thing!
      ex_player('space');

      if(!_self.hasClass('selected'))
      {
        ex_removeClass('selected focused zero');
        _self.addClass('selected');
        ex_showProp();
      }
      break;

    case '32ctrl':          // space + ctrl
    case '32ctrlshift':     // space + ctrl + shift
      _self.toggleClass('selected');
      if(_self.hasClass('selected'))
      {
        ex_showProp();
      }
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
      _id = cid + 1;
      if(_id >= lastid)
      {
        _id = 0;
      }
      $('.card:eq('+ cid +')').removeClass('selected');
      $('.card:eq('+ _id +')').addClass('selected');

      break;

    // ---------------------------------------------------------- Up
    case '38':              // up
      break;

    // ---------------------------------------------------------- Right
    case '39':              // right
      _id = cid - 1;
      if(_id < 0)
      {
        _id = lastid-1;
      }
      $('.card:eq('+ cid +')').removeClass('selected');
      $('.card:eq('+ _id +')').addClass('selected');
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
      break;

    case 'dblclick':        // Double click
      break;

    default:                // exit this handler for other keys
      return;
  }
}

