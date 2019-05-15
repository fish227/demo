$(document).ready(function() {
    $.ajax({
        type: 'POST',
        url: "../controller/IndexController.php",
        dataType: "json",
        data:{
            mod_tab:'show_list'
        },
        success: function(json){
            // console.log(json)
            Data = json;
            var text = '';
            for(i = 0; i <= json.length - 1; i++){
                text += '<tr>';
                text +=     '<td data-th="編號"><span class="bt-content">'+ json[i].id +'</span></td>';
                text +=     '<td data-th="姓名"><span class="bt-content">'+ json[i].name +'</span></td>';
                text +=     '<td data-th="Eamil"><span class="bt-content">'+ json[i].email +'</span></td>';
                text +=     '<td data-th="電話"><span class="bt-content">'+ json[i].phone +'</span></td>';
                text +=     '<td data-th="修改時間"><span class="bt-content">'+ json[i].updated_time +'</span></td>';
                text +=     '<td><button data-toggle="modal" data-target="#user" class="btn btn-xs btn-primary" onclick="edit(' + i + ')">修改</button></td>';
                text +=     '<td><button class="btn btn-xs btn-danger" onclick="delete(' + i + ')">刪除</button></td>';
                text += '</tr>';
            }
            $(".users_data").html(text);
        }
    });
});

function edit(index) {
    user_data = Data[index];
    console.log(user_data) //F12看資料
    $("input[name='id']").val(user_data.id)
    $("input[name='name']").val(user_data.name)
    $("input[name='email']").val(user_data.email)
    $("input[name='phone']").val(user_data.phone)
}