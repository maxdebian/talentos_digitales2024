$(document).ready(function(){
    /* debugger */
    $('[data-mask]').inputmask()
   /*  $('.name')
    $('#name') */
});

function validateForm() {
    let flag = true;

    if ($('#name').val() == "")
        flag = chekedInputOrFields("name");

   /*  if ($('#first_name').val() == "")
        flag = chekedInputOrFields("first_name"); */

    if ($('#last_name').val() == "")
        flag = chekedInputOrFields("last_name");


    const email =$('#email').val();

    if (email == ""){
        flag = chekedInputOrFields("email");
    }else{
        flag = validateEmail(email);
    }

    if ($('#password').val() == "")
        flag = chekedInputOrFields("password");

    if ($('#role').val() == null)
        flag = chekedInputOrFields("role");

    if ($('#dni').val() == "")
        flag = chekedInputOrFields("dni");

    let archivoInput = document.getElementById('avatar');
    /* let files = document.getElementById('avatar').files; */
    let zero= document.getElementsByName('avatar');
    let archivoRuta = archivoInput.value;
    let extPermitidas = /(.png|.gif|.jpg|.jpeg)$/i;
    if(!extPermitidas.exec(archivoRuta)){
        archivoInput.value = '';
        flag = chekedInputOrFields("avatar");
    }
    if(zero.length=="")
        flag = chekedInputOrFields("avatar");

    if ($('#address').val() == "")
        flag = chekedInputOrFields("address");

    if ($('#mobile').val() == "")
        flag = chekedInputOrFields("mobile");

    if ($('#date_of_birth').val() == "")
        flag = chekedInputOrFields("date_of_birth");



    return flag;
}











function chekedInputOrFields(classOrIdJquery) {
    $(`#${classOrIdJquery}`).attr('required', true);
    $(`.${classOrIdJquery}`).css('color', 'red');
    return false;
}

















function validateEmail(email) {
    var mailformat = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,6}$/;
    if (email.match(mailformat)) {
        return true;
    }else{
        return chekedInputOrFields("email");
    }
}

function searchUsername(email) {
    $.ajax({
        type: "POST",
        dataType: "json",
        url: '/user/search',
        data: {
            'email': email,
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            if (data.status === 200) {
                alert('Valid User');
            } else {
                alert('Username already exists');
                $('#email').val('');
            }
        }
    });
}


function changeValidate(idOrClass){
    $(`.${idOrClass}`).css('color','black');
}
