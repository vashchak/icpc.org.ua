function appQaComments() {
    $(function(){
        var Dater = function() {
            var date = new Date();
            var attributes = {
                seconds: date.getSeconds(),
                minutes: date.getMinutes(),
                hours: date.getHours(),
                day: date.getDate(),
                month: date.getMonth() + 1,
                year: date.getUTCFullYear()
            };
            this.yyyymmdd_hhiiss = function(){
                return [
                    attributes.year,
                    attributes.month,
                    attributes.day
                ].join('-') + ' ' + [
                    attributes.hours,
                    attributes.minutes,
                    attributes.seconds
                ].join(':');
            }
        }
        var Helper = {
            comments: {
                toggle: function($el) {
                    var $mainPane = $el.closest('.comment-container');
                    $('.comment-subcontainer', $mainPane).toggle();
                    $('.comment-add-btn', $mainPane).toggle();
                }
            }
        }
        $('.comment-add-btn').on('click', function(){
            Helper.comments.toggle($(this));
        });
        $('.comment-submit-btn').on('click', function(){
            var $this = $(this);
            var $mainPane = $this.closest('.comment-container');
            var comment = _.template($('#tmpl_comment').html().trim());
            var entity_title = _.template($('#tmpl_entity_title').html().trim());
            $.ajax({
                url: app.baseUrl + "/qa/addComment",
                data: {
                    content: $('.comment-content', $mainPane).val(),
                    entity: $('.comment-entity-resolver', $mainPane).data('entity'),
                    id: $('.comment-entity-resolver', $mainPane).data('id')
                },
                success: function(data){
                    if (data.status === 'success') {
                        var d = new Dater();
                        var $row = $(comment({
                            content: $('.comment-content', $mainPane).val(),
                            author_fio: app.user.fio,
                            date_created: d.yyyymmdd_hhiiss()
                        }));
                        $('.comment-content', $mainPane).val('');
                        $row.insertBefore($mainPane.parent());
                        if ($row.prev().hasClass('intro')) {
                            $('.comment', $row.prev()).html(
                                entity_title({
                                    entity: $('.comment-entity-resolver', $mainPane).data('entity')
                                })
                            );
                        }
                        Helper.comments.toggle($this);
                    } else {
                        appShowErrors(data.errors, $('.form-horizontal', $mainPane));
                        if (data.errors.author) {
                            window.location = '/auth/login';
                        }
                        console.log('Error status: ', data);
                    }
                },
                error: function(xhr){
                    if (parseInt(xhr.status) == 403) {
                        alert('Access denied');
                    }
                }
            });
            return false;
        });
    });
}
