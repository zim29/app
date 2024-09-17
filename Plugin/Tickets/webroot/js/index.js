jQuery(document).on('ready', function(){
  jQuery('.actions.ajax a.button_action').on('click', function(e){
    var button_pressed = jQuery(this);
    jQuery.ajax({
      type:  'post',
      cache: false,
      data: { id : jQuery(this).attr('href').substr(jQuery(this).attr('href').lastIndexOf('/') + 1) },
      url:   jQuery(this).attr('href'),         
      dataType: 'json',
      beforeSend: function () {
        ajax_loading_open();
      },
      success:  function (response) {
        ajax_loading_close();
        if (response.error)
        {
          alert(response.error);
        }
        else
        {
          button_pressed.children().attr('class', response.data.class);
          button_pressed.attr('href', response.data.href);
        }
      }
    });
    e.preventDefault();
  });
});