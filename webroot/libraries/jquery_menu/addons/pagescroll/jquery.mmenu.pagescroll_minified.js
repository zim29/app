!function(t){function e(t){c&&c.length&&c.is(":visible")&&r.$html.add(r.$body).animate({scrollTop:c.offset().top+t}),c=!1}function i(t){try{return!("#"==t||"#"!=t.slice(0,1)||!r.$page.find(t).length)}catch(e){return!1}}var n="mmenu",s="pageScroll";t[n].addons[s]={setup:function(){var o=this,c=this.opts[s],a=this.conf[s];if(r=t[n].glbl,"boolean"==typeof c&&(c={scroll:c}),c=this.opts[s]=t.extend(!0,{},t[n].defaults[s],c),c.scroll&&this.bind("close:finish",function(){e(a.scrollOffset)}),c.update){var o=this,h=[],d=[];o.bind("initListview:after",function(e){o.__filterListItemAnchors(e.find("."+l.listview).children("li")).each(function(){var e=t(this).attr("href");i(e)&&h.push(e)}),d=h.reverse()});var u=-1;r.$wndw.on(f.scroll+"-"+s,function(e){for(var i=r.$wndw.scrollTop(),n=0;n<d.length;n++)if(t(d[n]).offset().top<i+a.updateOffset){u!==n&&(u=n,o.setSelected(o.__filterListItemAnchors(o.$pnls.children("."+l.opened).find("."+l.listview).children("li")).filter('[href="'+d[n]+'"]').parent()));break}})}},add:function(){l=t[n]._c,o=t[n]._d,f=t[n]._e},clickAnchor:function(n,o){if(c=!1,o&&this.opts[s].scroll&&this.opts.offCanvas&&r.$page&&r.$page.length){var f=n.attr("href");i(f)&&(c=t(f),r.$html.hasClass(l.mm("widescreen"))&&e(this.conf[s].scrollOffset))}}},t[n].defaults[s]={scroll:!1,update:!1},t[n].configuration[s]={scrollOffset:0,updateOffset:50};var l,o,f,r,c=!1}(jQuery)