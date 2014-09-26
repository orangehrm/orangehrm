
function double_list_move(srcId, destId)
{
  var src=document.getElementById(srcId);
  var dest=document.getElementById(destId);
  for (var i = 0; i < src.options.length; i++)
  {
    if (src.options[i].selected)
    {
      dest.options[dest.length] = new Option(src.options[i].text, src.options[i].value);
      src.options[i] = null;
      --i;
    }
  }
}

function double_list_submit()
{
  var form = document.getElementById('sf_admin_edit_form');
  var element;

  // find multiple selects with name beginning 'associated_' and select all their options
  for (var i = 0; i < form.elements.length; i++)
  {
    element = form.elements[i];
    if (element.type == 'select-multiple')
    {
      if (element.className == 'sf_admin_multiple-selected')
      {
        for (var j = 0; j < element.options.length; j++)
        {
          element.options[j].selected = true;
        }
      }
    }
  }
}
