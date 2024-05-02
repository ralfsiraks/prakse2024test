<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl bg-dark text-light leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded">
                <div class="p-3 h3 text-light">
                    Hi, {{  Auth::user()->name }}!
                </div>
                </div>
                <div class="container text-light mt-5">
                    <form id="form" class="needs-validation" novalidate>
                        <div class="mb-3">
                          <label for="title" class="form-label">Some Title</label>
                          <input type="text" class="form-control bg-dark text-light" name="title" required>
                        </div>
                        <div class="mb-3">
                          <label for="number" class="form-label">Some number</label>
                          <input type="number" class="form-control bg-dark text-light" name="number" required>
                        </div>
                        <div class="mb-3">
                            <label for="text" class="form-label">Some Text</label>
                            <input type="text" class="form-control bg-dark text-light" name="text" required>
                          </div>
                        <button type="submit" class="btn btn-primary">Post</button>
                    </form>
                    <div id="postList" class="mt-5">
                        <!-- Posts go here -->
                    </div>
                </div>
    </div>
    <div class="modal fade" id="post-modal" tabindex="-1" aria-labelledby="post-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content bg-dark text-light container p-5">
            <form id="update-post-form" class="needs-validation" novalidate>
                @method('PATCH') 
                <div class="mb-3">
                    <label for="modal-title" class="form-label">Title</label>
                    <input type="text" class="form-control bg-dark text-light" name="title" id="modal-title" required>
                </div>
                <div class="mb-3">
                    <label for="modal-number" class="form-label">Number</label>
                    <input type="number" class="form-control bg-dark text-light" name="number" id="modal-number" required>
                </div>
                <div class="mb-3">
                    <label for="modal-text" class="form-label">Text</label>
                    <input type="text" class="form-control bg-dark text-light" name="text" id="modal-text" required>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
          </div>
        </div>
      </div>
      
</x-app-layout>

<script>
    // Builds the post datatable
    function loadPosts() {
    $.ajax({
        url: '{{ route("get-posts") }}',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            const postTable = $('#postList');
            postTable.empty();
            const table = $('<table>').attr('id', 'postsTable');
            const tableHeader = `
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Number</th>
                        <th>Text</th>
                        <th></th>
                    </tr>
                    
                </thead>`;
            table.append(tableHeader);

            const tableBody = $('<tbody>');
            response.forEach(post => {
                const tableRow = `
                    <tr data-post-id="${post.id}" class="link-primary">
                        <td data-bs-toggle="modal" data-bs-target="#post-modal" data-id="${post.id}">${post.title}</td>
                        <td data-bs-toggle="modal" data-bs-target="#post-modal" data-id="${post.id}">${post.number}</td>
                        <td data-bs-toggle="modal" data-bs-target="#post-modal" data-id="${post.id}">${post.text}</td>
                        <td>
                            <button class="btn btn-danger btn-sm delete-post" data-post-id="${post.id}">Delete</button>
                        </td>
                    </tr>`;
                tableBody.append(tableRow);
            });
            table.append(tableBody);
            postTable.append(table);

            $(document).ready(function() { 
                $('#postsTable').DataTable({
                    stateSave: true,
                  });
            });
        },
        error: function(error) {
            console.error('Error:', error);
        }
    });
}


    $(document).ready(function() {
        // Handle's post form submit
        const form = $('#form');
        form.submit(function(event) {
        if (!form[0].checkValidity()) { 
            event.preventDefault();
            event.stopPropagation();
        } else {
            event.preventDefault();
            const formData = new FormData(this);
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '{{ url("store-post") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                'X-CSRF-TOKEN': csrfToken
            },
                success: function(response) {
                    alert("Posted successfully!");
                    form.trigger('reset');
                    form.removeClass('was-validated');
                    loadPosts();
                },
                error: function(xhr, status, error) {
                console.error('Error:', error);
                console.log('Response:', xhr.responseText);
                }
            });
        }
            form.addClass('was-validated'); 
        });
        loadPosts();

        // Get's the modal info
        $('#postList').on('click', 'td[data-bs-toggle="modal"]', function() {
            const id = $(this).data('id');

            $.ajax({
                url: '{{ url("get-post") }}/' + id,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Populate the inputs
                    $('#modal-title').val(data.title);
                    $('#modal-number').val(data.number);
                    $('#modal-text').val(data.text);
                    $('#post-modal').data('post-id', id);
                },
                error: function(error) {
                    console.error('Error fetching data:', error);
                }
            });
        });
    });

    // Handle update post form
    const updateForm = $('#update-post-form');
    updateForm.submit(function(event) {
        event.preventDefault();
        const postId = $('#post-modal').data('post-id');
        const formData = new FormData(this);
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: '{{ url("update-post") }}/' + postId,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                console.log(response);
                alert('Post updated successfully!');
                $('#post-modal').modal('hide');
                loadPosts();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('Failed to update post.');
            }
        });
        
        updateForm.addClass('was-validated'); 
    });

    // Delete post
    $('#postList').on('click', '.delete-post', function() {
        console.log($(this).data('post-id'));
        const postId = $(this).data('post-id');

        $.ajax({
            url: `{{ url('delete-post') }}/${postId}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert('Post deleted successfully!');
                loadPosts(); 
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('Failed to delete post.');
            }
        });
    });
</script>

