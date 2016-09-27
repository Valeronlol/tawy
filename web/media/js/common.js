$( document ).ready(function() {

//Переключатель для футер кнопок контактов
	$('.spoiler-body').css('display', 'none');
    $('.spoiler-head button').click(function(){
        $(this).parent().next().slideToggle(500);
        $("html, body").animate({ scrollTop: $(document).height() }, 400);
    });

//Переключатель для хедер кнопок контактов
function toggler(){ 
	$('.spoiler-body').slideDown(500);
	$("html, body").animate({ scrollTop: $(document).height() }, 400);
	$('#cont_but i.fa-phone').addClass('active');
	$('#cont_but span.disp_none').addClass('active');
};

  $('#cont_but').on('click', function(){//доп условие по взаимодействию с нижники кнопками
  	if($('.spoiler-body').css("display") == "block"){
  		$('.spoiler-body').slideUp(500);
			$('#cont_but i.fa-phone').removeClass('active');
			$('#cont_but span.disp_none').removeClass('active');
  	}
  	else
  		toggler();
	return false;
  });

//fancybox
   $(".fan_img").fancybox();

//Form 
//   $("#send_form").submit(function() {
//     $.ajax({
//       type: "POST",
//       url: "mail.php",
//       data: $(this).serialize()
//     }).done(function() {
//       $(this).find("input").val("");
//       alert("Спасибо за заявку! Я свяжусь с вами позже.");
//       $("#send_form").trigger("reset");
//       $('.spoiler-body').hide('400');
//     });
//     return false;
//   });

//Авто-удаление букв из поля телефона
$('input[name=phone]').on('keyup', function(e){ 
  $(this).val($(this).val().replace( /[^\d+$]/g ,'')); 
});










});
