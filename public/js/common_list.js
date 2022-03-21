$(document).ready(function(){
	
	$('.action_delete').on('click' , function(){
		
		return confirm('Are you sure you want to delete this item ?');
	} )
});