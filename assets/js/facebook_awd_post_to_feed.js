/**
 * 
 * @author alexhermann
 *
 */
jQuery(document).ready(function($){
	//create eventlistener on the element
	$('.AWD_facebook_post_to_feed_button').live('click',function(e){
		e.preventDefault();
		$(this);
		var $this = $(this);
		var data = $this.data();
		AWD_facebook.post_to_feed(data);
	});
});


AWD_facebook.post_to_feed = function(data){
	//call the FB.ui
	FB.ui({
		method: 'feed',
        link: data.link,
	},
	//call the user defined callback
	function(response){
		if(data.callback){
			var AWD_actions_callback = window[data.callback];
			if(jQuery.isFunction(AWD_actions_callback))
				AWD_actions_callback(response, data);
		}
	});
};