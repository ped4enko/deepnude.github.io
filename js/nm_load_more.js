var maxpages;
var button;
var page = 2;
var loading = false;
var scrollHandling = {allow: true,reallow: function() {scrollHandling.allow = true;},delay: 20};
$(document).ready(function(){
	$('.article-container').append( '<div class="load-more"></div>' );
	var button = $('.article-container .load-more');
	$(".article-grouping:last").css("display", "block");
	$(".article-group .article").animate({opacity: 1}, 1000);
	$(window).scroll(function(){
		if( ! loading && scrollHandling.allow && page<=maxpages) {
			scrollHandling.allow = false;
			setTimeout(scrollHandling.reallow, scrollHandling.delay);
			var offset = $(".article-group:last").offset().top - $(window).scrollTop();
			if( 0 > offset ) {
				loading = true;
				$("html").addClass("wait");
				$(button).addClass("visible");
				var data = {action: 'nm_load_more',pg: page,c: cat};
				$.post(url, data, function(res) {
					if( res.success) {
						$('.article-container').append( res.data );
						$('.article-container').append( button );
						$(".article-grouping:last").slideDown(1000);
						$(".article-group .article").animate({opacity: 1}, 1000);
						page = page + 1;
						loading = false;
						$(button).removeClass("visible");
						$("html").removeClass("wait");
					} else {
						$(".load-more").html("The articles could not be loaded");
						$("html").removeClass("wait");
					}
				}).fail(function(xhr, textStatus, e) {
					// console.log(xhr.responseText);
					$(".load-more").html("There was an error loading the articles");
					$("html").removeClass("wait");
				});
			}
		}
		else
			$(button).removeClass("visible");
	});
})