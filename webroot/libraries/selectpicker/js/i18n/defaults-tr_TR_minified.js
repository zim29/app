/*!
 * Bootstrap-select v1.12.4 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2017 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
(function(root,factory){if(typeof define==='function'&&define.amd){define(["jquery"],function(a0){return(factory(a0))})}else if(typeof module==='object'&&module.exports){module.exports=factory(require("jquery"))}else{factory(root.jQuery)}}(this,function(jQuery){(function($){$.fn.selectpicker.defaults={noneSelectedText:'Hiçbiri seçilmedi',noneResultsText:'Hiçbir sonuç bulunamadı {0}',countSelectedText:function(numSelected,numTotal){return(numSelected==1)?"{0} öğe seçildi":"{0} öğe seçildi"},maxOptionsText:function(numAll,numGroup){return[(numAll==1)?'Limit aşıldı (maksimum {n} sayıda öğe )':'Limit aşıldı (maksimum {n} sayıda öğe)',(numGroup==1)?'Grup limiti aşıldı (maksimum {n} sayıda öğe)':'Grup limiti aşıldı (maksimum {n} sayıda öğe)']},selectAllText:'Tümünü Seç',deselectAllText:'Seçiniz',multipleSeparator:', '}})(jQuery)}))