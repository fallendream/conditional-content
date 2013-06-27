var $ = jQuery;

$(function() {
	$('#widgets-right').on('click', '.ccwidget-remove-button', function() {
		// Do nothing if tis the last element in the table.
		var childs = $(this).parents('.ccwidget-rules').children().length;
		if(childs == 1) {
			return;
		}
		
		$(this).parents('.ccwidget-rule').remove();
	});
	
	$('#widgets-right').on('click', '.ccwidget-add-button', function(event){
		event.preventDefault();			
		// Copy with events.
		var rule = $(this).parents('div.widget-content').find('.ccwidget-rule:first-child').clone(true);
		rule.find('input').val('');
		$(this).parents('div.widget-content').find('.ccwidget-rules').append(rule);
		return false;
	});
});