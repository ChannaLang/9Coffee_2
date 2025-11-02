<!DOCTYPE html>
<html>
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta charset="utf-8">
        <title>Admin Panel</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Headings / Logo -->
        <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
        <link href="{{ asset('assets/css/admin.css')}}" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
        <link rel="stylesheet" href="{{ asset('assets/css/fonts/icomoon/icomoon.woff') }}">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
       


    </head>
<body style="background-image: url('{{ asset('assets/images/bg_1.jpg') }}');
             background-size: cover;
             background-position: center;
             background-attachment: fixed;
             min-height: 100vh;">
    <div id="wrapper" style="background: rgba(0,0,0,0.6); min-height: 100vh;">
        <div class="container-fluid py-4 text-light">
            @yield('content')
        </div>
    </div>
     <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Single Delete Confirmation
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This order will be permanently deleted.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#b7410e',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    background: '#201d1dff',
                    color: '#fff'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Delete All Confirmation
        const deleteAllBtn = document.querySelector('.btn-delete-all');
        if (deleteAllBtn) {
            deleteAllBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                Swal.fire({
                    title: 'Delete All Orders?',
                    text: "This will remove all orders permanently.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#b7410e',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete all!',
                    background: '#3e2f2f',
                    color: '#fff'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        }
    });
    </script>
</body>

</html>
