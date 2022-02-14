$(function(){
    $("#switch").click(function(event){
        event.preventDefault();
        $('tr[id="row"]').each(function (index, item) {
            if($(item).find('td.active').children('input.select-item').is(":checked")){
                let id = $(item).find('.js-user-id')[0].innerText;
                $.ajax({
                    url: '/panel/switch/' + id,
                    method: 'POST',
                    success: function (response) {
                        $(item).find('.js-user-status').text(response.new_status);
                        location.reload();
                    }

                });
            }
        });
    });
    $("#delete").click(function(event){
        event.preventDefault();
        let selfDeleteFlag = false;
        $('tr[id="row"]').each(function (index, item) {
            if($(item).find('td.active').children('input.select-item').is(":checked")){
                let id = $(item).find('.js-user-id')[0].innerText;
                $.ajax({
                    url: '/panel/delete/' + id,
                    method: 'POST',
                    success: function (response) {
                        $(item).remove();
                        location.reload();
                        selfDeleteFlag = response.is_current
                        console.log('is_current - ' + selfDeleteFlag);
                    }
                });
            }
        });
        if(selfDeleteFlag === true){
            $.ajax({
                url: '/panel/self-delete',
                method: 'POST',
            });
        }
        }
    );
    //button select all or cancel
    $("#select-all").click(function () {
        let all = $("input.select-all")[0];
        all.checked = !all.checked
        let checked = all.checked;
        $("input.select-item").each(function (index,item) {
            item.checked = checked;
        });
    });

    //button select invert
    $("#select-invert").click(function () {
        $("input.select-item").each(function (index,item) {
            item.checked = !item.checked;
        });
        checkSelected();
    });

    //button get selected info
    $("#selected").click(function () {
        var items=[];
        $("input.select-item:checked:checked").each(function (index,item) {
            items[index] = item.value;
        });
        if (items.length < 1) {
            alert("no selected items!!!");
        }else {
            var values = items.join(',');
            console.log(values);
            var html = $("<div></div>");
            html.html("selected:"+values);
            html.appendTo("body");
        }
    });

    //column checkbox select all or cancel
    $("input.select-all").click(function () {
        var checked = this.checked;
        $("input.select-item").each(function (index,item) {
            item.checked = checked;
        });
    });

    //check selected items
    $("input.select-item").click(function () {
        var checked = this.checked;
        console.log(checked);
        checkSelected();
    });

    //check is all selected
    function checkSelected() {
        var all = $("input.select-all")[0];
        var total = $("input.select-item").length;
        var len = $("input.select-item:checked:checked").length;
        console.log("total:"+total);
        console.log("len:"+len);
        all.checked = len===total;
    }
});