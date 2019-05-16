function deleteitem(id) {
    var data =
        {
            id: id,
        }

    $.ajax({
        type: "POST",
        dataType: "json",
        url: 'Task/delete',
        data: data,
        success: function (dataBack) {
            location.reload();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            location.reload();
        }
    });
}

function completeItem(id) {
    var data =
        {
            id: id,
        }

    $.ajax({
        type: "POST",
        dataType: "json",
        url: 'Task/complete',
        data: data,
        success: function (dataBack) {
            location.reload();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            location.reload();
        }
    });
}