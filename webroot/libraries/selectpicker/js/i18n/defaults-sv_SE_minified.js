/*!
 * Bootstrap-select v1.12.4 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2017 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
(function(root,factory){if(typeof define==='function'&&define.amd){define(["jquery"],function(a0){return(factory(a0))})}else if(typeof module==='object'&&module.exports){module.exports=factory(require("jquery"))}else{factory(root.jQuery)}}(this,function(jQuery){(function($){$.fn.selectpicker.defaults={noneSelectedText:'Inget valt',noneResultsText:'Inget sökresultat matchar {0}',countSelectedText:function(numSelected,numTotal){return(numSelected===1)?"{0} alternativ valt":"{0} alternativ valda"},maxOptionsText:function(numAll,numGroup){return['Gräns uppnåd (max {n} alternativ)','Gräns uppnåd (max {n} gruppalternativ)']},selectAllText:'Markera alla',deselectAllText:'Avmarkera alla',multipleSeparator:', '}})(jQuery)}))