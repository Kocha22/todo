@extends('layouts.template')

@section('content')
<style>
.form-group {
    margin-bottom: 0.5rem;
}
</style>
<style>
.col-form-label {
    padding-top: calc(.120rem + 1px);
    padding-bottom: calc(.120rem + 1px);
    margin-bottom: 0;
    font-size: inherit;
    line-height: 0.1;
}
</style>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Высшее профессиональное образование</h1>
            </div>
            <div class="col-sm-6">

            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>


<section class="content">



    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Default box -->
                <div class="card">
                    <div class="card-header">


                        <div class="personal_inner text-sm text-red">
                            Будьте внимательны при заполнении полей. Личная информация заполняется на русском языке
                            (кириллицей). Данные следует указывать согласно Вашим личным документам.

                        </div>
                    </div>

                    <div class="card-body">
                        <form id="form_app24" class="" action="{{ route('storepost') }}"
                            method="post">
                            <input type="hidden" name="user_id" value="" />
                            <input type="hidden" id="post_id" value="" />
                            <div class="form-group row">

                                <div class="form-group row">
                                    <label for="title" class="col-sm-6 col-form-label">Название поста</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="title" type="text"
                                            name="title">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="description" class="col-sm-6 col-form-label">Описание</label>
                                    <div class="col-sm-6">
                                        <textarea class="form-control" id="description" name="description"></textarea>
                                    </div>
                                </div>


                                <div id="loaderIcon" class="spinner-border text-primary" style="display:none"
                                    role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <div class="button-submit">
                                    <input id="btn" class="button light-blue" type="submit" value="Добавить">
                                </div>

                            </div>

                        </form>

                        <div class="form-home26 card-body">
                            <table id="customers_next" class="table table-hover text-wrap text-sm">
                                <thead class="thead-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Название</th>
                                        <th>Описание</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody"></tbody>
                            </table>
                        </div>







                    </div>
                </div>









            </div>
        </div>
    </div>


</section>

<script>
$(document).ready(function() {
    function showdata() {
        $.ajax({
            url: "{{ route('getPosts') }}",
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $("#tbody").html(data.table_data);
            }
        });
    }
    showdata();

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



    $("#form_app24").submit(function(e) {
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
                $("#form_app24").find('span.error-text').text('');
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
                    $("#form_app24")[0].reset();
                    showdata();
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

    $("#tbody").on("click", ".delete-icon", function() {
        let post_id = $(this).attr('data-sid');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "/deleteproduction/" + post_id,
            method: "GET",
            dataType: 'json',
            success: function(response) {
                let success = response.msg;
                console.log(response.msg);
                $("#res").html(success);
                showdata();
            }

        });
    });

});
</script>
@endsection