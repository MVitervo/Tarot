<?php
require_once __DIR__ . '/../auth.php';
requireAdmin();
?>

<div class="form-panel w-11/12 mx-auto rounded-2xl border border-gold/40
    p-4 shadow-gold transition-colors duration-300 sm:p-6 lg:p-8 mb-4">

    <!-- Encabezado y acciones -->
    <div class="mb-6 flex flex-col gap-4 border-b border-gold/30 pb-5
        sm:flex-row sm:items-center sm:justify-between">

        <div>
            <p class="mb-1 text-xs uppercase tracking-[0.3em] text-gold">
                Administración
            </p>

            <h2 class="font-display text-xl font-semibold text-gray-900
                dark:text-parchment sm:text-2xl">
                Lista de registros
            </h2>

            <p class="mt-2 text-sm text-gray-600 dark:text-parchment/60">
                Consulta y administra las citas registradas.
            </p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
            <button
                type="button"
                onclick="loadPage('/add_register')"
                class="inline-flex items-center justify-center gap-2 rounded-lg
                    border border-gold bg-gold px-4 py-2.5 font-semibold
                    text-gray-950 shadow-md transition-all duration-300
                    hover:bg-gold/80 hover:shadow-gold
                    focus:outline-none focus:ring-2 focus:ring-gold/60">

                <i data-lucide="plus" class="size-5"></i>
                Agregar registro
            </button>

            <button
                type="button"
                onclick="loadPage('/modify_dates_schedules')"
                class="inline-flex items-center justify-center gap-2 rounded-lg
                    border border-gold/50 bg-white/60 px-4 py-2.5
                    font-semibold text-gray-800 transition-all duration-300
                    hover:border-gold hover:bg-gold/10
                    dark:bg-violet/60 dark:text-parchment
                    dark:hover:bg-gold/10
                    focus:outline-none focus:ring-2 focus:ring-gold/60">

                <i data-lucide="calendar-clock" class="size-5"></i>
                Editar horarios
            </button>
        </div>
    </div>

    <!-- Tabla -->
    <div class="containerTable w-full overflow-x-auto rounded-xl
        border border-gold/30 bg-white/70
        dark:bg-ink/50">

        <table class="appoinmentTable w-full min-w-[1000px] border-collapse
            text-left text-sm">

            <thead class="bg-gold/10 text-xs uppercase tracking-wider
                text-gray-700 dark:text-gold">

                <tr>
                    <th class="whitespace-nowrap border-b border-gold/30
                        px-4 py-4 font-semibold">Nombre y apellido</th>

                    <th class="whitespace-nowrap border-b border-gold/30
                        px-4 py-4 font-semibold">Signo zodiacal</th>

                    <th class="whitespace-nowrap border-b border-gold/30
                        px-4 py-4 font-semibold">Nacionalidad</th>

                    <th class="whitespace-nowrap border-b border-gold/30
                        px-4 py-4 font-semibold">Correo</th>

                    <th class="whitespace-nowrap border-b border-gold/30
                        px-4 py-4 font-semibold">Teléfono</th>

                    <th class="whitespace-nowrap border-b border-gold/30
                        px-4 py-4 font-semibold">Fecha</th>

                    <th class="whitespace-nowrap border-b border-gold/30
                        px-4 py-4 font-semibold">Horario</th>

                    <th class="whitespace-nowrap border-b border-gold/30
                        px-4 py-4 font-semibold">
                        Última modificación
                    </th>

                    <th class="whitespace-nowrap border-b border-gold/30
                        px-4 py-4 text-center font-semibold">Opciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gold/15 text-gray-800
                dark:text-parchment/90">
            </tbody>
        </table>
    </div>

    <p class="mt-3 text-xs text-gray-500 dark:text-parchment/50">
        Desliza horizontalmente la tabla para consultar todas las columnas.
    </p>
</div>


<script>
    $(function() {
        loadRegister();
    });

    $('#btnLogout').removeClass('hidden');
    $('#btnLogout').addClass('inline-flex');

    // document.querySelector('#contentButtonTheme').style.display = 'block';

    function loadRegister() {
        $('.appoinmentTable').DataTable({
            language: {
                // url: '/config/datatables-bs5/language-spanish.json',
                emptyTable: 'No hay datos disponibles'
            },
            ordering: false,
            ajax: {
                method: 'GET',
                url: '/api/listRegister',
                dataSrc: 'data' // Indica que los datos están en la raíz del JSON
            },
            // los valores que recibira data estan pendientes de la tabla en la bd
            columns: [{
                    data: 'nameLastname'
                },
                {
                    data: 'zodiacSign'
                },
                {
                    data: 'nationality'
                },
                {
                    data: 'email'
                },
                {
                    data: 'phone'
                },
                {
                    data: 'date',
                    width: '10%'
                },
                {
                    data: 'schedule',
                    render: function(data, type, row) {
                        return `<div class="whitespace-normal">
                            ${data}
                        </div>`;
                    }
                },
                {
                    data: 'lastEditDt'
                },
                {
                    data: 'options'
                }
            ],
            initComplete: function(settings, json) {
            }
        });
    }

    function editRegister(id) {
        // window.id = id;
        sessionStorage.setItem('editRegisterId', id);
        loadPage('/edit_register'); // ya muestra la vista
    }

    function deleteRegister(id) {
        const idFormatter = `id=${id}`;
        // agregar un aviso para poder confirmar la eliminacion
        modalconfirmation(
            'Esta acción eliminará el registro seleccionado. ¿Deseas continuar?',

            function() {

                $.ajax({
                    method: 'POST',
                    url: '/api/deleteRegister',
                    data: idFormatter,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            modalSuccess(response.message);
                        } else {
                            modalSuccess(response.message);
                        }
                    },
                    error: function(error) {
                        modalSuccess(error.message);
                    }
                });

            }
        );
    }
</script>