$("#newStudyForm").on("submit",function (e) {
    e.preventDefault();
    $.ajax({
        type:"POST",
        url:"/professor/study-field/new",
        data:$(this).serialize(),
        success:function (data,status) {
            if (data.ok){
                Swal.fire({
                    title: 'موفق !',
                    text: "گروه با موفقیت اضافه شد !",
                    type: 'success',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'باشه'
                }).then(function(result){
                    if (result.value) {
                        window.location.reload();
                    }
                })
            }
        }
    })
})
function deleteField(fieldId) {
    Swal.fire({
        title: 'آیا مطمینید ؟',
        text: "آیا واقعا می خواهید این گروه را حذف کنید ؟",
        type: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'بله',
        cancelButtonText:'نه'
    }).then(function(result) {
        if (result.value) {
            $.ajax({
                type:"DELETE",
                url:"/professor/study-field/"+fieldId,
                success:function (data,status) {
                    if (data.ok){
                        Swal.fire({
                            title: 'موفق !',
                            text: "گروه با موفقیت حذف شد !",
                            type: 'success',
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'باشه'
                        }).then(function(result){
                            if (result.value) {
                                window.location.reload();
                            }
                        })
                    }
                }
            })
        }
    })

}