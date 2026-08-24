{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: @json(session('success')),
                confirmButtonText: 'OK',
                confirmButtonColor: '#198754'
            });
        });
    </script>
@endif

@if (session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: @json(session('error')),
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc3545'
            });
        });
    </script>
@endif

@if (session('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: @json(session('warning')),
                confirmButtonText: 'OK',
                confirmButtonColor: '#ffc107'
            });
        });
    </script>
@endif

@if (session('info'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                text: @json(session('info')),
                confirmButtonText: 'OK',
                confirmButtonColor: '#0dcaf0'
            });
        });
    </script>
@endif

@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const errors = @json($errors->all());

            let errorList = '<ul style="text-align:left; margin-bottom:0;">';

            errors.forEach(function (error) {
                errorList += '<li>' + error + '</li>';
            });

            errorList += '</ul>';

            Swal.fire({
                icon: 'error',
                title: 'Data Belum Sesuai',
                html: errorList,
                confirmButtonText: 'Perbaiki',
                confirmButtonColor: '#dc3545'
            });
        });
    </script>
@endif
