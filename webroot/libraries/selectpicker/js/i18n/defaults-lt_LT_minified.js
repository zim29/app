/*!
 * Bootstrap-select v1.12.4 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2017 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
(function(root,factory){if(typeof define==='function'&&define.amd){define(["jquery"],function(a0){return(factory(a0))})}else if(typeof module==='object'&&module.exports){module.exports=factory(require("jquery"))}else{factory(root.jQuery)}}(this,function(jQuery){(function($){$.fn.selectpicker.defaults={noneSelectedText:'Niekas nepasirinkta',noneResultsText:'Niekas nesutapo su {0}',countSelectedText:function(numSelected,numTotal){return(numSelected==1)?"{0} elementas pasirinktas":"{0} elementai(-ų) pasirinkta"},maxOptionsText:function(numAll,numGroup){return[(numAll==1)?'Pasiekta riba ({n} elementas daugiausiai)':'Riba pasiekta ({n} elementai(-ų) daugiausiai)',(numGroup==1)?'Grupės riba pasiekta ({n} elementas daugiausiai)':'Grupės riba pasiekta ({n} elementai(-ų) daugiausiai)']},selectAllText:'Pasirinkti visus',deselectAllText:'Atmesti visus',multipleSeparator:', '}})(jQuery)}))