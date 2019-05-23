function renderExamInfo(info) {
    let output=`
        <div class="order-details row">
              <div class="order-details__detail col-md-3">
                   <div class="order-details__detail__label">
                        شماره آزمون
                    </div>
                    <div class="order-details__detail__value">
                        ${ info.exam_id }
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
              
              <div class="col-12">
                <h4 class="text-center mt-4 mb-4">نتایج آزمون دانشجو (${info.fname + " "+info.lname})</h4>
              </div>
              <div class="order-details__detail col-md-3">
                   <div class="order-details__detail__label">
                        نمره دانشجو 
                    </div>
                    <div class="order-details__detail__value">
                        ${ info.final_mark }  
                    </div>
              </div>
              <div class="order-details__detail col-md-3">
                   <div class="order-details__detail__label">
                        تعداد سوالات صحیح 
                    </div>
                    <div class="order-details__detail__value">
                        ${ info.correct_answers }  
                    </div>
              </div>
              <div class="order-details__detail col-md-3">
                   <div class="order-details__detail__label">
                        تعداد سوالات غلط 
                    </div>
                    <div class="order-details__detail__value">
                        ${ info.incorrect_answers }  
                    </div>
              </div>
              <div class="order-details__detail col-md-3">
                   <div class="order-details__detail__label">
                        تعداد نمره منفی 
                    </div>
                    <div class="order-details__detail__value">
                        ${ info.negative_mark }  
                    </div>
              </div>
       </div>
       <div class="d-flex justify-content-center mt-4">
            <a href="/user/exam-result/${info.exam_id}" class="btn btn-success">مشاهده جواب ها </a>
            <button type="button" class="btn btn-danger mr-2" data-dismiss="modal">بستن</button>
       </div>
    `;

    $("#examDetails").html(output);
    $("#examDetailsModal").modal("show");
}
function showExamInfo(examId) {
    $.get("/professor/exam/results/json/"+examId,function (data,status) {
        if (data.ok){
            console.log(data);
            renderExamInfo(data.info);
        }
    })
}