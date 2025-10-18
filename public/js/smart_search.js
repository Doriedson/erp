/**
 * Event to focus on product_search.
 */
 $(document).on("focus", ".smart_search", function(event) {

	var dropdownlist = $(this).closest('.autocomplete-dropdown').find('.dropdown-list');

	// if (dropdownlist.html().length > 0) {
		dropdownlist.show();
	// }

	this.select();
});

/**
 * Event to focusout on product_search.
 */
 $(document).on("focusout", ".smart_search", function(event) {

	// console.log("focusout smart_search");

	var dropdown_list = $(this).closest('.autocomplete-dropdown').find(".dropdown-list");

	// console.log(dropdown_list.hasClass("mouseover"));

	if (!(dropdown_list.hasClass('mouseover'))) {

		dropdown_list.hide();
	}
});

/**
 * Event mouseover to highlight item from list search autocomplete.
 */
$(document).on("mouseenter", ".dropdown-item", function(event) {

	// console.log("pressed " + event.keyCode);

	var dropdown_list = $(this).closest(".dropdown-list");

	$("li", dropdown_list).removeClass("selected");

	$(this).addClass("selected");

	dropdown_list.addClass("mouseover");
});

/**
 * Event mouseout to remove class mouseout for focusout.
 */
 $(document).on("mouseleave", ".dropdown-list", function(event) {

	// console.log("mouse leave dropdown-list");

	var field = $(this).closest('.autocomplete-dropdown').find(".smart-search");
	
	$(this).removeClass("mouseover");

	if (!field.is(':focus')) {

		$(this).hide();
	}
});

/**
 * Event .
 */
 $(document).on("click", ".dropdown-item", function(event) {

	// console.log("dropdown-item click");

	var field = $(this).closest('.autocomplete-dropdown').find(".smart-search");

	var dropdown_list = $(this).closest('.dropdown-list');

	field.data("sku", $(this).data("sku"));
	field.data("descricao", $(this).data("descricao"));

	dropdown_list.hide();

	var next = field.data('focus_next');

	if (next) {

		field.val($(this).data("descricao"));
		field.closest('form').find(next).focus();

	} else {

		$(this).closest('form').submit();
	}
});

function scrollParentToChild(parent, child) {

	// Where is the parent on page
	var parentRect = parent.getBoundingClientRect();
	// What can you see?
	var parentViewableArea = {
		height: parent.clientHeight,
		width: parent.clientWidth
	};

	// Where is the child
	var childRect = child.getBoundingClientRect();
	// Is the child viewable?
	var isViewable = (childRect.top >= parentRect.top) && (childRect.bottom <= parentRect.top + parentViewableArea.height);

	// if you can't see the child try to scroll parent
	if (!isViewable) {
			// Should we scroll using top or bottom? Find the smaller ABS adjustment
			const scrollTop = childRect.top - parentRect.top;
			const scrollBot = childRect.bottom - parentRect.bottom;
			if (Math.abs(scrollTop) < Math.abs(scrollBot)) {
				// we're near the top of the list
				parent.scrollTop += scrollTop;
			} else {
				// we're near the bottom of the list
				parent.scrollTop += scrollBot;
			}
	}

}

/**
 * Event keyup to select item on list search autocomplete.
 */
 $(document).on("keyup", ".smart_search", function(event) {

	// console.log("pressed func " + event.keyCode);
	$(this).data("sku", "");

	var ul = $(this).closest('.autocomplete-dropdown').find('.dropdown-list');

	if ($("li", ul).length == 0) return;

	switch (event.keyCode) {

		case 38: // up

			// console.log('keyup ' + $(this).hasClass('selecting'))

			var selected = $(".selected", ul);

			if(selected.length == 0) {
				
				selected = $("li:first", ul);

			} else {

				if ($("li", ul).length > 1) {
				
					$("li", ul).removeClass("selected");

					// if there is no element before the selected one, we select the last one
					if (selected.prev().length == 0) {

						selected = selected.siblings().last();

					} else { // otherwise we just select the next one

						selected = selected.prev();
					}
				}
			}

			selected.addClass("selected");
			// selected[0].scrollIntoView(true);
			positionFromTopOfScrollableDiv = selected[0].offsetTop;
			ul[0].scrollTop = positionFromTopOfScrollableDiv

			$(this).data("sku", ul.find("li.selected").data("sku"));
			$(this).data("descricao", ul.find("li.selected").data("descricao"));
			$(this).val(ul.find("li.selected").data("descricao"));

		break;

		case 40: // down

			var selected = $(".selected", ul);

			if(selected.length == 0) {
				
				// $("li:first", ul).addClass("selected");
				selected = $("li:first", ul);

			} else {

				if ($("li", ul).length > 1) {

					$("li", ul).removeClass("selected");

					// if there is no element before the selected one, we select the last one
					if (selected.next().length == 0) {

						// selected.siblings().first().addClass("selected");
						selected = selected.siblings().first();

					} else { // otherwise we just select the next one

						// selected.next().addClass("selected");
						selected = selected.next();
					}
				}
			}

			selected.addClass("selected");
			// selected[0].scrollIntoView(false);
			positionFromTopOfScrollableDiv = selected[0].offsetTop;
			ul[0].scrollTop = positionFromTopOfScrollableDiv

			$(this).data("sku", ul.find("li.selected").data("sku"));
			$(this).data("descricao", ul.find("li.selected").data("descricao"));
			$(this).val(ul.find("li.selected").data("descricao"));

		break;
	}
});