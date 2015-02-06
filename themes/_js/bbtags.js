function tagPos(str)
{
    return str.length;
}

function bbTag(tag, id)
{
    var txtarea = document.getElementById(id);

    if (txtarea.selectionStart == null)
    {
        var rng = document.selection.createRange();
        // rng.setSelectionRange(tagPos('['+tag+']'), tagPos('['+tag+']'));
    }
    else
    {
        txtarea.value = txtarea.value.substring(0, txtarea.selectionStart) + "["+tag+"]" + txtarea.value.substring(txtarea.selectionStart, txtarea.selectionEnd) + "[/"+tag+"]" + txtarea.value.substring(txtarea.selectionEnd);
    }

    if(txtarea.setSelectionRange)
    {
        txtarea.focus();
        txtarea.setSelectionRange(tagPos(txtarea.value+"["+tag+"]"), tagPos(txtarea.value+"["+tag+"]"));
    }
    else if(txtarea.createTextRange)
    {
        var range = txtarea.createTextRange();
        range.collapse(true);
        range.moveEnd(value, tagPos(txtarea.value+"["+tag+"]"));
        range.moveStart(value, tagPos(txtarea.value+"["+tag+"]"));
        //alert(range.moveStart('character', pos));
        range.select();
    }
}

function bbUrl(id)
{
    var url = prompt('Enter Address', 'http://');
    var txtarea = document.getElementById(id);
    var value = txtarea.value+'[url='+url+']Link...[/url]';

    if(url != null)
    {
        txtarea.value = value;
    }

    if(txtarea.setSelectionRange)
    {
        txtarea.focus();
        txtarea.setSelectionRange(tagPos(txtarea.value+'[url='+url+']'), tagPos(txtarea.value+'[url='+url+']'));
    }
    else if(txtarea.createTextRange)
    {
        var range = txtarea.createTextRange();
        range.collapse(true);
        range.moveEnd(value, tagPos(txtarea.value+'[url='+url+']'));
        range.moveStart(value, tagPos(txtarea.value+'[url='+url+']'));
        //alert(range.moveStart('character', pos));
        range.select();
    }
}