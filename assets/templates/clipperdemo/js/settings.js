/* [jQuery] */
jQuery(document).ready(function(){

	/* [Slideshow] */
	jQuery("#slideshow ul").responsiveSlides({
		auto: true,             // Boolean: Animate automatically, true or false
		speed: 500,             // Integer: Speed of the transition, in milliseconds
		timeout: 4000,          // Integer: Time between slide transitions, in milliseconds
		pager: true,            // Boolean: Show pager, true or false
		nav: true,              // Boolean: Show navigation, true or false
		random: false,          // Boolean: Randomize the order of the slides, true or false
		pause: false,           // Boolean: Pause on hover, true or false
		pauseControls: true,    // Boolean: Pause when hovering controls, true or false
		prevText: "Previous",   // String: Text for the "previous" button
		nextText: "Next",       // String: Text for the "next" button
		maxwidth: "",           // Integer: Max-width of the slideshow, in pixels
		navContainer: "",       // Selector: Where controls should be appended to, default is after the 'ul'
		manualControls: "",     // Selector: Declare custom pager navigation
		namespace: "rslides",   // String: Change the default namespace used
		before: function(){},   // Function: Before callback
		after: function(){}     // Function: After callback
	});	
	/* [/End Slideshow] */	
	
	/* [jQuery Form Validation] */
    jQuery('#contactform').isHappy({
		fields: {
            // reference the field you're talking about, probably by `id`
            // but you could certainly do jQuery('[name=name]') as well.
            '.required-name': {
				required: true,
				message: 'Please enter your name'
            },
            '.required-email': {
				required: true,
				message: 'Please enter a valid email address',
				test: happy.email
            },
            '.required-subject': {
				required: true,
				message: 'Please enter a subject'
            },
            '.required-text': {
				required: true,
				message: 'Please enter your message'
            }			
		}
    });	
	/* [/End jQuery Form Validation] */		
	
	// Add Class "has-children" if parent "li" has children "ul"
	jQuery("#header-menu nav > ul > li:has(ul)").addClass('has-children');
	// Add span to "a" if parent "li" has children "ul", this will be used to build
	// a button to expand the menu when viewing the website on Mobiles phones
	jQuery("#header-menu nav > ul > li:has(ul) > a").append('<span></span>');
	
	/* [Media Queries] */
	enquire.register("only screen and (min-width: 0) and (max-width: 799px)", {
		setup : function() {
			// Show/Hide Mobile Menu	
			jQuery('#header-menu-mobile').click(function () {
				jQuery('#header-menu-mobile,#header-menu nav > ul').toggleClass('is-open');
            });	
			// Show/Hide Mobile Search
			jQuery('#header-search-mobile').click(function () { jQuery('#header-search-form,#header-search-mobile,#header-search').toggleClass('is-open'); });
		},
		match : function() {
			// Slide Down/Up the secondary nav for mobile
			jQuery('#header-menu nav > ul > li.has-children > a > span').click(function(e) {
				e.preventDefault();
				jQuery(this).parent().parent().children('ul').slideToggle('fast');
				jQuery(this).toggleClass('is-open');
			});			
		},
		unmatch : function() {
			// Disable Slide Down/Up if you exit Mobile view
			jQuery('#header-menu nav > ul > li.has-children > a > span').unbind();
		}  
	}).register("only screen and (min-width: 800px)", {
		match : function() {
			// Dropdown Menu (delay)
			jQuery("#header-menu nav > ul > li").hover(function () {
				jQuery(this).addClass('hovering');
			},
			function () { 
				jQuery(this).removeClass('hovering');
			});			
		},
		unmatch : function() {
			// Disable dropdown menu (for mobile)
			jQuery("#header-menu nav > ul > li").unbind('mouseenter mouseleave');
		} 
    });
	/* [/End Media Queries] */
	
});
/* [/End jQuery] */