function notify(msg, type){
    $.toast({
        heading: type[0].toUpperCase() + type.slice(1)+' Message',
        text: msg,
        showHideTransition: 'slide',
        position : 'top-right',
        icon: type
    })
}

$("form").submit(function(e){
    if($(this).attr('id') != undefined && $(this).attr('id') != ""){
        return false;
    }
});

function showErrors(errors, form=null){
    // notify('Oops!! Something went wrong', 'error');

    // if(typeof errors.responseJSON.status !== 'undefined'){
    //     notify(errors.responseJSON.status, 'error');
    // }
    // else if(typeof errors.responseJSON.message !== 'undefined'){
    //     notify(errors.responseJSON.message, 'error');
    // }
    // else{
    //     notify('OK', 'error');
    // }


    if(errors.status == 400){
        notify(errors.responseJSON.status, 'error');
    } else if(errors.status == 500 || errors.status == 419){
        notify(errors.responseJSON.message, 'error');
    }else{
        notify(errors.statusText , 'error');
    }
}

$('.eye-password').on('click', function(){
    var closestinput = $(this).closest('.input-group-btn').closest('.input-group').find('input');
    var type = (closestinput).attr('type');

    if(type == 'password'){
        $(closestinput).attr('type', 'text');

        $(this).html('<i class="fa fa-eye-slash"></i>');
    } else{
        $(closestinput).attr('type', 'password');

        $(this).html('<i class="fa fa-eye"></i>');
    }
});

$('.select2').select2();
