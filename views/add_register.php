<?php
require_once __DIR__ . '/../auth.php';
requireAdmin();
?>

<form class="px-4 mb-4" id="addAppointmentForm">
    <div class="w-full md:w-1/2 mx-auto">
        <?php require __DIR__ . '/form.php' ?>
    </div>
</form>

<script>

    $('#btnLogout').removeClass('hidden');
    $('#btnLogout').addClass('inline-flex');

    var form = document.querySelector('#addAppointmentForm');

    form.addEventListener('submit', function(event) {

        event.preventDefault(); // evita que la pagina recargue

        const appointment = $('#addAppointmentForm').serialize(); // toma todos los valores de los campos

        // retroalimentacion ya busque y me quedare siempre con esta forma de manera estandar si por alguna razon los nombres de los campos del formulario
        // con diferentes a los del modelo entonces los voy a mappear directamente en el contrador

        $.ajax({
            method: 'POST',
            url: '/api/saveAppointmentAdmin',
            data: appointment,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    modalSuccess(response.message);
                } else {
                    modalError(
                        response.message
                    );
                }
            },
            error: function(response) {
                modalError(response.message);
            }
        });

    });
</script>