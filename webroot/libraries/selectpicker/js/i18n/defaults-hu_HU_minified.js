/*!
 * Bootstrap-select v1.12.4 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2017 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
(function(root,factory){if(typeof define==='function'&&define.amd){define(["jquery"],function(a0){return(factory(a0))})}else if(typeof module==='object'&&module.exports){module.exports=factory(require("jquery"))}else{factory(root.jQuery)}}(this,function(jQuery){(function($){$.fn.selectpicker.defaults={noneSelectedText:'Válasszon!',noneResultsText:'Nincs találat {0}',countSelectedText:function(numSelected,numTotal){return'{0} elem kiválasztva'},maxOptionsText:function(numAll,numGroup){return['Legfeljebb {n} elem választható','A csoportban legfeljebb {n} elem választható']},selectAllText:'Mind',deselectAllText:'Egyik sem',multipleSeparator:', '}})(jQuery)}))