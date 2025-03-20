jQuery(document).ready(function($){
	Coloris({
		el: '.coloris'
	  });
	// custom opening time
	$(document).on('change', 'select[name="new_custome_date_status[]"]', function(e) {
		if(this.value === "close")
		{
			$(this).parent().parent().find('option.should_disable').attr('disabled','disabled')
			$(this).parent().parent().find('select.select_time_type').val("full_day");
			$(this).parent().parent().find('input.start_time_picker').hide();
			$(this).parent().parent().find('input.end_time_picker').hide();
		}
		else
		{
			$(this).parent().parent().find('option.should_disable').removeAttr('disabled');
		}
	});

	$(document).on('change', 'select[name="new_custome_date_type[]"]', function(e) {
		if(this.value === "multiple")
		{
			$(this).parent().parent().find('input.end_date_picker').show();
		}
		else
		{
			$(this).parent().parent().find('input.end_date_picker').hide();
		}
	});

	$(document).on('change', 'select[name="new_custome_date_time_type[]"]', function(e) {
		if(this.value === "time_to_time")
		{
			$(this).parent().parent().find('input.start_time_picker').show();
			$(this).parent().parent().find('input.end_time_picker').show();
		}
		else
		{
			$(this).parent().parent().find('input.start_time_picker').hide();
			$(this).parent().parent().find('input.end_time_picker').hide();
		}
	});

	// for opening time delevery and in shop
	$(document).on('change', 'input[name="dsmart_method_ship"]', function(e) {
		//e.preventDefault();
		if(this.checked){
			$("#opening_time_delivery").show();
			$("#closing_time_delivery").show();
			$("#delay_time_delivery").show();
			$("#time_delivery_step").show();
			console.log("on");
		}else{
			$("#opening_time_delivery").hide();
			$("#closing_time_delivery").hide();
			$("#delay_time_delivery").hide();
			$("#time_delivery_step").hide();
			console.log("off");
		}
		
	  });

	  	// for opening time takeway and in shop
	$(document).on('change', 'input[name="dsmart_method_direct"]', function(e) {
		//e.preventDefault();
		if(this.checked){
			$("#opening_time_in_shop").show();
			$("#closing_time_in_shop").show();
			$("#delay_time_in_shop").show();
			$("#time_takeaway_step").show();
			console.log("on");
		}else{
			$("#opening_time_in_shop").hide();
			$("#closing_time_in_shop").hide();
			$("#delay_time_in_shop").hide();
			$("#time_takeaway_step").hide();
			console.log("off");
		}
		
	  });

	  // rabatt on/off
	  $(document).on('change', 'input[name="discount_cod"]', function(e) {
		//e.preventDefault();
		if(this.checked){
			$("#rabatt_delivery_type").show();
			$("#discount-group").show();
		}else{
			$("#rabatt_delivery_type").hide();
			$("#discount-group").hide();
		}
	  });

		// min free ship
		$(document).on('change', 'input[name="dsmart_min_order_free_checkbox"]', function(e) {
		//e.preventDefault();
		if($('input[name=dsmart_min_order_free_checkbox]:checked' ).val() === "1"){
			$('input[name="dsmart_min_order_free"]').show();
		}else{
			$('input[name="dsmart_min_order_free"]').hide();
		}
		});

	// rabatt on/off
	$(document).on('change', 'input[name="discount_shop"]', function(e) {
		//e.preventDefault();
		if(this.checked){
			$("#rabatt_in_shop_type").show();
			$("#discount-group-2").show();
		}else{
			$("#rabatt_in_shop_type").hide();
			$("#discount-group-2").hide();
		}
		});


    const $body = $("body");
	//add gallery
	var file_frame;
	$(document).on('click', '#gallery-metabox a.gallery-add', function(e) {
	   e.preventDefault();
	   if (file_frame) file_frame.close();
	   file_frame = wp.media.frames.file_frame = wp.media({
	      title: $(this).data('uploader-title'),
	      button: {
	      text: $(this).data('uploader-button-text'),
	   },
	   multiple: true
	 });

	file_frame.on('select', function() {
	   var listIndex = $('#gallery-metabox-list li').index($('#gallery-metabox-list li:last')),
	   selection = file_frame.state().get('selection');

	selection.map(function(attachment, i) {
	   attachment = attachment.toJSON(),
	   index = listIndex + (i + 1);
	$('#gallery-metabox-list').append('<li><input type="hidden" name="book_gallery_id[' + index + ']" value="' + attachment.id + '"><img class="image-preview" src="' + attachment.sizes.thumbnail.url + '"><a class="change-image button button-small" href="#" data-uploader-title="Change image" data-uploader-button-text="Change image">Change image</a><br><small><a class="remove-image" href="#">Remove image</a></small></li>');
	 });
	 });
	 fnSortable();
	 file_frame.open();

	});
	$(document).on('click', '#gallery-metabox a.change-image', function(e) {
	  e.preventDefault();
	  var that = $(this);
	  if (file_frame) file_frame.close();
	  file_frame = wp.media.frames.file_frame = wp.media({
	     title: $(this).data('uploader-title'),
	     button: {
	     text: $(this).data('uploader-button-text'),
	  },
	  multiple: false
	  });

	  file_frame.on( 'select', function() {
	     attachment = file_frame.state().get('selection').first().toJSON();
	     that.parent().find('input:hidden').attr('value', attachment.id);
	     that.parent().find('img.image-preview').attr('src', attachment.sizes.thumbnail.url);
	  });
	  file_frame.open();
	});
	$(document).on('click', '#gallery-metabox a.remove-image', function(e) {
	   e.preventDefault();
	   $(this).parents('li').animate({ opacity: 0 }, 200, function() {
	     $(this).remove();
	      resetIndex();
	    });
	 });
	function resetIndex() {
	    $('#gallery-metabox-list li').each(function(i) {
	       $(this).find('input:hidden').attr('name', 'book_gallery_id[' + i + ']');
	    });
	}
	function fnSortable() {
	    $('#gallery-metabox-list').sortable({
	       opacity: 0.6,
	       stop: function() {
	          resetIndex();
	       }
	    });
	}
	$('.timepicker').timepicker({ 'timeFormat': 'H:i' });
	$('.datepicker').datepicker({
		changeYear: true, 
		dateFormat: 'dd-mm-yy',
	});
	$(".edit-tag-actions input[type='submit']").on("click",function(){
		console.log("zzzzzz");
		$("#edittag").submit() 
	});
	$(".remove-selected-option").on("click",function(e){
		e.preventDefault();
		$("select.dsmart-select option").removeAttr("selected");
	});
	$(".dsmart-form").on("click",".remove-row",function(){
		$(this).parents("tr").remove();
	});

	$(".table-custom-date-tax .remove-row").on("click",function(){
		$(this).parents("tr").get(0).remove();
	});
	$(".dsmart-form .table-custom-date .add-new-row").on("click",function(){
		console.log("Zzzzzzz")
		let row = $(this).parent().find('tbody tr.hidden-field' ).clone(true);
		row.removeClass("hidden-field");
		row.find("input").prop("required",true).attr("id","");
		row.insertAfter($(this).parent().find("tbody tr:last") );
		row.find('input.datepicker').datepicker("destroy");
		row.find('input.datepicker').removeClass('hasDatepicker').removeData('datepicker').unbind().datepicker({
			changeYear: true, 
			dateFormat: 'dd-mm-yy',
		});
		let id = row.find('input.datepicker').attr("id");
		row.attr("id","tr"+id);
	});

	$(".dsmart-form .table-custom-date .import-holiday").on("click",function(){
		var parent = $(this).parent().parent();
		let year = $('select[name="holiday_year"]').find(":selected").val();
		let region = $('select[name="holiday_region"]').find(":selected").val();
		console.log(parent);
		$.get( "https://digidates.de/api/v1/germanpublicholidays?year="+year+"&region=" + region, function( data ) {
			
			Object.keys(data).forEach(function(key) {
				console.log(key)
				let row = parent.find('table tbody tr.hidden-field' ).clone(true);
				console.log(row);
				row.removeClass("hidden-field");
				row.find("input").prop("required",true).attr("id","");
				row.insertAfter(parent.find("table tbody tr:last") );
				row.find('input.datepicker').datepicker("destroy");
				row.find('input.datepicker').removeClass('hasDatepicker').removeData('datepicker').unbind().datepicker({
					changeYear: true, 
					dateFormat: 'dd-mm-yy'
				});
				let id = row.find('input.datepicker').attr("id");
				row.attr("id","tr"+id);

				// fill date time
				let holiday = new Date(key);
				let start = row.find(".start_date_picker").datepicker( "setDate", holiday );
			})
		});
	});

	$(".table-custom-date-tax .import-holiday").on("click",function(){
		var parent = $(this).parent().parent();
		let year = $('select[name="holiday_year"]').find(":selected").val();
		let region = $('select[name="holiday_region"]').find(":selected").val();
		console.log(parent);
		$.get( "https://digidates.de/api/v1/germanpublicholidays?year="+year+"&region=" + region, function( data ) {
			
			Object.keys(data).forEach(function(key) {
				console.log(key)
				let row = parent.find('table tbody tr.hidden-field' ).clone(true);
				console.log(row);
				row.removeClass("hidden-field");
				row.find("input").prop("required",true).attr("id","");
				row.insertAfter(parent.find("table tbody tr:last") );
				row.find('input.datepicker').datepicker("destroy");
				row.find('input.datepicker').removeClass('hasDatepicker').removeData('datepicker').unbind().datepicker({
					changeYear: true, 
					dateFormat: 'dd-mm-yy'
				});
				let id = row.find('input.datepicker').attr("id");
				row.attr("id","tr"+id);

				// fill date time
				let holiday = new Date(key);
				let start = row.find(".start_date_picker").datepicker( "setDate", holiday );
			})
		});
	});

	$(".table-custom-date-tax .add-new-row").on("click",function(){
		console.log("Zzzzzzz")
		let row = $(this).parent().find('tbody tr.hidden-field' ).clone(true);
		row.removeClass("hidden-field");
		row.find("input").prop("required",true).attr("id","");
		row.insertAfter($(this).parent().find("tbody tr:last") );
		row.find('input.datepicker').datepicker("destroy");
		row.find('input.datepicker').removeClass('hasDatepicker').removeData('datepicker').unbind().datepicker({
			changeYear: true, 
			dateFormat: 'dd-mm-yy',
		});
		let id = row.find('input.datepicker').attr("id");
		row.attr("id","tr"+id);
	});
	$(".dsmart-form .table-custom-closed .add-new-row").on("click",function(){
		var parent = $(this).parents(".table-custom-closed");
		let row = $(this).parents(".table-custom-closed").find( 'tbody tr.hidden-field' ).clone(true);
		row.removeClass("hidden-field");
		row.find("input").prop("required",true).attr("id","");
		row.insertAfter( parent.find("tbody tr:last") );
		row.find('input.datepicker').datepicker("destroy");
		row.find('input.datepicker').removeClass('hasDatepicker').removeData('datepicker').unbind().datepicker({
			changeYear: true, 
			dateFormat: 'dd-mm-yy',
		});
	});

	$(".dsmart-form .add-new-row2").on("click",function(){
		let row = $('.list-shipping-fee .fee-item:nth-child(2)').clone();
		row.find("input").val("");
		row.append('<span class="remove-row2">x</span>');
		$(".list-shipping-fee").append(row);
	});
	$("body").on("click",".remove-row2",function(){
		$(this).parents(".fee-item").remove();
	});

	$(".dsmart-form .add-new-row3").on("click",function(){
		let row = '<div class="method-item"><input type="text" name="dsmart_custom_method[]" class="widefat" placeholder="Enter custom payment method" value="" required/><span class="remove-row3">x</span></div>';
		$(".list-method").append(row);
	});
	$("body").on("click",".remove-row3",function(){
		$(this).parents(".method-item").remove();
	});

	/////////////////////////////////////
    //  PRODUCT TRUE/FALSE FIELD
    /////////////////////////////////////
	 $("#product-info .vegetarian label").click(function(){
	 	console.log('adsdb');
        if($('#product-info .vegetarian input[name="vegetarian"]').is(":checked")){
            $('#product-info .vegetarian input[name="vegetarian"]').val(1);
        }else{
            $('#product-info .vegetarian input[name="vegetarian"]').val(0);
        }
    });

	 /////////////////////////////////////
    //  PRODUCT ACF FIELD
    /////////////////////////////////////
	$(".dsmart-clone-fields").on("click", function () {
        let $this = $(this);
        let $template = $($this.data('template'));
        $this.parents(".acf-table").find("tbody").append($template); 
        var i = 0;
         $this.parents(".acf-table").find('.fields-group').each(function(i){
        	i = i+1;
        	let radio_name = $(this).find("input[type='radio']").attr("data-name");
        	radio_name = radio_name+Number(i);
        	$(this).find("input[type='radio']").attr("name",radio_name);
        });
        $(".dsmart-remove-fields").on("click", function () {
            var $this = $(this);
            $this.parents('.fields-group').remove();
            return false;
        });

        $template.find('input:first-child').focus();
        return false;
    });

    $(document).on("click", ".dsmart-remove-fields", function () {
    	console.log('a');
        var $this = $(this);
        $this.parents('.fields-group').remove();
        return false;
    });
    //add time 1
    $("#discount-group .add-time").on("click", function(){
    	let id = $(this).parent().attr("id");
    	$(".add-time-form .object").val(id);
    	$(".add-time-form").fadeIn();
    });
    $("#custom-discount-group .add-time").on("click", function(){
    	let id = $(this).parents("tr").attr("id");
    	$(".add-time-form .object").val(id);
    	$(".add-time-form").fadeIn();
    });
    $(".add-time-form").on("click",".btn-cancel", function(){
    	$(".add-time-form").fadeOut();
    	$(".add-time-form input").val("");
    });
    $(".add-time-form").on("click",".btn-ok", function(){
    	let from =  $(this).parent().children("input[name='discount_form_mo']").val();
    	let to =  $(this).parent().children("input[name='discount_to_mo']").val();
    	let id = $(this).parent().children(".object").val();
    	let discount_time = $("#"+id).find("input.multi_timepicker").val();
    	var stt = new Date("November 13, 2013 " + from);
		stt = stt.getTime();
		var endt = new Date("November 13, 2013 " + to);
		endt = endt.getTime();
    	if(from === '' || to === ''){
    		$(this).parent().children(".message").text(bookingVars.time_discount_error1).removeClass("hidden");
    	}else if(from === to){
    		$(this).parent().children(".message").text(bookingVars.time_discount_error2).removeClass("hidden");
    	}else if(stt > endt){	
    		$(this).parent().children(".message").text(bookingVars.time_discount_error3).removeClass("hidden");
    	}else{
    		$(this).children(".message").addClass("hidden");
    		let input_val = from+"-"+to;
    		if(discount_time == ''){
    			$("#"+id).find("input.multi_timepicker").val(input_val);
    		}else{
    			discount_time_Arr = discount_time.split(",");
    			if(jQuery.inArray(input_val, discount_time_Arr) !== -1){

    			}else{
    				discount_time_Arr.push(input_val);
    			}
    			$("#"+id).find("input.multi_timepicker").val(discount_time_Arr.toString());
    		}
    	}    	
    });
    //addtime 2
    $("#discount-group-2 .add-time-2").on("click", function(){
    	let id = $(this).parent().attr("id");
    	$(".add-time-form-2 .object").val(id);
    	$(".add-time-form-2").fadeIn();
    });
    $(".add-time-form-2").on("click",".btn-cancel", function(){
    	$(".add-time-form-2").fadeOut();
    	$(".add-time-form-2 input").val("");
    });
    $(".add-time-form-2").on("click",".btn-ok", function(){
    	let from =  $(this).parent().children("input[name='discount_form_mo']").val();
    	let to =  $(this).parent().children("input[name='discount_to_mo']").val();
    	let id = $(this).parent().children(".object").val();
    	let discount_time = $("#"+id).find("input.multi_timepicker").val();
    	var stt = new Date("November 13, 2013 " + from);
		stt = stt.getTime();
		var endt = new Date("November 13, 2013 " + to);
		endt = endt.getTime();
    	if(from === '' || to === ''){
    		$(this).parent().children(".message").text(bookingVars.time_discount_error1).removeClass("hidden");
    	}else if(from === to){
    		$(this).parent().children(".message").text(bookingVars.time_discount_error2).removeClass("hidden");
    	}else if(stt > endt){	
    		$(this).parent().children(".message").text(bookingVars.time_discount_error3).removeClass("hidden");
    	}else{
    		$(this).children(".message").addClass("hidden");
    		let input_val = from+"-"+to;
    		if(discount_time == ''){
    			$("#"+id).find("input.multi_timepicker").val(input_val);
    		}else{
    			discount_time_Arr = discount_time.split(",");
    			if(jQuery.inArray(input_val, discount_time_Arr) !== -1){

    			}else{
    				discount_time_Arr.push(input_val);
    			}
    			$("#"+id).find("input.multi_timepicker").val(discount_time_Arr.toString());
    		}
    	}    	
    });
    $(".reset-order").on("click",function(){
    	if(confirm("Möchten Sie Ihre zweite Bestellnummer zurücksetzen?")){
    		$.ajax({
                type: 'POST',       
                url: bookingVars.ajaxurl,
                data:{
                    'action':'reset_your_order_number',
                },
                success:function(data){
                    alert('Reset successful!');
                }
            });
    	}
    });
    $("#export-excel").on("click",function(){
        if(confirm("Möchten Sie Ihre Aufträge exportieren?")){
            $.ajax({
                type: 'POST',    
                dataType: 'json',   
                url: bookingVars.ajaxurl,
                data:{
                    'action':'export_excel',
                },
                success:function(data){
                    window.location.href = data.url;
                    console.log(data);
                }
            });
        }
    });
    $("body").on("click",".add-new-date",function(){
        var html = `<div class="item-date new-date">
            <select class="widefat" name="time_date[]" required>
                <option value="mo">Montag</option>
                <option value="tu">Dienstag</option>
                <option value="we">Mittwoch</option>
                <option value="th">Donnerstag</option>
                <option value="fr">Freitag</option>
                <option value="sa">Samstag</option>
                <option value="su">Sonntag</option>
            </select>
            <input type="text" name="time_open[]" class="widefat timepicker" placeholder="Open time" value="" autocomplete="off"/>
            <input type="text" name="time_close[]" class="widefat timepicker" placeholder="Close time" value="" autocomplete="off"/>
            <span class="remove-date">x</span>
        </div>`;
        $(".list-tax-date").append(html);
        $(".list-tax-date .new-date .timepicker").timepicker({ 'timeFormat': 'H:i' });
        $(".list-tax-date .item-date").removeClass("new-date");
    });
    $("body").on("click",".remove-date",function(){
        $(this).parents(".item-date").remove();
    });
    $body.on("click",".add-new-row-zipcode",function(){
        var data = `<tr>
            <td><input type="text" name="zipcode[]" class="widefat" value="" autocomplete="off" required/></td>
            <td><input type="text" name="minium_order[]" class="widefat" value="" autocomplete="off" required/></td>
            <td><input type="text" name="zipcode_price[]" class="widefat" value="" autocomplete="off" required/></td>
            <td><span class="button remove-row">Löschen</span></td>
        </tr>`;
        $(".table-zipcode tbody").append(data);
    });
    $(".delete-all-order").on("click",function(){
        if(confirm("Do you want to delete all orders?")){
            $.ajax({
                type: 'POST',    
                dataType: 'json',   
                url: bookingVars.ajaxurl,
                data:{
                    'action':'d_delete_orders',
                    'type': 'all'
                },
                success:function(data){
					console.log(data);
                    alert(data.message);
                }
            });
        }
    });
    /*$(".delete-spe-order").on("click",function(){
        var date = $("#order-date").val();
        if(date == "" || parseInt(date) < 1){
            alert("Please enter date");
            return false;
        }
        if(confirm("Do you want to delete all orders more than "+date+" dates?")){
            $.ajax({
                type: 'POST',    
                dataType: 'json',   
                url: bookingVars.ajaxurl,
                data:{
                    'action':'d_delete_orders',
                    'type': 'spe',
                    'date' : date
                },
                success:function(data){
                    alert(data.message);
                }
            });
        }
    });*/
});