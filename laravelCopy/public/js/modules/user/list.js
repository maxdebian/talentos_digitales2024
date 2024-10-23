$(function () {
   /*  $("#listUserTD").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)'); */
    $('#listUserTD').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
      });

      $('[data-mask]').inputmask()
});
function deleteUser(userId){
/*     debugger */
    Swal.fire({
        title: "Are you sure?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#F70922",
        cancelButtonColor: "#797374",
        confirmButtonText: "Yes, delete it!"
      }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "GET",
                dataType: "json",
                url: '/user/destroy',
                data: { userId },
                success: function (data) {
                    if (data.status === 200) {
                        toastr.success('User delete successfully');
                        $(`#userId_${userId}`).remove();
                    } else {
                        toastr.error('User delete Error');
                    }
                }


            });

        }
      });
     /* toastr.error('Error the Document Attachemente wasn\'t removed'); */
}



/* function showUser(id){
    emptyModal();
    $.ajax({
        type: "GET",
        dataType: "json",
        url: '/user/show',
        data: {
            id
        },
        success: function (data) {
            debugger
            if (data.status === 200) {
                $('.userDataJSName').val(data.name)
                $('.userUsernameJS').val(data.username)
                $('#showUser').modal('toggle')
            }
        }
    });
}

function emptyModal(){
    $('.userDataJSName').val('')
    $('.userUsernameJS').val('')
}
 */
