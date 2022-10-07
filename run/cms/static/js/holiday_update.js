function getStartDate() {

    // var hoid = $(".member_holiday_used_hoid").find('option:selected').val();
    // var is_contain_weekend = 0;

    // if($("#is_contain_weekend").attr("checked")) {
    //     is_contain_weekend =  $("#is_contain_weekend").val();
    // }

    // if(hoid == -2 || (hoid == -1 && is_contain_weekend == 1)) {
    //     WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:"#F{$dp.$D('enddate1');}",disabledDays:[1,2,3,4,5]});
    // }else{
    //     WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:"#F{$dp.$D('enddate1');}",disabledDays:[0,6]});
    // }
    

    WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:"#F{$dp.$D('enddate1');}", onpicked:chtime});
}

function selecttype(starttime,hoid) {
    // var starttime = $(".starttime").val();
    if(starttime == '') {
        return false;
    }
    // var hoid = $(".member_holiday_used_hoid").find('option:selected').val(); 
    $.post( 'my/holiday?operation=validdate',
            {starttime:starttime,hoid:hoid},
            function(json){
                if(json != -1) {
                    $(".validdays").css("display","inline-block");
                    $(".validdays").text("可申请" + json + "天");
                    $("input[name='validday']").val(json);
                }else{
                    $(".validdays").css("display","none");
                    $("input[name='validday']").val(-1);
                }
            }
    )
}

function caldate(){
    var totaldays = 0;
    var is_checked = $(':focus').is(':checked');

    totaldays = $("input[name = 'member_holiday_used[totaldays]']").val();
    totaldays = parseFloat(totaldays);
    if(is_checked) {
        totaldays = totaldays + 1;
    } else {
        totaldays = totaldays - 1;
    }

    $(".totaldays").val(totaldays);
}

function getEndDate() {
    WdatePicker({dateFmt:'yyyy-MM-dd',minDate:"#F{$dp.$D('startdate1');}", onpicked:chtime});
}

function chtime() {
    var starttime = $(".starttime").val();
    var endtime = $(".endtime").val();
    var start_timetype = $(".start_timetype").val();
    var end_timetype = $(".end_timetype").val();
    var hoid = $(".member_holiday_used_hoid").find('option:selected').val();
    var is_contain_weekend = 0;
    var html = '';
    var checknum = 0;
    var checkboxnum = 0;
    var unchecknum = 0;

    if($("#is_contain_weekend").attr("checked")) {
        is_contain_weekend =  $("#is_contain_weekend").val();
    }

    if($(':focus').attr('name') == 'member_holiday_used[hoid]' || $(':focus').attr('name') == 'member_holiday_used[starttime]') {
        selecttype(starttime,hoid);
    }

    if(hoid == -2 || hoid == 4) {
        start_timetype = 1;
        end_timetype = 1;
    }

    var url = 'my/holiday?operation=difftime';
    $.post( url,
            {starttime:starttime,endtime:endtime,start_timetype:start_timetype,end_timetype:end_timetype,hoid:hoid,is_contain_weekend:is_contain_weekend},
            function(json2){
                var data2 = eval("(" + json2 + ")");
                if($(':focus').attr('name') == 'member_holiday_used[is_contain_weekend]' || $(':focus').attr('name') == 'member_holiday_used[starttime]' || $(':focus').attr('name') == 'member_holiday_used[endtime]') {
                    if(endtime != '' && hoid == -1 && is_contain_weekend == 1) {
                    var arr = data2.weekend;
                    for(var i in arr){
                       html += "<label><input name='weekend[]' type='checkbox' value='"+arr[i]+"' checked='checked' onchange='return caldate()'/>"+arr[i]+"</label>";
                           if((i % 4) != 0) {
                                html += "&nbsp;&nbsp;";
                           }
                        }
                    }else{
                        html = '';
                    }
                
                    $('.is_contain_weekend_checkbox').html(html);
                }
                

                if(hoid == -2 || hoid == 4) {
                    $(".start_timetype_ul").hide();
                    $(".end_timetype_ul").hide();
                } else {
                    $(".start_timetype_ul").show();
                    $(".end_timetype_ul").show();
                }

                checknum = $("[name='weekend[]']:checked").length;
                checkboxnum = $("[name='weekend[]']").length;
                unchecknum = checkboxnum - checknum;
                if(data2.error == 0) {
                    $(".totaldays").val(data2.diffdate-unchecknum);
                }else{
                    $(".totaldays").val(0);
                }
            }
    )
}

function hidetimehtml(obj,flag) {
    $(obj).each(function() {
        if(flag == 1) {
            if($(this).val() == 1) {
                $(this).attr("selected","selected").siblings().hide();
            }
        }else {
            if($(this).is(':hidden')) {
                $(this).show();
            }
        }
    });
}