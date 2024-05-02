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
                        <!-- Posts will go here -->
                    </div>
                </div>
    </div>
</x-app-layout>

<script>
    function loadPosts() {
    $.ajax({
        url: '{{ route("get-posts") }}',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            const postTable = $('#postList');
            postTable.empty(); // Clear previous posts

            // Create a new table element
            const table = $('<table>').attr('id', 'postsTable');

            // Append table header
            const tableHeader = `
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Number</th>
                        <th>Text</th>
                        <th></th>
                    </tr>
                    
                </thead>
            `;
            table.append(tableHeader);

            // Append table body
            const tableBody = $('<tbody>');
            response.forEach(post => {
                const tableRow = `
                    <tr data-post-id="${post.id}">
                        <td>${post.title}</td>
                        <td>${post.number}</td>
                        <td>${post.text}</td>
                        <td>
                                <button class="btn btn-danger btn-sm delete-post" data-post-id="${post.id}">Delete</button>
                            </td>
                    </tr>
                `;
                tableBody.append(tableRow);
            });
            table.append(tableBody);

            // Append table to postTable container
            postTable.append(table);

            // Initialize DataTables on the table
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
        const form = $('#form');
        form.submit(function(event) {
        if (!form[0].checkValidity()) { 
            event.preventDefault();
            event.stopPropagation();
        } else {
            event.preventDefault();
            const formData = new FormData(this);
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            formData.append('_token', csrfToken);
            $.ajax({
                url: '{{ url("store-post") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
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
    });

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

