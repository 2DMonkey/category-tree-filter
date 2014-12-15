jQuery(document).ready(function($) {

	$('#categoryfilter').fastLiveFilter('#categorychecklist');

	$('.categorychecklist li, .category-checklist li').each(function() {
		// add empty space to each of li
		$(this).prepend("<a href=\"#\" class=\"ctf-cat-toggle\"></a>")
		
		// check if have children category
		if ($(this).children("ul").length > 0) {
			// hide children ul
			$(this).find("ul").hide();
			// add unfold class
			$(this).children(".ctf-cat-toggle").addClass('ctf-expand');
		}
	});
	ctf_category_scan();
	
	// quick edit
	$(".ptitle").focus(function() {
		ctf_category_scan();
	});
	
	$(".ctf-cat-toggle").click(function() {
		if ($(this).hasClass("ctf-expand")) {
			$(this).parent("li").children("ul").show();
			$(this).removeClass("ctf-expand");
			$(this).addClass("ctf-contract");
		} else if ($(this).hasClass("ctf-contract")) {
			$(this).parent("li").children("ul").hide();
			$(this).removeClass("ctf-contract");
			$(this).addClass("ctf-expand");
		}
		return false;
	});
	
	function ctf_category_scan() {
		$('.categorychecklist li, .category-checklist li').each(function() {
			// check if children checked
			if ($("> label > input[type=\"checkbox\"]", this).is(":checked")) {
				$(this).parents("ul").show();
				$(this).parents("li").each(function() {
					$(this).children(".ctf-cat-toggle").removeClass('ctf-expand');
					$(this).children(".ctf-cat-toggle").addClass('ctf-contract');
				});
			}
		});
	}
	
	$(".expand-all").click(function() {
		$(this).parent().removeClass("tabs");
		if ($(this).hasClass("ctf-expand-all")) {
			$(this).text('Expand All');
			$(this).removeClass("ctf-expand-all");
			$('.ctf-cat-toggle').each(function(i, obj) {
				if ($(this).hasClass("ctf-contract")) {
					$(this).parent("li").children("ul").hide();
					$(this).removeClass("ctf-contract");
					$(this).addClass("ctf-expand");
				}
			});
		} else {
			$(this).addClass("ctf-expand-all");
			$(this).text('Collapse All');
			$('.ctf-cat-toggle').each(function(i, obj) {
				if ($(this).hasClass("ctf-expand")) {
					$(this).parent("li").children("ul").show();
					$(this).removeClass("ctf-expand");
					$(this).addClass("ctf-contract");
				}
			});
		}
	});
	
	
	
});