function processStep(step){
	$.getJSON("/action/modules/"+process+"_"+step,{"id":module_id},function(data){
		if(Object.keys(data.data).length!=0){
			if(typeof data.data.step !== "undefined"){
				updateProgress(data.data.step/9*100);
			}
			if(typeof data.data.msg !== "undefined"){
				status_txt.text(data.data.msg);
			}else{
				status_txt.text('');
				$($.parseHTML(data.msg)).css("display", "none").insertBefore("#header").fadeIn();
				prog_bar.addClass("progress-bar-danger");
			}
			if(data.status==0){
				if(typeof data.data.status !== "undefined" && data.data.status==1){
					clearStatusMessage($($.parseHTML(data.msg)).find("div")[0].id);
					processStep(data.data.step);
				}else{
					error(data);
				}
			}else{
				clearStatusMessage($($.parseHTML(data.msg)).find("div")[0].id);
				prog_bar.addClass("progress-bar-success").parent().removeClass("progress-striped active");
				$($.parseHTML(data.msg)).css("display", "none").insertBefore("#header").fadeIn();
				status_txt.addClass("text-success").removeClass("text-muted");
				close_txt.html("You may now close this page, the "+process+" has completed.");
			}
		}else{
			error(data);
		}
	});
}
function error(data){
	$($.parseHTML(data.msg)).css("display", "none").insertBefore("#header").fadeIn();
	prog_bar.addClass("progress-bar-danger").parent().removeClass("progress-striped active");
	status_txt.addClass("text-danger").removeClass("text-muted");
	status_txt.append(" Please retry module "+process+". <br />If this module keeps failing to "+process+", please contact an Administrator, or the module author.");
	close_txt.html("You may now close this page, your system has not been modified.");
}
function updateProgress(percentage){
    if(percentage > 100) percentage = 100;
    prog_bar.css('width', percentage+'%');
	prog_bar.children('span').html(percentage+'%');
}