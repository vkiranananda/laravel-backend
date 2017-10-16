

(function(w,doc) {
	var region = $('#post-select-region');
	if (region.length > 0) {
		var area = $('#Forms_area');

		setArea();
		
		function setArea(){
			area.find('option').filter(function() {
        		return this.value.length !== 0;
    		}).hide();
			if(region.val() != ''){
				area.find('option[data-region='+region.val()+']').show();
			}

		}
		region.change(function(){
			setArea();
			// /area.find('option').removeAttr('selected');
			area.find('option').removeAttr('selected').first().attr('selected', 'selected');
		});
	}
})(window,document);
