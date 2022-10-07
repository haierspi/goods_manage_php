$('#addtag').bind('mouseleave', function() {
    ajaxtags_likelist($("#addtag").val());
});

function getkeyword(){
    var title = $('#resourcetitle').val();
    var content = $('#resourcecontent').val();
    if(content != ''){
        $.post('whaleyvr/tag?operation=keywords', {title:title, content:content}, function(data){
            if(data.kw){
                var kw = data.kw.join(',');
                if(kw != ''){
                    tags_createlist(kw);
                }
            }
        }, 'json');
    }
}

function ajaxtags_likelist(title){
    var $=jQuery;
    if (title) {
        $.getJSON('whaleyvr/tag?operation=managetaglike&tagtitle='+encodeURI(title)+'&inajax=1', function(response){
            if (response.scode == '1') {
                $("#addtag_body").html(response.content);
                if (response.tagsnum == 0) {
                    $("#createtagbtn").css('display','inline-block');
                }else{
                    $("#createtagbtn").css('display','none');
                }
            }
        }); 
    }else{
        $("#createtagbtn").css('display','none');
    }
}

function tags_removemap(id){
    $.getJSON('whaleyvr/tag?operation=managetagmap&tagid='+id+'&mapids='+$('#mapids').val()+'&inajax=1', function(response){
        if (response.scode == '1') {
            $('.taglistbody').html(response.content);
            $('#mapids').val(response.mapids);
        }
    });
}

function ajaxtags_create(title){
    var $=jQuery;

    var title = $('#addtag').val();
    $.getJSON('whaleyvr/tag?operation=managetagcreate&tagtitle='+encodeURI(title)+'&inajax=1', function(response){
        if (response.scode == '1') {
            ajaxtags_likelist(title);
        }
    });
}

function tags_addmap(id, $this){
    $.getJSON('whaleyvr/tag?operation=managetagmap&tagid='+id+'&mapids='+$('#mapids').val()+'&add=1&inajax=1', function(response){
        if (response.scode == '1') {
            $('.taglistbody').html(response.content);
            $('#mapids').val(response.mapids);
            //$("#addtag_body").html('');
            $('#addtag').val('')
            $this.remove();
        }
    });
}

function tags_createlist(titles){
    $.getJSON('whaleyvr/tag?operation=managetagcreatelist&tagtitles='+encodeURI(titles)+'&inajax=1', function(response){
        if (response.scode == '1') {
            $("#addtag_body").html(response.content);
        }
    });
}