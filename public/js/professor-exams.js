function deleteExam(examId){
    Swal.fire({
        title: 'آیا مطمینید ؟',
        text: "آیا واقعا می خواهید این ازمون را حذف کنید ؟",
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
                url:"/professor/exam/"+examId,
                success:function (data,status) {
                    if (data.ok){
                        Swal.fire({
                            title: 'موفق !',
                            text: "آزمون با موفقیت حذف شد !",
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

function renderExamInfo(info) {
    let output=`
        <div class="order-details row">
              <div class="order-details__detail col-md-3">
                   <div class="order-details__detail__label">
                        شماره آزمون
                    </div>
                    <div class="order-details__detail__value">
                        ${ info.id }
                    </div>
              </div>
              <div class="order-details__detail col-md-5">
                   <div class="order-details__detail__label">
                        عنوان آزمون
                    </div>
                    <div class="order-details__detail__value">
                        ${ info.name }
                    </div>
              </div>
              <div class="order-details__detail col-md-4">
                   <div class="order-details__detail__label">
                        گروه امتحانی
                    </div>
                    <div class="order-details__detail__value">
                        ${ info.study_field }
                    </div>
              </div>
              <div class="order-details__detail col-md-2">
                   <div class="order-details__detail__label">
                        نمره منفی 
                    </div>
                    <div class="order-details__detail__value">
                        ${ info.has_negative_mark ==='0' ? "ندارد":"دارد" }
                    </div>
              </div>
              <div class="order-details__detail col-md-5">
                   <div class="order-details__detail__label">
                        تاریخ ثبت 
                    </div>
                    <div class="order-details__detail__value">
                        ${ info.create_date_persian }  
                    </div>
              </div>
              <div class="order-details__detail col-md-2">
                   <div class="order-details__detail__label">
                        نمره آزمون 
                    </div>
                    <div class="order-details__detail__value">
                        ${ info.exam_mark }  
                    </div>
              </div>
              <div class="order-details__detail col-md-3">
                   <div class="order-details__detail__label">
                        تعداد سوالات
                    </div>
                    <div class="order-details__detail__value">
                        ${ info.question_count }
                    </div>
              </div>
         
              <div class="order-details__detail col-md-3">
                   <div class="order-details__detail__label">
                        سوالات منفی دار 
                    </div>
                    <div class="order-details__detail__value">
                        ${ info.negative_mark_count }
                    </div>
              </div>
              <div class="order-details__detail col-md-3">
                   <div class="order-details__detail__label">
                        زمان آزمون 
                    </div>
                    <div class="order-details__detail__value">
                        ${ info.exam_time } دقیقه 
                    </div>
              </div>
              
              <div class="order-details__detail col-md-3">
                   <div class="order-details__detail__label">
                        وضعیت فعال بودن 
                    </div>
                    <div class="order-details__detail__value">
                        ${ info.is_disabled ==='1' ? "غیرفعال":"فعال"  }  
                    </div>
              </div>
              
       </div>
       <div class="d-flex justify-content-center mt-4">
            <a href="/professor/exam/questions/show/${info.id}" class="btn btn-success">مشاهده سوالات ازمون</a>
            <button type="button" class="btn btn-danger mr-2" data-dismiss="modal">بستن</button>     
       </div>
    `;

    output+="</div>";
    $("#examDetails").html(output);
    $("#examDetailsModal").modal("show");
}

function showExamInfo(examId) {
    $.get("/professor/exam/json/"+examId,function (data,status) {
        if (data.ok){
            renderExamInfo(data.info);
        }
    })
}