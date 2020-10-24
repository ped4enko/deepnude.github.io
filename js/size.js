//	set the element contents size to the size of the contents
function set_size(obj, heightwidth)
{
	if (heightwidth == "height")
	{
		h = obj.outerHeight(true);
		obj.height(h)
	}
	else
	{
		w = obj.outerWidth(true);
		obj.width(w)
	}
}

//	set the size of the element to the sum of the child element sizes
function set_to_sum_children(obj, heightwidth)
{


	sum = 0;
	if (heightwidth == "height")
	{

		obj.children().each(function(){
			//// console.log(obj.attr("id") + " oh: " + $(this).outerHeight(true))
			sum += $(this).outerHeight(true);
		})
		obj.height(sum);
	}
	else
	{
		obj.children().each(function(){
			sum += $(this).outerWidth();
		})
		obj.width(sum);
	}

		//// console.log(obj.attr("id") + " sum: " + sum)
}

//	set the size of the element to the sum of the child element
//	sizes that have each been rounded up
function set_to_sum_child_ceiling(obj, heightwidth)
{

	sum = 0;
	if (heightwidth == "height")
	{
		obj.children().each(function(){
			sum += Math.ceil($(this).outerHeight());
		});
		obj.height(w);
	}
	else
	{
		obj.children().each(function(){
			sum += Math.ceil($(this).outerWidth());
		});
		obj.width(sum);
	}

}
//	set the size of the element to the sum of the sibling sizes
function set_to_sum_sibling(obj, heightwidth)
{
	sum = 0;
	if (heightwidth == "height")
	{
		obj.siblings().each(function(){
			sum += $(this).outerHeight();
		})
		obj.height(sum);
	}
	else
	{
		obj.siblings().each(function(){
			sum += $(this).outerwidth();
		})
		obj.width(sum);
	}

}

//	set the size of the element to the size of the largest child
function set_to_max_child(obj, heightwidth)
{
	tot = 0;
	if (heightwidth == "height")
	{
		obj.children().each(function(){
			t = $(this).outerHeight();
			if (t > tot)
				tot = t
		})
		obj.height(tot);
	}
	else
	{
		obj.children().each(function(){
			t = $(this).outerWidth();
			if (t > tot)
				tot = t
		})
		obj.width(tot);
	}
}
//	set the size of the element to the size of the
//	largest sibling
function set_max_sibling(obj, heightwidth)
{
	tot = 0;
	if (heightwidth == "height")
	{
		obj.siblings().each(function(){
			t = $(this).outerHeight();
			if (t > tot)
				tot = t
		})
		obj.height(tot);
	}
	else
	{
		obj.siblings().each(function(){
			t = $(this).outerWidth();
			if (t > tot)
				tot = t
		})
		obj.width(tot);
	}
}
//	set the size of the element's children to the size
//	of the largest child
function set_child_to_largest(parentObj, heightwidth)
{
	tot = 0;
	if (heightwidth == "height")
	{
		parentObj.children().each(function(){
			th = getChildrenHeight($(this))
			if (th > tot)
				tot = th
		})
		parentObj.children().each(function(){
			$(this).height(tot)
		})
	}
	else
	{
		parentObj.children().each(function(){
			th = set_to_sum_children($(this))
			if (th > tot)
				tot = th
		})
		parentObj.children().each(function(){
			$(this).width(tot)
		})

	}
}
function set_size_by_aspect_ratio(obj, heightwidth, ratioX, ratioY)
{
	if (heightwidth == "height")
	{
		h = $(this).outerHeight();
		$(this).width(h / ratioY * ratioX);
	}
	else
	{
		w = $(this).outerWidth();
		$(this).height(w / ratioX * ratioY);
	}
}
var currentTallest = 0,
currentRowStart = 0,
rowDivs = new Array();

function setConformingHeight(el, newHeight)
{
	el.data("originalHeight", (el.data("originalHeight") == undefined) ? (el.height()) : (el.data("originalHeight")));
	el.height(newHeight);
}

function getOriginalHeight(el)
{
	return (el.data("originalHeight") == undefined) ? (el.height()) : (el.data("originalHeight"));
}

function columnConform(ele) {
	//// console.log(ele)
	currentTallest = 0;
	currentRowStart = 0;
	rowDivs = new Array();
	$(ele).each(function() {
		var $el = $(this);
		var topPosition = $el.position().top;
		if (currentRowStart != topPosition)
		{
			for(currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++)
				setConformingHeight(rowDivs[currentDiv], currentTallest);
			rowDivs.length = 0;
			currentRowStart = topPosition;
			currentTallest = getOriginalHeight($el); rowDivs.push($el);
		}
		else
		{
			rowDivs.push($el);
			currentTallest = (currentTallest < getOriginalHeight($el)) ? (getOriginalHeight($el)) : (currentTallest);
		}
	});
	for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++)
		setConformingHeight(rowDivs[currentDiv], currentTallest);
}

var sizeClassList = ".set-height, .set-width, .set-to-children-height, .set-to-children-width, .set-to-sibling-height, .set-to-sibling-width, .set-to-max-child-height, .set-to-children-height-ceiling, .set-to-children-width-ceiling, .set-size-by-aspect-ratio";
function executeSizeFunction()
{
	matchedElements = $($(document).find(sizeClassList).get().reverse());
	matchedElements.css("height", "")
	if (matchedElements.length > 0)
	{
		matchedElements.each(function(){

			//console.log($(this))
			//////////////////////////////////////////////////////
			//	call the functions to actually set the sizes
			//////////////////////////////////////////////////////

			//	set the height to the height of the contents
			if ($(this).hasClass("set-height"))
			{
				set_size($(this), "height");
			}

			//	set the width to the width of the contents
			if ($(this).hasClass("set-width"))
			{
				set_size($(this), "width");
			}

			//	set the height to the sum of the heights of the contents
			if ($(this).hasClass("set-to-children-height"))
			{
				set_to_sum_children($(this), "height");
			}

			//	set the height to the sum of the heights of the contents
			if ($(this).hasClass("set-to-children-width"))
			{
				set_to_sum_children($(this), "width");
			}

			//	set the height to the sum of the sibling heights
			if ($(this).hasClass("set-to-sibling-height"))
			{
				set_to_sum_sibling($(this), "height");
			}

			//	set the width to the sum of the sibling widths
			if ($(this).hasClass("set-to-sibling-width"))
			{
				set_to_sum_sibling($(this), "width");
			}

			//	set the height to the maximum height of the contents
			if ($(this).hasClass("set-to-max-child-height"))
			{
				set_to_max_child($(this), "height");
			}

			//	set the width to the maximum width of the contents
			if ($(this).hasClass("set-to-max-child-width"))
			{
				set_to_max_child($(this), "width");
			}

			//	set the height to the sum of the child element sizes that have each been rounded up
			if ($(this).hasClass("set-to-children-height-ceiling"))
			{
				set_to_sum_child_ceiling($(this), "height");
			}

			//	set the width to the sum of the child element sizes that have each been rounded up
			if ($(this).hasClass("set-to-children-width-ceiling"))
			{
				set_to_sum_child_ceiling($(this), "width");
			}

			//	set the element size to 16x9
			if ($(this).hasClass("set-size-by-aspect-ratio"))
			{
				set_size_by_aspect_ratio($(this), "width", 16, 9, $(window).height() * .8)
			}

		})
	}
}