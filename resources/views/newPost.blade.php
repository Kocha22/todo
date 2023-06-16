@extends('layouts.template')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}  {{ $user->name ?? "" }} {{ $user->email ?? "" }}

                    <section class="content-header">
                        <div class="container-fluid">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <h1>Создание поста</h1>
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
                                        <div class="card-body">
                                            <form id="form_app24" class="" action="{{ route('storepost') }}"
                                                method="post">
                                                <input type="hidden" id="user_id" name="user_id" value="{{ $user->id }}" />
                                                <input type="hidden" id="post_id" value="" />
                                                <div class="form-group row">

                                                    <div class="row mb-3">
                                                        <label for="title" class="col-sm-3 col-form-label">Название поста</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control" id="title" type="text" name="title">
                                                            <span class="text-danger error-text title_error"></span>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <label for="description" class="col-sm-3 col-form-label">Описание</label>
                                                        <div class="col-sm-9">
                                                            <textarea class="form-control" id="description" name="description"></textarea>
                                                            <span class="text-danger error-text description_error"></span>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <label class="col-sm-3 col-form-label">Картинка</label>
                                                        <div class="col-sm-9">
                                                        <input type="file" class="form-control" name="image" id="selectImage">
                                                        <span class="text-danger error-text image_error"></span>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <label for="description" class="col-sm-3 col-form-label">Теги</label>
                                                        <div class="col-sm-9">
                                                        <input type="text" class="form-control" id="tags" name="tags" data-role="tagsinput" value="{{ old('tags') }}">
                                                        <span class="text-danger error-text tags_error"></span>
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

                                        </div>
                                    </div>









                                </div>
                            </div>
                        </div>


                    </section>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('#tags').tagsinput({
        confirmKeys: [13, 44] // Enter and comma keys
    });
    
    var user_id = $('#user_id').val();
    var title= document.getElementById('title');
    var description = document.getElementById('description');
    let btn = document.getElementById('btn');
    let tagInput = document.getElementById('tags');
    let editFlag = false;
    let row_title;
    let row_description;

    $("#form_app24").submit(function(e) {
        e.preventDefault();
        let url = $(this).attr('action');
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: url,
                type: 'POST',
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
                        $("#form_app24")[0].reset();
                        window.location = '/home';
                    }
                }
            });
    });
</script>
@endsection
