var tabs_to_remove_icon = ['tab_faq', /*'tab_support',*/ 'tab_changelog---downloads', 'tab_Вопрос---Ответ', /*'tab_Поддержка',*/ 'tab_История-изменений---downloads'];
$(document).on('ready', function(){
    $('ul.nav.nav-tabs li a').each(function(){
        var class_a = $(this).attr('class');
        if($.inArray( class_a, tabs_to_remove_icon ) >= 0) {
            icon = $(this).find('span');
            $(this).html(icon);
            $(this).addClass('tab_no_text');
        }
    });
});