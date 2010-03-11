function double_list_move(srcId, destId)
{
  var src = document.getElementById(srcId);
  var dest = document.getElementById(destId);
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

function double_list_submit(formElement, className)
{
  var element;

  for (var i = 0; i < formElement.elements.length; i++)
  {
    element = formElement.elements[i];
    if (element.type == 'select-multiple')
    {
      if (element.className == className + '-selected')
      {
        for (var j = 0; j < element.options.length; j++)
        {
          element.options[j].selected = true;
        }
      }
    }
  }
}
