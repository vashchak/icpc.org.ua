function appQaView() {
    $(function(){
        window.editor = CKEDITOR.replace('answer-content', {
            extraPlugins: 'onchange',
            height: '200px'
        });
        $(".answer-create").on('click', function(){
            $.ajax({
                url: app.baseUrl + "/qa/saveAnswer/" + app.question.id,
                data: {
                    content: window.editor.getData()
                },
                success: function(data) {
                    if (data.status === 'success') {
                        window.location = app.baseUrl + "/qa/view/" + app.question.id;
                    } else {
                        appShowErrors(data.errors, $('.form-horizontal'));
                    }
                },
                error: function(xhr) {
                    if (parseInt(xhr.status) === 403) {
                        window.location = "/auth/login";
                    } else {
                        console.log('Unexpected server error: ', xhr.statusText);
                    }
                }
            });
        });
    });
}
