$( document ).ready(function() {

//fancybox
$(".fan_img").fancybox();

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

// Admin Ajax actions
    function runajax(id){
        $.ajax({
            type: "POST",
            data: parseInt(id.attr('href')),
            url: id.attr('href'),
            beforeSend: function(){
                $('#status_post').fadeOut( 400);
            },
            error: function(e){
                console.log('ajax error : ' + e);
            },
            success: function(e)	{
                id.parent().parent().remove();
                $('#status_post').fadeIn(400).text('Статья удалена').css('color', 'darkred');
                setTimeout(function(){ $('#status_post').fadeIn(800).text('Панель администратора').css('color', 'green'); }, 2500);
            }
        })
    };

    function runEditAjax(id){
        $.ajax({
            type: "POST",
            data: parseInt(id.attr('href')),
            url: id.attr('href'),
            error: function(e){
                console.log('editAjax error : ' + e);
            },
            success: function(e)	{
                $('#modal_ajax_admin').fadeIn(600).append(e);
            }
        })
    };

    function runSubmitAjax(id){
        $.ajax({
            type: "POST",
            data: $("#art_form").serialize(),
            url: "admin/save",
            dataType:"json",
            error: function(e){
                console.log('saveeditAjax error : ' + e.message);
            },
            success: function(e){
                $('#modal_ajax_admin').fadeOut(600);
                $('#status_post').fadeIn(400).text('Статья отредактирована').css('color', 'darkblue');
                setTimeout(function(){ $('#status_post').fadeIn(800).text('Панель администратора').css('color', 'green'); }, 2500);
                $('.num_' + e.art_id).find('.title').text(e.art_title).parent()
                    .find('.descr').text(e.art_description);

            }
        })
    };

    $(".btn_remove").on('click', function(e){
        e.preventDefault();
        runajax($(this));
    });
    $(".btn_edit").on('click', function(e){
        e.preventDefault();
        runEditAjax($(this));
    });
    $('body').on('submit', '.ajaxForm', function(e){
        e.preventDefault();
        runSubmitAjax($(this));
    });
// END Admin Ajax actions

//Авто-удаление букв из поля телефона
$('input[name=phone]').on('keyup', function(e){ 
  $(this).val($(this).val().replace( /[^\d+$]/g ,'')); 
});










});
