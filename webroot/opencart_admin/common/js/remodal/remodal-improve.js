function remodal_button_confirm_loading_on() {
    var button_confirm = remodal_button_confirm_get();
    button_confirm.attr('disabled','disabled');
    button_confirm.html(remodal_button_confirm_loading_text)
}
function remodal_button_confirm_loading_off(text) {
    var button_confirm = remodal_button_confirm_get();
    button_confirm.removeAttr('disabled');
    button_confirm.html(text);
}
function remodal_button_confirm_get_text() {
    var button_confirm = remodal_button_confirm_get();
    return button_confirm.html();
}
function remodal_button_confirm_get() {
    var button = $('div.remodal.remodal-is-opened').find('button.remodal-confirm');
    return button;
}
function remodal_notification(message, class_custom, position)
{
    var remodal_openned = $('div.remodal.remodal-is-opened');
    if(typeof class_custom == 'undefined') class_custom = 'danger';
	if(class_custom == 'warning') class_custom = 'danger';

	if(class_custom == 'danger')
	    icon_class = 'exclamation-circle';
	else if(class_custom == 'success')
	    icon_class = 'check-circle';
	else
	    icon_class = '';

	remodal_openned.find('div.alert').remove();

	if(typeof class_custom == 'undefined')
	    position = 'after';

	var message = '<div class="alert alert-'+class_custom+'"><i class="fa fa-'+icon_class+'-circle"></i> '+message+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>';

	if(position == 'after')
	    remodal_openned.append(message);
	else
	    remodal_openned.find('h1').first().after(message);
}