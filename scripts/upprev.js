function iua(box,iuh)
{return'-'+(box.width()+iuh+parseInt(box.css('padding-top').replace(/px$/,''))+parseInt(box.css('padding-bottom').replace(/px$/,'')))+'px';}
function iub(box,iuh)
{return'-'+(box.width()+iuh+parseInt(box.css('padding-left').replace(/px$/,''))+parseInt(box.css('padding-right').replace(/px$/,'')))+'px';}
function iun(h)
{return parseInt(h.css('margin-top').replace(/px$/,''))-h.offset().top;}
jQuery(function($){if('undefined'==typeof(iworks_upprev)){return;}
var iuc=false;var iud=true;var iue=true;var iug=typeof(_gaq)!='undefined';var iuf=iworks_upprev.ga_opt_noninteraction==1;var iuh=0;function iui(){var ium=false;if(iworks_upprev.offset_element&&$(iworks_upprev.offset_element)){if($(iworks_upprev.offset_element).length>0){ium=iun($('html'))+$(window).height()>$(iworks_upprev.offset_element).offset().top;}else{ium=iun($('html'))+$(window).height()>=$(document).height()*iworks_upprev.offset_percent/100;}}else{ium=(iun($('html'))+$(window).height()>=$(document).height()*iworks_upprev.offset_percent/100);}
box=$('#upprev_box');if(ium&&!iuc){if(iworks_upprev.animation=="fade"){box.fadeIn("slow");}else{iuj=iworks_upprev.css_side+'px';iuk=iworks_upprev.css_bottom+'px';console.log('p: '+iworks_upprev.position.all);console.log('v: '+iuk);console.log('h: '+iuj);switch(iworks_upprev.position.all){case'left':box.stop().animate({left:iuj,bottom:iuk});break;case'left-top':box.stop().animate({left:iuj,top:iuk});break;case'right':box.stop().animate({right:iuj,bottom:iuk});break;case'right-middle':box.css('top',(($(window).height()+box.height())/2)+'px');box.stop().animate({right:iuj});break;case'right-top':box.stop().animate({right:iuj,top:iuk});break;default:alert(iworks_upprev.position);break;}}
iud=false;if(iug&&iue&&iworks_upprev.ga_track_views==1){_gaq.push(['_trackEvent','upPrev',iworks_upprev.title,null,0,iuf]);iue=false;}}
else if(iuc&&iun($('html'))==0){iuc=false;}
else if(!iud){iud=true;if(iworks_upprev.animation=='fade'){box.fadeOut('slow');}else{iuj=iua(box,iuh);iuk=iub(box,iuh);switch(iworks_upprev.position.all){case'left':box.stop().animate({left:iuk,bottom:iuj});break;case'left-top':box.stop().animate({left:iuk,top:iuj});break;case'right-top':box.stop().animate({right:iuk,top:iuj});break;case'right-middle':box.stop().animate({right:iuj});break;case'right':box.stop().animate({right:iuk,bottom:iuj});break;default:alert(iworks_upprev.position);break;}}}}
$(document).ready(function(){$.get(iworks_upprev.url,function(data){$('body').append(data);$(window).bind('scroll',function(){iui();});$("#upprev_close").click(function(){$('#upprev_box').fadeOut("slow");$(window).unbind('scroll');return false;});if(iworks_upprev.url_new_window==1||iworks_upprev.ga_track_clicks==1){$('#upprev_box a[id!=upprev_close]').click(function(){$(this).attr('style','bacground-color:lime');if(iworks_upprev.url_new_window==1){window.open($(this).attr('href'));}
if(iug&&iworks_upprev.ga_track_clicks==1){_gaq.push(['_trackEvent','upPrev',iworks_upprev.title,$(this).html(),1,iuf]);}
if(iworks_upprev.url_new_window==1){return false;}});}
box=$('#upprev_box');box.css({width:iworks_upprev.css_width});if(iworks_upprev.animation=='flyout'){iuj=iua(box,iuh);iuk=iub(box,iuh);switch(iworks_upprev.position.all){case'left':box.css({left:iuk,bottom:iuj});break;case'left-top':box.css({left:iuk,top:iuj});break;case'right-top':box.css({right:iuk,top:iuj});break;case'right-middle':box.css({right:iuj});break;case'right':box.css({right:iuk,bottom:iuj});break;default:alert(iworks_upprev.position);break;}}
iui();});});});