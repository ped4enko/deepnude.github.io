!function(e){e.fn.customScrollbar=function(i,t){var o={skin:void 0,hScroll:!0,vScroll:!0,updateOnWindowResize:!1,animationSpeed:300,onCustomScroll:void 0,swipeSpeed:1,wheelSpeed:40,fixedThumbWidth:void 0,fixedThumbHeight:void 0,preventDefaultScroll:!1},s=function(i,t){this.$element=e(i),this.options=t,this.addScrollableClass(),this.addSkinClass(),this.addScrollBarComponents(),this.options.vScroll&&(this.vScrollbar=new n(this,new r)),this.options.hScroll&&(this.hScrollbar=new n(this,new l)),this.$element.data("scrollable",this),this.initKeyboardScrolling(),this.bindEvents()};s.prototype={addScrollableClass:function(){this.$element.hasClass("scrollable")||(this.scrollableAdded=!0,this.$element.addClass("scrollable"))},removeScrollableClass:function(){this.scrollableAdded&&this.$element.removeClass("scrollable")},addSkinClass:function(){"string"!=typeof this.options.skin||this.$element.hasClass(this.options.skin)||(this.skinClassAdded=!0,this.$element.addClass(this.options.skin))},removeSkinClass:function(){this.skinClassAdded&&this.$element.removeClass(this.options.skin)},addScrollBarComponents:function(){this.assignViewPort(),0==this.$viewPort.length&&(this.$element.wrapInner('<div class="viewport" />'),this.assignViewPort(),this.viewPortAdded=!0),this.assignOverview(),0==this.$overview.length&&(this.$viewPort.wrapInner('<div class="overview" />'),this.assignOverview(),this.overviewAdded=!0),this.addScrollBar("vertical","prepend"),this.addScrollBar("horizontal","append")},removeScrollbarComponents:function(){this.removeScrollbar("vertical"),this.removeScrollbar("horizontal"),this.overviewAdded&&this.$element.unwrap(),this.viewPortAdded&&this.$element.unwrap()},removeScrollbar:function(e){this[e+"ScrollbarAdded"]&&this.$element.find(".scroll-bar."+e).remove()},assignViewPort:function(){this.$viewPort=this.$element.find(".viewport")},assignOverview:function(){this.$overview=this.$viewPort.find(".overview")},addScrollBar:function(e,i){0==this.$element.find(".scroll-bar."+e).length&&(this.$element[i]("<div class='scroll-bar "+e+"'><div class='thumb'></div></div>"),this[e+"ScrollbarAdded"]=!0)},resize:function(e){this.vScrollbar&&this.vScrollbar.resize(e),this.hScrollbar&&this.hScrollbar.resize(e)},scrollTo:function(e){this.vScrollbar&&this.vScrollbar.scrollToElement(e),this.hScrollbar&&this.hScrollbar.scrollToElement(e)},scrollToXY:function(e,i){this.scrollToX(e),this.scrollToY(i)},scrollToX:function(e){this.hScrollbar&&this.hScrollbar.scrollOverviewTo(e,!0)},scrollToY:function(e){this.vScrollbar&&this.vScrollbar.scrollOverviewTo(e,!0)},scrollByX:function(e){this.hScrollbar&&this.scrollToX(this.hScrollbar.overviewPosition()+e)},scrollByY:function(e){this.vScrollbar&&this.scrollToY(this.vScrollbar.overviewPosition()+e)},remove:function(){this.removeScrollableClass(),this.removeSkinClass(),this.removeScrollbarComponents(),this.$element.data("scrollable",null),this.removeKeyboardScrolling(),this.vScrollbar&&this.vScrollbar.remove(),this.hScrollbar&&this.hScrollbar.remove()},setAnimationSpeed:function(e){this.options.animationSpeed=e},isInside:function(i,t){var o=e(i),s=e(t),n=o.offset(),l=s.offset();return n.top>=l.top&&n.left>=l.left&&n.top+o.height()<=l.top+s.height()&&n.left+o.width()<=l.left+s.width()},initKeyboardScrolling:function(){var e=this;this.elementKeydown=function(i){document.activeElement===e.$element[0]&&(e.vScrollbar&&e.vScrollbar.keyScroll(i),e.hScrollbar&&e.hScrollbar.keyScroll(i))},this.$element.attr("tabindex","-1").keydown(this.elementKeydown)},removeKeyboardScrolling:function(){this.$element.removeAttr("tabindex").unbind("keydown",this.elementKeydown)},bindEvents:function(){this.options.onCustomScroll&&this.$element.on("customScroll",this.options.onCustomScroll)}};var n=function(e,i){this.scrollable=e,this.sizing=i,this.$scrollBar=this.sizing.scrollBar(this.scrollable.$element),this.$thumb=this.$scrollBar.find(".thumb"),this.setScrollPosition(0,0),this.resize(),this.initMouseMoveScrolling(),this.initMouseWheelScrolling(),this.initTouchScrolling(),this.initMouseClickScrolling(),this.initWindowResize()};n.prototype={resize:function(e){this.overviewSize=this.sizing.size(this.scrollable.$overview),this.calculateViewPortSize(),this.sizing.size(this.scrollable.$viewPort,this.viewPortSize),this.ratio=this.viewPortSize/this.overviewSize,this.sizing.size(this.$scrollBar,this.viewPortSize),this.thumbSize=this.calculateThumbSize(),this.sizing.size(this.$thumb,this.thumbSize),this.maxThumbPosition=this.calculateMaxThumbPosition(),this.maxOverviewPosition=this.calculateMaxOverviewPosition(),this.enabled=this.overviewSize>this.viewPortSize,void 0===this.scrollPercent&&(this.scrollPercent=0),this.enabled?this.rescroll(e):this.setScrollPosition(0,0),this.$scrollBar.toggle(this.enabled)},calculateViewPortSize:function(){var e=this.sizing.size(this.scrollable.$element);if(e>0&&!this.maxSizeUsed)this.viewPortSize=e,this.maxSizeUsed=!1;else{var i=this.sizing.maxSize(this.scrollable.$element);this.viewPortSize=Math.min(i,this.overviewSize),this.maxSizeUsed=!0}},calculateThumbSize:function(){var e,i=this.sizing.fixedThumbSize(this.scrollable.options);return e=i?i:this.ratio*this.viewPortSize,Math.max(e,this.sizing.minSize(this.$thumb))},initMouseMoveScrolling:function(){var i=this;this.$thumb.mousedown(function(e){i.enabled&&i.startMouseMoveScrolling(e)}),this.documentMouseup=function(e){i.stopMouseMoveScrolling(e)},e(document).mouseup(this.documentMouseup),this.documentMousemove=function(e){i.mouseMoveScroll(e)},e(document).mousemove(this.documentMousemove),this.$thumb.click(function(e){e.stopPropagation()})},removeMouseMoveScrolling:function(){this.$thumb.unbind(),e(document).unbind("mouseup",this.documentMouseup),e(document).unbind("mousemove",this.documentMousemove)},initMouseWheelScrolling:function(){var e=this;this.scrollable.$element.mousewheel(function(i,t,o,s){if(e.enabled){var n=e.mouseWheelScroll(o,s);e.stopEventConditionally(i,n)}})},removeMouseWheelScrolling:function(){this.scrollable.$element.unbind("mousewheel")},initTouchScrolling:function(){if(document.addEventListener){var e=this;this.elementTouchstart=function(i){e.enabled&&e.startTouchScrolling(i)},this.scrollable.$element[0].addEventListener("touchstart",this.elementTouchstart),this.documentTouchmove=function(i){e.touchScroll(i)},this.scrollable.$element[0].addEventListener("touchmove",this.documentTouchmove),this.elementTouchend=function(i){e.stopTouchScrolling(i)},this.scrollable.$element[0].addEventListener("touchend",this.elementTouchend)}},removeTouchScrolling:function(){document.addEventListener&&(this.scrollable.$element[0].removeEventListener("touchstart",this.elementTouchstart),document.removeEventListener("touchmove",this.documentTouchmove),this.scrollable.$element[0].removeEventListener("touchend",this.elementTouchend))},initMouseClickScrolling:function(){var e=this;this.scrollBarClick=function(i){e.mouseClickScroll(i)},this.$scrollBar.click(this.scrollBarClick)},removeMouseClickScrolling:function(){this.$scrollBar.unbind("click",this.scrollBarClick)},initWindowResize:function(){if(this.scrollable.options.updateOnWindowResize){var i=this;this.windowResize=function(){i.resize()},e(window).resize(this.windowResize)}},removeWindowResize:function(){e(window).unbind("resize",this.windowResize)},isKeyScrolling:function(e){return null!=this.keyScrollDelta(e)},keyScrollDelta:function(e){for(var i in this.sizing.scrollingKeys)if(i==e)return this.sizing.scrollingKeys[e](this.viewPortSize);return null},startMouseMoveScrolling:function(i){this.mouseMoveScrolling=!0,e("body").addClass("not-selectable"),this.setUnselectable(e("body"),"on"),this.setScrollEvent(i),i.preventDefault()},stopMouseMoveScrolling:function(i){this.mouseMoveScrolling=!1,e("body").removeClass("not-selectable"),this.setUnselectable(e("body"),null)},setUnselectable:function(e,i){e.attr("unselectable")!=i&&(e.attr("unselectable",i),e.find(":not(input)").attr("unselectable",i))},mouseMoveScroll:function(e){if(this.mouseMoveScrolling){var i=this.sizing.mouseDelta(this.scrollEvent,e);this.scrollThumbBy(i),this.setScrollEvent(e)}},startTouchScrolling:function(e){e.touches&&1==e.touches.length&&(this.setScrollEvent(e.touches[0]),this.touchScrolling=!0,e.stopPropagation())},touchScroll:function(e){if(this.touchScrolling&&e.touches&&1==e.touches.length){var i=-this.sizing.mouseDelta(this.scrollEvent,e.touches[0])*this.scrollable.options.swipeSpeed,t=this.scrollOverviewBy(i);t&&this.setScrollEvent(e.touches[0]),this.stopEventConditionally(e,t)}},stopTouchScrolling:function(e){this.touchScrolling=!1,e.stopPropagation()},mouseWheelScroll:function(e,i){var t=-this.sizing.wheelDelta(e,i)*this.scrollable.options.wheelSpeed;return 0!=t?this.scrollOverviewBy(t):void 0},mouseClickScroll:function(e){var i=this.viewPortSize-20;e["page"+this.sizing.scrollAxis()]<this.$thumb.offset()[this.sizing.offsetComponent()]&&(i=-i),this.scrollOverviewBy(i)},keyScroll:function(e){var i=e.which;if(this.enabled&&this.isKeyScrolling(i)){var t=this.scrollOverviewBy(this.keyScrollDelta(i));this.stopEventConditionally(e,t)}},scrollThumbBy:function(e){var i=this.thumbPosition();i+=e,i=this.positionOrMax(i,this.maxThumbPosition);var t=this.scrollPercent;if(this.scrollPercent=i/this.maxThumbPosition,t!=this.scrollPercent){var o=i*this.maxOverviewPosition/this.maxThumbPosition;return this.setScrollPosition(o,i),this.triggerCustomScroll(t),!0}return!1},thumbPosition:function(){return this.$thumb.position()[this.sizing.offsetComponent()]},scrollOverviewBy:function(e){var i=this.overviewPosition()+e;return this.scrollOverviewTo(i,!1)},overviewPosition:function(){return-this.scrollable.$overview.position()[this.sizing.offsetComponent()]},scrollOverviewTo:function(e,i){e=this.positionOrMax(e,this.maxOverviewPosition);var t=this.scrollPercent;if(this.scrollPercent=e/this.maxOverviewPosition,t!=this.scrollPercent){var o=this.scrollPercent*this.maxThumbPosition;return i?this.setScrollPositionWithAnimation(e,o):this.setScrollPosition(e,o),this.triggerCustomScroll(t),!0}return!1},positionOrMax:function(e,i){return 0>e?0:e>i?i:e},triggerCustomScroll:function(e){this.scrollable.$element.trigger("customScroll",{scrollAxis:this.sizing.scrollAxis(),direction:this.sizing.scrollDirection(e,this.scrollPercent),scrollPercent:100*this.scrollPercent})},rescroll:function(e){if(e){var i=this.positionOrMax(this.overviewPosition(),this.maxOverviewPosition);this.scrollPercent=i/this.maxOverviewPosition;var t=this.scrollPercent*this.maxThumbPosition;this.setScrollPosition(i,t)}else{var t=this.scrollPercent*this.maxThumbPosition,i=this.scrollPercent*this.maxOverviewPosition;this.setScrollPosition(i,t)}},setScrollPosition:function(e,i){this.$thumb.css(this.sizing.offsetComponent(),i+"px"),this.scrollable.$overview.css(this.sizing.offsetComponent(),-e+"px")},setScrollPositionWithAnimation:function(e,i){var t={},o={};t[this.sizing.offsetComponent()]=i+"px",this.$thumb.animate(t,this.scrollable.options.animationSpeed),o[this.sizing.offsetComponent()]=-e+"px",this.scrollable.$overview.animate(o,this.scrollable.options.animationSpeed)},calculateMaxThumbPosition:function(){return Math.max(0,this.sizing.size(this.$scrollBar)-this.thumbSize)},calculateMaxOverviewPosition:function(){return Math.max(0,this.sizing.size(this.scrollable.$overview)-this.sizing.size(this.scrollable.$viewPort))},setScrollEvent:function(e){var i="page"+this.sizing.scrollAxis();this.scrollEvent&&this.scrollEvent[i]==e[i]||(this.scrollEvent={pageX:e.pageX,pageY:e.pageY})},scrollToElement:function(i){var t=e(i);if(this.sizing.isInside(t,this.scrollable.$overview)&&!this.sizing.isInside(t,this.scrollable.$viewPort)){var o=t.offset(),s=this.scrollable.$overview.offset();this.scrollable.$viewPort.offset();this.scrollOverviewTo(o[this.sizing.offsetComponent()]-s[this.sizing.offsetComponent()],!0)}},remove:function(){this.removeMouseMoveScrolling(),this.removeMouseWheelScrolling(),this.removeTouchScrolling(),this.removeMouseClickScrolling(),this.removeWindowResize()},stopEventConditionally:function(e,i){(i||this.scrollable.options.preventDefaultScroll)&&(e.preventDefault(),e.stopPropagation())}};var l=function(){};l.prototype={size:function(e,i){return i?e.width(i):e.width()},minSize:function(e){return parseInt(e.css("min-width"))||0},maxSize:function(e){return parseInt(e.css("max-width"))||0},fixedThumbSize:function(e){return e.fixedThumbWidth},scrollBar:function(e){return e.find(".scroll-bar.horizontal")},mouseDelta:function(e,i){return i.pageX-e.pageX},offsetComponent:function(){return"left"},wheelDelta:function(e,i){return e},scrollAxis:function(){return"X"},scrollDirection:function(e,i){return i>e?"right":"left"},scrollingKeys:{37:function(e){return-10},39:function(e){return 10}},isInside:function(i,t){var o=e(i),s=e(t),n=o.offset(),l=s.offset();return n.left>=l.left&&n.left+o.width()<=l.left+s.width()}};var r=function(){};return r.prototype={size:function(e,i){return e.hasClass("imagelist")?"250px":i?e.height(i):e.height()},minSize:function(e){return parseInt(e.css("min-height"))||0},maxSize:function(e){return parseInt(e.css("max-height"))||0},fixedThumbSize:function(e){return e.fixedThumbHeight},scrollBar:function(e){return e.find(".scroll-bar.vertical")},mouseDelta:function(e,i){return i.pageY-e.pageY},offsetComponent:function(){return"top"},wheelDelta:function(e,i){return i},scrollAxis:function(){return"Y"},scrollDirection:function(e,i){return i>e?"down":"up"},scrollingKeys:{38:function(e){return-10},40:function(e){return 10},33:function(e){return-(e-20)},34:function(e){return e-20}},isInside:function(i,t){var o=e(i),s=e(t),n=o.offset(),l=s.offset();return n.top>=l.top&&n.top+o.height()<=l.top+s.height()}},this.each(function(){if(void 0==i&&(i=o),"string"==typeof i){var n=e(this).data("scrollable");n&&n[i](t)}else{if("object"!=typeof i)throw"Invalid type of options";i=e.extend(o,i),new s(e(this),i)}})}}(jQuery),function(e){function i(i){var t=i||window.event,o=[].slice.call(arguments,1),s=0,n=0,l=0;return i=e.event.fix(t),i.type="mousewheel",t.wheelDelta&&(s=t.wheelDelta/120),t.detail&&(s=-t.detail/3),l=s,void 0!==t.axis&&t.axis===t.HORIZONTAL_AXIS&&(l=0,n=s),void 0!==t.wheelDeltaY&&(l=t.wheelDeltaY/120),void 0!==t.wheelDeltaX&&(n=t.wheelDeltaX/120),o.unshift(i,s,n,l),(e.event.dispatch||e.event.handle).apply(this,o)}var t=["DOMMouseScroll","mousewheel"];if(e.event.fixHooks)for(var o=t.length;o;)e.event.fixHooks[t[--o]]=e.event.mouseHooks;e.event.special.mousewheel={setup:function(){if(this.addEventListener)for(var e=t.length;e;)this.addEventListener(t[--e],i,!1);else this.onmousewheel=i},teardown:function(){if(this.removeEventListener)for(var e=t.length;e;)this.removeEventListener(t[--e],i,!1);else this.onmousewheel=null}},e.fn.extend({mousewheel:function(e){return e?this.bind("mousewheel",e):this.trigger("mousewheel")},unmousewheel:function(e){return this.unbind("mousewheel",e)}})}(jQuery);