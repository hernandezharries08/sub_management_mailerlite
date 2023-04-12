@extends('layout')

@section('content')
    <h1>Subscribers</h1>
    <a href="{{ route('subscribers.create') }}" class="btn btn-primary mb-3">Add Subscriber</a>

    <table id="subscribers-table" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Email</th>
                <th>Name</th>
                <th>Country</th>
                <th>Subscribe Date</th>
                <th>Subscribe Time</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            let nextCursor = "";
            let prevCursor = "";
            const subscribersTable = $('#subscribers-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("subscribers.data") }}',
                    dataSrc: function (json) {
                        // Use the nextLink and prevLink URLs to fetch the next/previous page
                        const nextLink = json.nextLink;
                        const prevLink = json.prevLink;
                        console.log(nextLink);
                        nextCursor = nextLink;
                        prevCursor = prevLink;
                        return json.data;
                    },
                },
                columns: [
                    { data: 'email' },
                    { data: 'fields.name' },
                    { data: 'fields.country' },
                    {
                        data: 'subscribed_at',
                        render: function (data, type, row) {
                            if (type === 'display') {
                                const date = new Date(data);
                                return date.toLocaleDateString('en-GB');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'subscribed_at',
                        render: function (data, type, row) {
                            if (type === 'display') {
                                const date = new Date(data);
                                return date.toLocaleTimeString('en-GB', 
                                { 
                                    hour12: false, 
                                    hour: '2-digit', 
                                    minute: '2-digit' 
                                });
                            }
                            return data;
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row) {
                            if (type === 'display') {
                                const editBtn = `<a href="/subscribers/${row.id}/edit" class="btn btn-primary btn-sm">Edit</a>`;
                                const deleteBtn = `<button data-id="${row.id}" class="btn btn-danger btn-sm delete-btn">Delete</button>`;
                                return editBtn + ' ' + deleteBtn;
                            }
                            return '';
                        },
                        orderable: false,
                    },
                ],
                lengthMenu: [10, 25, 50, 75, 100], // Set the entries per page
                pageLength: 10, // Set the default entries per page
                drawCallback: function (settings) {
                    if (nextCursor) {
                        $('#subscribers-table_next').removeClass('disabled');
                    } else {
                        $('#subscribers-table_next').addClass('disabled');
                    }

                    if (prevCursor) {
                        $('#subscribers-table_previous').removeClass('disabled');
                    } else {
                        $('#subscribers-table_previous').addClass('disabled');
                    }
                    
                    $('#subscribers-table_next').on('click', function () {
                        const cursor = nextCursor;
                        if(cursor!=null){
                            subscribersTable.ajax.url('{{ route("subscribers.data") }}?cursor='+cursor).load();
                        }
                    });
                    $('#subscribers-table_previous').on('click', function () {
                        const cursor = prevCursor;
                        if(cursor!=null){
                            subscribersTable.ajax.url('{{ route("subscribers.data") }}?cursor='+cursor).load();
                        }
                    });
                },         
            });

            $('#subscribers-table').on('click', '.delete-btn', function () {
                const subscriberId = $(this).data('id');
                const deleteUrl = `/subscribers/${subscriberId}`;
                
                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        // Refresh the DataTable after successful deletion
                        $('#subscribers-table').DataTable().ajax.reload();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        // Handle errors during the deletion process
                        console.error('Error deleting subscriber:', textStatus, errorThrown);
                    }
                });
                console.log('Delete subscriber with ID:', subscriberId);
            });
        });
    </script>
@endsection
