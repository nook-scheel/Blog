$(document).ready(function(){

var tafBox1 = $("#contrib_content");  
var taf1Default = "Type your text here.";

tafBox1.focus(function(){  
    if($(this).attr("value") == taf1Default) $(this).attr("value", "");  
});  
tafBox1.blur(function(){  
    if($(this).attr("value") == "") $(this).attr("value", taf1Default);  
}); 

});

function addtag( tag )
{
	var txt = document.getElementById( 'contrib_content' );
	if(txt.value == 'Type your text here.') {
		txt.value = '';
	}
	
	if(tag == 'list') {
		txt.value = (txt.value).substring(0, txt.selectionStart) + "[list]" +
		"[*title]Bold Title" +
		"[*]First Item" +
		"[*]Second Item" +
		"[*last]Last Item" +
		"[/list]" + (txt.value).substring(txt.selectionEnd, txt.textLength);
	}
	else if(tag == 'url') {
		txt.value = (txt.value).substring(0, txt.selectionStart) + "[url=http://urlhere]Link Text[/url]" + (txt.value).substring(txt.selectionEnd, txt.textLength);
	}
	else if (document.selection)
	{
		txt.focus();
		sel = document.selection.createRange();
	    sel.text = '[' + tag + ']' + sel.text + '[/' + tag + ']';
	}
	else if (txt.selectionStart || txt.selectionStart == '0')
	{
		txt.value = (txt.value).substring(0, txt.selectionStart) + "["+tag+"]" + (txt.value).substring(txt.selectionStart, txt.selectionEnd) + "[/"+tag+"]" + (txt.value).substring(txt.selectionEnd, txt.textLength);
	}
	else
	{
		txt.value = '[' + tag + '][/' + tag + ']';
	}
	txt.focus();
	return;
}