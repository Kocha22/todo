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
@endsection