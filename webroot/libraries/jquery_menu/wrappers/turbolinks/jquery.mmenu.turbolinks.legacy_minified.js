!function(n){var t,o,e="mmenu";n(document).on("ready",function(){o=n("html"),t=o.attr("class"),t=n.grep(t.split(/\s+/),function(n){return!/mm-/.test(n)}).join(" ")}),n(document).on("page:load",function(){o.attr("class",t),n[e].glbl=!1})}(jQuery)