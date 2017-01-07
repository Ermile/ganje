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
  // console.log(mytxt);

  if($('body').hasClass('editingTable'))
  {
    // editing ermile table
    return false;
  }

  if(!cid)
  {
    cid = 0;
  }

  // handle numpad
  if(_key >= 96 && _key <= 105)
  {
    keyp = _key - 96;
  }

  // select item with number
  if($('body').attr('data-location') == 'dashboard' && keyp >= '0' && keyp <= '9')
  {
    $('.card').removeClass('selected');
    $('.card:eq('+ (keyp-1) +')').addClass('selected');
  }


  switch(mytxt)
  {
    // ---------------------------------------------------------- Enter
    case '13':              // Enter
        if(cid)
        {
          transfer('home', cid);
        }
      break;

    case '13ctrl':          // ctrl + Enter
    case '106':             // *
        if($('body').attr('data-location') == 'personal')
        {
          setTime(cid);
        }
      break;


    // ---------------------------------------------------------- Escape
    case '27':              //Escape
      transfer(null, 'home');
      changePerson(0);
      break;


    // ---------------------------------------------------------- Space
    case '32':              // space
    case '32shift':         // space + shift
    case '32ctrl':          // space + ctrl
    case '32ctrlshift':     // space + ctrl + shift
      if(!cid)
      {
        changePerson(1);
      }
      break;


    // ---------------------------------------------------------- Page Up
    case '33':              // PageUP
    // ---------------------------------------------------------- Up
    case '38':              // up
      _id = cid - 5;
      if(!cid || _id < 1)
      {
        _id = 1;
      }
      changePerson(_id);
      break;


    // ---------------------------------------------------------- Page Down
    case '34':              // PageDown
    // ---------------------------------------------------------- Down
    case '40':              // down
      _id = cid + 5;
      if(!cid || _id > lastid)
      {
        _id = lastid;
      }
      changePerson(_id);
      break;


    // ---------------------------------------------------------- End
    case '35':              // End
      changePerson(lastid);
      break;


    // ---------------------------------------------------------- Home
    case '36':              // Home
      changePerson(1);
      break;


    // ---------------------------------------------------------- Left
    case '37':              // left
      if(!cid || (cid + 1) > lastid)
      {
        _id = 1;
      }
      else
      {
        _id = cid + 1;
      }
      changePerson(_id);
      break;


    // ---------------------------------------------------------- Right
    case '39':              // right
      if(!cid || (cid - 1) < 1)
      {
        _id = lastid;
      }
      else
      {
        _id = cid - 1;
      }
      changePerson(_id);
      break;

    // ---------------------------------------------------------- BackSpace
    case '8':               // Back Space
    // ---------------------------------------------------------- Delete
    case '46':              // delete
      changePerson(0);
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

    case '107':             // plus +
    case '187shift':        // plus +
      setExtra('plus', 10);
      break;

    case '109':             // minus -
    case '189shift':        // minus -
      setExtra('minus', 5);
      break;

    case '110':             // .
    case '190':             // .
      setExtra(false, false);
      changePerson(0);
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
      // if user is selected enter to it
      if(cid == _id)
      {
        if($(e.target).hasClass('action') || $(e.target).hasClass('enter') || $(e.target).hasClass('exit'))
        {
          setTime(_id);
        }
        else
        {
          // transfer('home', cid);
        }
      }
      // else select this user
      else
      {
        changePerson(_id);
      }
      break;

    case 'dblclick':        // Double click
      if (_self.parents('.detail').length)
      {
        if($('body').attr('data-location') == 'personal')
        {
          _id = _self.parents('.detail').attr('data-id');
          setTime(_id);
        }
      }

      if(cid)
      {
        if($(e.target).hasClass('action') || $(e.target).hasClass('enter') || $(e.target).hasClass('exit'))
        {

        }
        else
        {
          transfer('home', cid);
        }
      }
      break;

    case 'rightclick':        // Double click
      transfer(null, 'home');
      changePerson(0);
      break;

    default:                // exit this handler for other keys
      return;
  }
}

