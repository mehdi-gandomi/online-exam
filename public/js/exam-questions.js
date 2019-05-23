$(".new-question").on("submit",function (e) {
    e.preventDefault();
    let $this=$(this);
    $.ajax({
        type:"POST",
        url:$this.attr("action"),
        data:$this.serialize(),
        success:function (data,status) {
            if (data.ok){
                alertify.success('سوال با موفقیت اضافه شد');
                $this.fadeOut();
                $this.remove();
                $("#addedQuestionsTitle").removeClass("d-none");
                $("#addedQuestions").append(`
                            <div class="col-md-3  question-card ">
                                <h5 class="m-0">سوال (${data.info.name})</h5>
                                <button class="btn btn-primary" onclick="showQuestionInfo('${data.info.id}')" > اطلاعات سوال</button>
                            </div>
                        `);
                let $elements=$(".new-question");
                if ($elements.length < 1){
                    $("#questions-wrap").addClass("compact");
                    Swal.fire({
                        title: 'موفق !',
                        text: "تمام سوالات با موفقیت افزوده شدند",
                        type: 'success',
                        showCancelButton:true,
                        cancelButtonText:"باشه",
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'بازگشت به صفحه اصلی'
                    }).then(function(result){
                        if (result.value) {
                            window.location.href=window.location.origin+"/professor";
                        }
                    })
                    $("#questions-wrap").html(`
                                <div class="d-flex justify-content-center align-items-center flex-d-column center">
                                    <h4>تمام سوالات با موفقیت اضافه شدند</h4>
                                    <button onclick="window.location.href=window.location.origin+'/professor'" class="btn btn-primary">بازگشت به صفحه اصلی</button>
                                </div>
                            `);
                    $("#page-title").addClass("d-none");
                }
            }
        }
    })
})
function showQuestionInfo(qid) {
    $.get("/professor/question/info/json",{qid:qid},function (data,status) {
        if (data.ok){
            renderQuestionInfo(data.info);
        }
    })
}
function renderQuestionInfo(info) {
    let output=`
        <div class="order-details row">
             <div class="order-details__detail col-md-3">
                   <div class="order-details__detail__label">
                        شماره سوال
                    </div>
                    <div class="order-details__detail__value">
                        ${ info.id }
                    </div>
              </div>
              <div class="order-details__detail col-md-3">
                   <div class="order-details__detail__label">
                        عنوان سوال
                    </div>
                    <div class="order-details__detail__value">
                        ${ info.title }
                    </div>
              </div>
              <div class="order-details__detail col-md-3">
                   <div class="order-details__detail__label">
                        گزینه اول
                    </div>
                    <div class="order-details__detail__value">
                        ${ info.option1 }
                    </div>
              </div>
              <div class="order-details__detail col-md-3">
                   <div class="order-details__detail__label">
                        گزینه دوم
                    </div>
                    <div class="order-details__detail__value">
                        ${ info.option2 }
                    </div>
              </div>
              <div class="order-details__detail col-md-3">
                   <div class="order-details__detail__label">
                        گزینه سوم
                    </div>
                    <div class="order-details__detail__value">
                        ${ info.option3 }
                    </div>
              </div>
              <div class="order-details__detail col-md-3">
                   <div class="order-details__detail__label">
                        گزینه چهارم
                    </div>
                    <div class="order-details__detail__value">
                        ${ info.option4 }
                    </div>
              </div>
              <div class="order-details__detail col-md-3">
                   <div class="order-details__detail__label">
                        جواب سوال
                    </div>
                    <div class="order-details__detail__value">
                        گزینه &nbsp; ${ info.answer }
                    </div>
              </div>
              <div class="order-details__detail col-md-3">
                   <div class="order-details__detail__label">
                        نمره سوال
                    </div>
                    <div class="order-details__detail__value">
                        ${ info.mark }
                    </div>
              </div>
        </div>
    `;

    $("#questionInfo").html(output);
    $("#questionInfoModal").modal("show");
}