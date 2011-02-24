/*!
 * Wanderfly Wordpress Plugin Scripts v0.1
 * http://partners.wanderfly.com/
 */

// Make sure that there's no conflict with old version of JQuery & JQuery UI,
var wf_jquery = $.noConflict(true);


var wf_plugin = {
	init: function() {
		
		var _this = this;
		
		// Init admin autocomplete,
		if(wf_jquery('#wf_destination_autocomplete').length > 0) {
			
			wf_jquery('#wf_destination_autocomplete').autocomplete({
				minLength: 3,
				source: function(request, response) {

					wf_jquery.ajax({
						url: wf_plugin_url+'/destination-ajax.php',
						data: {
							limit: 10,
							q: request.term,
							apikey: wf_api_key
						},
						timeout: 15000,
						success: function(data) {

							data = data.split("\n");
							dataArray = wf_plugin.parseAutocompleteData(data);

							dataArray = wf_plugin.buildDestinationLabel(dataArray);

							response(wf_jquery.map(dataArray, function(item) {
								return {
									label: item[3],
									value: item[0],
									value_id: item[1],
									value_type: item[2]
								}
							}));
						},
				        error: function(XMLHttpRequest, textStatus, errorThrown) {
				        	//Currently doing that for timeout error,
				        	//alert('sorry, server error');
				        }
					});

				},
				open: function() {
					//Select the first result;
					var autocomplete = wf_jquery(this).autocomplete("widget");
					autocomplete.find('a:first').trigger('mouseenter');

					wf_jquery(".ui-autocomplete li.ui-menu-item:odd a").addClass("ui-menu-item-alternate");
				},
				select: function(ui, i) {
					//Set the value into the hidden destinationID field,
					if(i.item) {
						_this.addDestination(i.item);
					}
				}
			}).data("autocomplete")._renderItem = function(ul, item ) {
			      return wf_jquery("<li></li>")
		           .data("item.autocomplete", item)
		           .append("<a>"+ item.label + "</a>")
		           .appendTo(ul);
		    };
			
			
			//Delete btn event,
			wf_jquery('#wf_destination_item_wrapper .btn-delete').bind('click', function() {
				_this.removeDestination();
			});
			
		}
		
	},
	
	parseAutocompleteData: function(data) {
		var dataArray = [];

		for(var i=0; i<data.length; i++) {
			if(data[i] != "") { dataArray[i] = data[i].split("|"); }
		}

		return dataArray;
	},
	
	buildDestinationLabel: function(destinations) {
		for(var i=0; i<destinations.length; i++) {
			if(destinations[i][2] == "COUNTRY") { destinations[i][3] = '<span class="country">COUNTRY</span>'+destinations[i][0]; }
			else if(destinations[i][2] == "DESTINATION") { destinations[i][3] = '<span class="city">CITY</span>'+destinations[i][0]; }
			else if(destinations[i][2] == "CONTINENT") { destinations[i][3] = '<span class="continent">CONTINENT</span>'+destinations[i][0]; }
			else if(destinations[i][2] == "REGION") { destinations[i][3] = '<span class="region">REGION</span>'+destinations[i][0]; }
		}
		return destinations;
	},
	
	addDestination: function(destination) {
		//Set the hidden value,
		wf_jquery('#wf_destination_val').val(destination.value_id+':'+destination.label);
		
		//Switch the visual stuff,
			//Hide autocomplete,
			wf_jquery('#wf_destination_search_wrapper').hide();
			
			//Show label name city with a delete button,
			wf_jquery('#wf_destination_item_wrapper .destination-name').html(destination.label);
			wf_jquery('#wf_destination_item_wrapper').show();
			
	},
	
	removeDestination: function() {
		//Set the hidden value to delete, so that it'll be deleted on post update,
		wf_jquery('#wf_destination_val').val('delete');
		
		//Hide label + delete button,
		wf_jquery('#wf_destination_item_wrapper').hide();
		wf_jquery('#wf_destination_item_wrapper .destination-name').html('');
		
		//Show the autocomplete,
		wf_jquery('#wf_destination_autocomplete').val('');
		wf_jquery('#wf_destination_search_wrapper').show();
		
	}
};

wf_jquery(document).ready(function() {
	// The user must provide an API KEY,
	if(wf_api_key != "")  { wf_plugin.init(); }
});


