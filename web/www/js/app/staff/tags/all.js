function appStaffTagsAll(Messages) {
    var msg = Messages;
    $('.tag').on('click', function(){
        var $el = $(this);
        if (confirm(msg['conf'] + $el.data('name') + '" ?')) {
            $.ajax({
                type: "POST",
                url: "/staff/tags/delete/" + $el.data('id'),
                success: function(data) {
                    if (data.result) {
                        $el.remove();
                    }
                },
                error: function(xhr) {
                    if (parseInt(xhr.status) === 403) {
                        alert(msg['deny']);
                    } else {
                        console.log('Unexpected server error: ', xhr.statusText);
                    }
                }
            });
        }
        return false;
    });
}
