/*!
 * Bootstrap-select v1.12.4 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2017 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
(function(root,factory){if(typeof define==='function'&&define.amd){define(["jquery"],function(a0){return(factory(a0))})}else if(typeof module==='object'&&module.exports){module.exports=factory(require("jquery"))}else{factory(root.jQuery)}}(this,function(jQuery){(function($){$.fn.selectpicker.defaults={noneSelectedText:'Valikut pole tehtud',noneResultsText:'Otsingule {0} ei ole vasteid',countSelectedText:function(numSelected,numTotal){return(numSelected==1)?"{0} item selected":"{0} items selected"},maxOptionsText:function(numAll,numGroup){return['Limiit on {n} max','Globaalne limiit on {n} max']},selectAllText:'Vali kõik',deselectAllText:'Tühista kõik',multipleSeparator:', '}})(jQuery)}))