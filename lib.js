$(function () {
    $(document).on('after.success.ic', function(evt, elem, data, status, xhr, requestID) {
        if(evt.target && evt.target.id === 'games-container') {
            $.bbq.pushState({steamname: $('[name="steamname"]').val(), friends: $('[name="friends[]"]').val() || []});
        }
    });

    var hash_params = $.deparam.fragment();
    if(hash_params.steamname && $('[name="steamname"]').val() != hash_params.steamname) {
        $('[name="steamname"]').val(hash_params.steamname).trigger('submit');
    }

    var current_friends = $('#friends-select').val();
    if(hash_params.friends && !(current_friends.length === hash_params.friends.length && current_friends.every(function(v,i) { return v === hash_params.friends[i]}))) {
        $('#friends-select').selectpicker('val', hash_params.friends).submit();
    }
});
