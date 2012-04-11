(function($){
    
    var jq=jQuery;
    jq(document).ready(function (){
        //
    
   /* When a navigation tab is clicked - e.g. | All Groups | My Groups | */
	jq('div.member-user-nav').click( function(event) {
		

		var target = jq(event.target).parent();

		if ( 'LI' == event.target.parentNode.nodeName && !target.hasClass('last') ) {
			var css_id = target.attr('id').split( '-' );
			var object = css_id[0];

			//if ( 'activity' == object )
				//return false;

			var scope = css_id[1];
			var filter = jq("#" + object + "-order-select select").val();
			var search_terms = jq("#" + object + "_search").val();

			bpcustom_filter_request( object, filter, scope, 'div.' + object, search_terms, 1, jq.cookie('bp-' + object + '-extras') );

			return false;
		}
	});
       	jq('div#subnav li a').live('click', function(event) {
		
                var cur_link=jq(event.target);//there is something bad, the id naming convention of activity is really bad
		var li = jq(event.target).parent();//it is li

		if ( 'LI' == event.target.parentNode.nodeName && !li.hasClass('last') ) {
                    
			var css_id = li.attr('id');//.split( '-' );
			//replace personal li in the attribute
                        css_id=css_id.replace('-personal-li','');
                        //replace the 'activity-';
                        css_id=css_id.replace('activity-','');
                        var extras=css_id;//this will be action
                        
                        //now, let us talk about the current component/object
                        var parent_nav_li= jq(".member-user-nav li.selected").get(0);
                       // parent_nav_li=jq(parent_nav_li);
                        
                        css_id=jq(parent_nav_li).attr('id').split( '-' );
                        
                        
                        
                        var object = css_id[0];

			//if ( 'activity' == object )
				//return false;

			var scope = css_id[1];
			var filter = jq("#" + object + "-order-select select").val();
			var search_terms = jq("#" + object + "_search").val();

			bpcustom_filter_request( object, filter, scope, 'div.' + object, search_terms, 1, extras );

			return false;
		}
	});  
        
    });     
        
 function bpcustom_filter_request( object, filter, scope, target, search_terms, page, extras ) {
        
        
	/* Set the correct selected nav and filter */
       
	jq('div.member-user-nav li').each( function() {
		jq(this).removeClass('selected current');
              
	});
       
        jq('div.member-user-nav li#' + object + '-' + scope +'-li'+ ', div.member-user-nav li.current').addClass('selected');
        
	jq('div.member-user-nav li.selected').addClass('loading');
        
	jq('div.member-user-nav option[value="' + filter + '"]').prop( 'selected', true );

	if ( 'activity' == object ){
                bpcustom_activity_request(scope, filter,extras);
                return false; 
            }
	
	if ( jq.query.get('s') && !search_terms )
		search_terms = jq.query.get('s');

	if ( null == scope )
		scope = 'all';

	/* Save the settings we want to remain persistent to a cookie */
	jq.cookie( 'bp-' + object + '-scope', scope, {path: '/'} );
	jq.cookie( 'bp-' + object + '-filter', filter, {path: '/'} );
	jq.cookie( 'bp-' + object + '-extras', extras, {path: '/'} );

       
	if ( 'friends' == object )
		object = 'members';

	if ( bp_ajax_request )
		bp_ajax_request.abort();

	bp_ajax_request = jq.post( ajaxurl, {
		action: 'profile_'+object + '_filter',
		'cookie': encodeURIComponent(document.cookie),
		'object': object,
		'filter': filter,
		'search_terms': search_terms,
		'scope': scope,
		'page': page,
		'extras': extras
	},
	function(response)
	{
		jq(target).fadeOut( 100, function() {
			jq(this).html(response);
			jq(this).fadeIn(100);
	 	});
		jq('div.member-user-nav li.selected').removeClass('loading');
	});
}

function bpcustom_activity_request(scope, filter,extras) {
	/* Save the type and filter to a session cookie */
	jq.cookie( 'bp-activity-scope', scope, {path: '/'} );
	jq.cookie( 'bp-activity-filter', filter, {path: '/'} );
	jq.cookie( 'bp-activity-oldestpage', 1, {path: '/'} );

	
	

	/* Reload the activity stream based on the selection */
	jq('.widget_bp_activity_widget h2 span.ajax-loader').show();

	if ( bp_ajax_request )
		bp_ajax_request.abort();

	bp_ajax_request = jq.post( ajaxurl, {
		action: 'profile_activity_filter',
		'cookie': encodeURIComponent(document.cookie),
		'_wpnonce_activity_filter': jq("input#_wpnonce_activity_filter").val(),
		'scope': scope,
		'filter': filter,
                'object':'activity',
                'extras':extras
	},
	function(response)
	{
		jq('.widget_bp_activity_widget h2 span.ajax-loader').hide();

		jq('div.activity').fadeOut( 100, function() {
			jq(this).html(response.contents);
			jq(this).fadeIn(100);

			/* Selectively hide comments */
			bp_dtheme_hide_comments();
		});

		jq('div.member-user-nav li.selected').removeClass('loading');

	},'json' ) ;
}
   
})(jQuery);