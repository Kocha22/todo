jQuery(document).ready(function() {   
    // Удаление прикрепленного файла в модальном окне
    $("#practice_modal").on("click", ".delete-icon2", function() {
        event.preventDefault()
        let post_id = $(this).attr('data-sid');
        $.ajax({
            url: "/deletefile/" + post_id,
            method: "GET",
            dataType: 'json',
            success: function(data) {
                $('#filescanlist').fadeOut();
                $('#filescan').fadeIn();
                fileshow(post_id);
                showdata();
            }
        });
    });

    function kindshow(id) {
        $.ajax({
            url: "/kindeducationupdate/" + id,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $("#result35").html(data.table_data);
                $('#result35').val($('#result35 option:selected').val());

                $("#result36").html(data.outputspecial);
                $('#result36').val($('#result36 option:selected').val());
            }
        });
    }

    let post_id = $('#post_id').val();

    $('body').on('click', '#editCompany', function(event) {
        event.preventDefault();
        var id = $(this).data('id');
        kindshow(id);
        $.get('/edu/' + id + '/edit', function(data) {
            fileshow(id);
            $('#color_id2').val(data.data.id);
            $('#nameoforganization2').val(data.data.nameoforganization);
            $('#faculty2').val(data.data.faculty);
            $('#dateofentry2').val(data.data.dateofentry);
            $('#termination2').val(data.data.termination);
        })
    });

    $('body').on('click', '#submit', function(event) {
        event.preventDefault();
        var id = $("#color_id2").val();
        var formData = new FormData(document.getElementById("edu_form"));
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '/storepost/' + id,
            data: formData,
            cache: false,
            dataType: 'json',
            contentType: false,
            processData: false,
            type: "POST",
            success: function() {
                $("#practice_modal").modal("hide");
                $('.practice_modal').addClass('toggleable').hide(1000);   
                $(document.body).removeClass('modal-open');
                $('.modal-backdrop').remove();   
                showdata();
            }
        });
    });

    $(".kind").on('change', function(e) {
        e.preventDefault();
        let id = $(this).val();
        $.ajax({
            url: "/kindeducation/" + id,
            data: {
                id: id,
            },
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                 if (data.kind_id == 2 || data.kind_id == 3) {
                    $('#level').fadeIn();
                    $('#ordin').fadeOut();
                    $('#result').fadeIn();
                    $('#result').select2();
                    $('#kindeeducation2').html(data.label);
                    $('#result').html(data.table_data);
                } else if (data.kind_id == 1) {
                    $('#kindeeducation2').html(data.label);
                    $('#result').html(data.table_data);
                    $('#ordin').fadeIn();
                    $('#result34').html(data.table_data);
                    $('#level').fadeOut();
                } else if (data.table_data == '' || data.table_data == 0 || data.label == 0) {
                    $('#kindeeducation2').fadeOut();
                    $('#ordin').fadeOut();
                    $('#result').fadeOut();
                    $('#result').select2('destroy');
                    $('#level').fadeOut();
                } else {
                    $('#kindeeducation2').fadeIn();
                    $('#ordin').fadeOut();
                    $('#result').fadeIn();
                    $('#result34').fadeIn();
                    $('#result').select2();
                    $('#kindeeducation2').html(data.label);
                    $('#result').html(data.table_data);
                    $('#result34').html(data.table_data);
                    $('#level').fadeOut();
                }
            }
        });

    });

    $(".kindModal").on('change', function(e) {
        e.preventDefault();
        let id = $(this).val();
        $.ajax({
            url: "/kindeducation/" + id,
            data: {
                id: id,
            },
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.table_data == '' || data.table_data == 0 || data.label == 0) {
                    $('#kindeeducation2').fadeOut();
                    $('#result').fadeOut();
                    $('#result').select2('destroy');
                } else if (data.kind_id == 1) {
                    $('#kindeeducation2').html(data.label);
                    $('#result').html(data.table_data);
                    $('#result36').html(data.table_data);
                } else {
                    $('#kindeeducation2').fadeIn();
                    $('#result').fadeIn();
                    $('#result34').fadeIn();
                    $('#result').select2();
                    $('#kindeeducation2').html(data.label);
                    $('#result').html(data.table_data);
                    $('#result36').html(data.table_data);
                }
            }
        });

    });



    $("#form_app26").submit(function(e) {
        e.preventDefault();
        let url = $(this).attr('action');
        var formData = new FormData(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.post({
            url: url,
            data: formData,
            cache: false,
            dataType: 'json',
            contentType: false,
            processData: false,
            beforeSend: function() {
                $("#form_app26").find('span.error-text').text('');
            },
            success: function(response) {
                if (response.code != 200) {
                    console.log(response);
                    let errors = response.msg;
                    $.each(errors, function(prefix, val) {
                        $('span.' + prefix + '_error').text(val[0]);
                    });
                } else if (response.code == 200) {
                    let success = response.msg;
                    $("#res").text(success);
                    $("#form_app26")[0].reset();
                    showdata();
                }
            }
        });

    });
});