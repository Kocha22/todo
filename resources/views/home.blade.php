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

                     <section class="content">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12">
                                    <!-- Default box -->
                                    <div class="card">
                                        <div class="card-body">
                                            <input type="hidden" id="user_id" name="user_id" value="{{ $user->id }}" />
                                            <div class="upper_nav">
                                                <div id="tagsContainer">
                                                        <button class="button_1 btn bootstrap-tagsinput active" name="button" data-id="all">All</button>
                                                        @foreach ($tags as $tag)
                                                            <button class="button_1 btn bootstrap-tagsinput" name="button" data-id="{{ $tag->id }}">{{ $tag->name }}</button>
                                                        @endforeach
                                                </div>
                                                <a href="{{ route('newpost') }}" class="btn btn-primary">Добавить новый пост</a>
                                            </div>
                                            <div>
                                            <form id="tagSearchForm" class="form-inline">
                                                <input type="text" name="searchTag" id="searchTagInput" class="form-control" placeholder="Поиск">
                                            </form>
                                            </div>
                                            <div class="form-home26 card-body">
                                                <table id="customers_next" class="table table-hover text-wrap text-sm">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Название</th>
                                                            <th>Описание</th>
                                                            <th>Картинка</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbody"></tbody>
                                                </table>
                                                <span id="paginationLinks" class="pagination">
                                                </span>
                                            </div>
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
    var page = 1;

    function showdata() {
        $.ajax({
            url: `getPosts/${user_id}`,
            method: 'GET',
            dataType: 'json',
            data: { page: page },
            success: function(data) {
                $("#tbody").html(data.table_data);
                var currentPage = data.currentPage;
                var lastPage = data.lastPage;
                var paginationLinks = $('#paginationLinks');
                paginationLinks.empty();
                var limit = 5; // Change this to set the number of visible pages

                // Previous page link
                if (currentPage > 1) {
                    paginationLinks.append('<li class="page-item"><a class="page-link" href="#" data-page="' + (currentPage - 1) + '">&laquo;</a></li>');
                } else {
                    paginationLinks.append('<li class="page-item disabled"><span class="page-link">&laquo;</span></li>');
                }

                // Pages links
                var start = Math.max(1, currentPage - Math.floor(limit / 2));
                var end = Math.min(lastPage, start + limit - 1);
                if (end - start < limit) {
                    start = Math.max(1, end - limit + 1);
                }

                if (start > 1) {
                    paginationLinks.append('<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>');
                    if (start > 2) {
                        paginationLinks.append('<li class="page-item disabled"><span class="page-link">&hellip;</span></li>');
                    }
                }

                for (var i = start; i <= end; i++) {
                    var activeClass = i === currentPage ? 'active' : '';
                    paginationLinks.append('<li class="page-item ' + activeClass + '"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>');
                }

                if (end < lastPage) {
                    if (end < lastPage - 1) {
                        paginationLinks.append('<li class="page-item disabled"><span class="page-link">&hellip;</span></li>');
                    }
                    paginationLinks.append('<li class="page-item"><a class="page-link" href="#" data-page="' + lastPage + '">' + lastPage + '</a></li>');
                }

                // Next page link
                if (currentPage < lastPage) {
                    paginationLinks.append('<li class="page-item"><a class="page-link" href="#" data-page="' + (currentPage + 1) + '">&raquo;</a></li>');
                } else {
                    paginationLinks.append('<li class="page-item disabled"><span class="page-link">&raquo;</span></li>');
                }

                console.log(currentPage)
            }
        });
    }
    showdata();

        // Listen for clicks on the pagination links
    $(document).on('click', '#paginationLinks a', function (e) {
        e.preventDefault();
        page = $(this).data('page');

        showdata(); // Call the getUsers() function to get the data for the selected page
    });

    $(".button_1").click(function(e) {
        e.preventDefault();
        $('.button_1').removeClass('active');
        $(this).addClass('active'); 
        let id = $(this).attr('data-id');
        $.ajax({    
           url:"filterPosts/" + id,        
           method:'GET',        
           data:{id:id},        
           dataType:'json',        
           success:function(data) {    
              $("#tbody").html(data.table_data);  
           }        
          })        
    });

    $("#tbody").on("click", ".delete-icon", function() {
        let post_id = $(this).attr('data-sid');
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "/deletepost/" + post_id,
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

    $('#searchTagInput').on('input', function() {
        // Get the search query from the input field
        var query = $(this).val().trim();

        // Make an AJAX request to the server
        $.ajax({
                url: '/search',
                method: 'GET',
                data: { 
                    query: query,
                    page: page
                },
                dataType: 'json',
                success: function(data) {
                    $("#tbody").html(data.table_data);
                    var currentPage = data.currentPage;
                    var lastPage = data.lastPage;
                    var paginationLinks = $('#paginationLinks');
                    paginationLinks.empty();
                    var limit = 5; // Change this to set the number of visible pages

                    // Previous page link
                    if (currentPage > 1) {
                        paginationLinks.append('<li class="page-item"><a class="page-link" href="#" data-page="' + (currentPage - 1) + '">&laquo;</a></li>');
                    } else {
                        paginationLinks.append('<li class="page-item disabled"><span class="page-link">&laquo;</span></li>');
                    }

                    // Pages links
                    var start = Math.max(1, currentPage - Math.floor(limit / 2));
                    var end = Math.min(lastPage, start + limit - 1);
                    if (end - start < limit) {
                        start = Math.max(1, end - limit + 1);
                    }

                    if (start > 1) {
                        paginationLinks.append('<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>');
                        if (start > 2) {
                            paginationLinks.append('<li class="page-item disabled"><span class="page-link">&hellip;</span></li>');
                        }
                    }

                    for (var i = start; i <= end; i++) {
                        var activeClass = i === currentPage ? 'active' : '';
                        paginationLinks.append('<li class="page-item ' + activeClass + '"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>');
                    }

                    if (end < lastPage) {
                        if (end < lastPage - 1) {
                            paginationLinks.append('<li class="page-item disabled"><span class="page-link">&hellip;</span></li>');
                        }
                        paginationLinks.append('<li class="page-item"><a class="page-link" href="#" data-page="' + lastPage + '">' + lastPage + '</a></li>');
                    }

                    // Next page link
                    if (currentPage < lastPage) {
                        paginationLinks.append('<li class="page-item"><a class="page-link" href="#" data-page="' + (currentPage + 1) + '">&raquo;</a></li>');
                    } else {
                        paginationLinks.append('<li class="page-item disabled"><span class="page-link">&raquo;</span></li>');
                    }

                    console.log(currentPage)
                },
                error: function(xhr, status, error) {
                console.log('Error: ' + error);
                }
            });
       });
</script>
@endsection
