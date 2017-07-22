function cleared(value,begin){
	if (value.value==begin){
		value.value='';
	}
}

function hide(value){
	$('#'+value).fadeOut(200);
}

function refreshed(value,begin){
	if (value.value==''){
		value.value=begin;
	}
}

function zakazat(){
	$("#bg").remove();
	$("body").append('<div id="loadingBg" onClick="hide(\'loadingBg\')"></div>');
	$("body").append("<div id='formBlock'></div>");
	$("#formBlock").append("<div class='header'></div>");
	$("#formBlock").append("<div class='main'></div>");
	$(".main").append('<form action="" name="zakaz">\
	<input type="text" value="Фамилия Имя" name="fio" onBlur="refreshed(zakaz.fio,\'Фамилия Имя\')" onFocus="cleared(zakaz.fio,\'Фамилия Имя\')"/>\
	<input type="text" value="Телефон" name="phone" onBlur="refreshed(zakaz.phone,\'Телефон\')" onFocus="cleared(zakaz.phone,\'Телефон\')"/><br/>\
	<input type="text" value="E-mail" name="email" onBlur="refreshed(zakaz.email,\'E-mail\')" onFocus="cleared(zakaz.email,\'E-mail\')"/><br/>\
	<input type="text" value="Вид услуги" name="vidusl" onBlur="refreshed(zakaz.vidusl,\'Вид услуги\')" onFocus="cleared(zakaz.vidusl,\'Вид услуги\')"/><br/>\
	<textarea name="mess" onBlur="refreshed(zakaz.mess,\'Сообщение\')" onFocus="cleared(zakaz.mess,\'Сообщение\')">Сообщение</textarea>\
	</form>');
	var h=$("#formBlock").height();
	var w=$("#formBlock").width();
	$("#formBlock").css({
		'top':screen.height/2-h/2,
		'left':screen.width/2-w/2
	});
	$("#formBlock").fadeIn(100);
}