$(function () {
    $('#steamname-form').submit(function() {
        $.bbq.pushState({steamname: $('[name="steamname"]').val()});
    });

    var hash_params = $.deparam.fragment();
    if(hash_params.steamname) {
        $('[name="steamname"]').val(hash_params.steamname).trigger('submit');
    }

    $('#friends-form').submit(function () {
        $.bbq.pushState({friends: $('[name="friends[]"]').val()});
    });
    $(document).on('complete.ic.hashfriends', function(e, elem, data, status, xhr, requestID) {
        if($('[name="friends[]"]').length === 0) return;
        $(document).off('complete.ic.hashfriends');
        if(hash_params.friends) {
            $('[name="friends[]"]').selectpicker('val', hash_params.friends).submit();
        }
    })
});
