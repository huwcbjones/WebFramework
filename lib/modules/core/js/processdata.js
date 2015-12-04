/**
 * Data Processor
 *
 *
 * @category	 Core.ProcessData
 * @site			 www.biggleswadesc.org
 * @package		 core.processdata.js
 * @author		 Huw Jones <huwcbjones@gmail.com>
 * @copyright	 2014 Huw Jones
*/

/*
 */
function processData(url){
	$("#alert_working").removeClass("hidden");
	$.getJSON(url, {}, function(data) {
		if (data.status == 1) {
			window.location.href = data.url;
		} else {
			$(".alert").fadeOut(500).parent().remove();
			$($.parseHTML(data.msg)).css("display", "none").insertAfter("#status_bar").fadeIn();
			$(".alert").each(function(i,e){
				clearStatusMessage(e.id);
			});
		}
	})
	.always(function(){
		$("#alert_working").addClass("hidden");
	});
}
function processForm(frm, btn, sender, callback){
	$("#alert_working").removeClass("hidden");
	callback	= (typeof callback	=== "undefined") ? ""										: callback;
	frm			= (typeof frm		=== "undefined") ? document.getElementsByTagName('form')[0]	: document.getElementById(frm);
	var jfrm	= $(frm);
	if(typeof btn !== "undefined"){
		jbtn = $(btn);
		jbtn.attr('disabled','').attr('data-validate-text', 'Validating Form...').attr('data-process-text', 'Processing...').button('validate');
	}
	var validated = false;
	if(typeof Spry === "undefined"){
		validated = true;
	}else{
		validated = Spry.Widget.Form.validate(frm);
	}
	if(!validated){
		if(typeof btn !== "undefined") jbtn.button('reset').button('reset').removeAttr('disabled');
		$("#alert_working").addClass("hidden");
	}
	
	if(callback!=''){
		var fn = window[callback];
		if (typeof fn === "function"){
			var callback_resp = fn();
		}else{
			console.warn('Callback function wasn\'t found.');
			var callback_resp = false;
		}
	}else{
		var callback_resp = true;
	}
		
	if(!callback_resp){
		if(typeof btn !== "undefined") jbtn.button('reset').button('reset').removeAttr('disabled');
		$("#alert_working").addClass("hidden");
		return;
	}

	if(typeof btn !== "undefined") jbtn.button('reset').button('process');
	var formData = jfrm.serialize();
	$.post(jfrm.attr('action'),formData , function(data){
		if( (typeof sender === "undefined" || sender !== 'apply') && data.status == 1){
			window.location.href = data.url;
		}else{
			$(".alert").fadeOut(500).parent().remove();
			$($.parseHTML(data.msg)).css("display", "none").insertAfter("#status_bar").fadeIn();
			$(".alert").each(function(i,e){
				clearStatusMessage(e.id);
			});
			if(data.form != null){
				$.each(data.form, function(key, value) {
					$('[name="'+key+'"').val(value);
				});
			}
			
		}
	},'json')
	.always(function(){
		$("#alert_working").addClass("hidden");
		if(typeof btn !== "undefined") jbtn.button('reset').button('reset').removeAttr('disabled');
	});
	$('.validation-container').removeClass().addClass('validation-container');
}
function processModal(modal, frm, btn, sender, callback){
  processForm(frm, btn, sender, callback);
  $("#"+modal).modal("hide");
}
function clearStatusMessage(id){
	$.post('/action/core/clear_status_msg', {"msg_id": id});
}