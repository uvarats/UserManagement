$(function(){
    $("#switch").click(function(event){
        event.preventDefault();
        let all = $("input.select-item:checked:checked");
        switchAjax(Array.from(all).map(obj => obj.defaultValue).join());
        //
        // $('tr[id="row"]').each(function (index, item) {
        //     if($(item).find('td.active').children('input.select-item').is(":checked")){
        //         let id = $(item).find('.js-user-id')[0].innerText;
        //
        //     }
        // });
    });
    function switchAjax(ids){
        $.ajax({
            url: '/panel/switch/' + ids,
            method: 'POST',
            success: function (response) {
                //$(item).find('.js-user-status').text(response.new_status);
                let responseJsons = Array.from(response).map(JSON.parse);
                for(let resp of responseJsons){
                    $('input.select-item[value="' + resp.id + '"]').parent().parent().find('.js-user-status').text(resp.new_status);
                }

                location.reload();
            }

        });
    }
    function deleteAjax(ids){
        $.ajax({
            url: '/panel/delete/' + ids,
            method: 'POST',
        })
    }
    $("#delete").click(function(event){
        event.preventDefault();
        let all = $("input.select-item:checked:checked");

        deleteAjax(Array.from(all).map(obj => obj.defaultValue).join());

        $('tr[id="row"]').each(function (index, item) {
            if($(item).find('td.active').children('input.select-item').is(":checked")){
                $(item).remove();
            }
        });
        location.reload();
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