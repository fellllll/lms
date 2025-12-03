@if (Session::has('message'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '{{ Session::get('title') }}',
                text: '{{ Session::get('message') }}',
                icon: '{{ Session::get('icon') == "success" ? "success" : "error" }}',
                confirmButtonText: 'OK',
                confirmButtonColor: "#2463eb",
            });
        });
    </script>
@endif