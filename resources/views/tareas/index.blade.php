@extends('layouts.app')

@section('title', 'Lista de Tareas')

@section('content')
<div class="container-fluid mt-5">
    <h1 class="mb-4">Lista de Tareas del Proyecto {{ $proyectoId }}</h1>

    <button class="btn btn-primary mb-3" id="createTaskButton">Crear Tarea</button>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="taskTableBody">
            @foreach($tareas as $tarea)
            <tr data-id="{{ $tarea->id }}">
                <td>{{ $tarea->id }}</td>
                <td>{{ $tarea->titulo }}</td>
                <td>{{ $tarea->descripcion }}</td>
                <td>{{ $tarea->completada ? 'Completa' : 'Incompleta' }}</td>
                <td>
                    <button class="btn btn-warning editTaskButton" style="padding: 5px;">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger deleteTaskButton" style="padding: 5px;">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal para crear/editar tarea -->
<div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskModalLabel">Crear Tarea</h5>
            </div>
            <div class="modal-body">
                <form id="taskForm">
                    @csrf
                    <input type="hidden" id="taskId" name="id" value="">
                    <input type="hidden" id="proyecto_id" name="proyecto_id" value="{{ $proyectoId }}">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Título</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" required>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-select form-control" id="completada" name="completada" required>
                            <option value="0">Incompleta</option>
                            <option value="1">Completa</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
                        <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" required>
                    </div>
                    <div id="errorMessagesTarea" class="alert alert-danger d-none"></div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Mostrar modal para crear tarea
        $('#createTaskButton').on('click', function() {
            $('#errorMessagesTarea').addClass('d-none').html('');
            $('#taskModalLabel').text('Crear Tarea');
            $('#taskForm')[0].reset();
            $('#taskId').val('');
            $('#taskModal').modal('show');
        });

        // Enviar el formulario
        $('#taskForm').on('submit', function(e) {
            e.preventDefault(); // Evita el envío del formulario por defecto
            let url = $('#taskId').val() ? '/tareas/' + $('#taskId').val() : '/tareas';
            let method = $('#taskId').val() ? 'PUT' : 'POST';

            // Limpia los mensajes de error anteriores
            $('#errorMessagesTarea').addClass('d-none').html('');

            $.ajax({
                url: url,
                method: method,
                data: $(this).serialize(),
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    // Maneja los errores
                    if (xhr.status === 422) {

                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('#errorMessagesTarea').append('<p>' + value[0] + '</p>');
                        });
                        $('#errorMessagesTarea').removeClass('d-none');
                    } else {

                        $('#errorMessagesTarea').append('<p>Error en el servidor. Inténtalo más tarde.</p>');
                        $('#errorMessagesTarea').removeClass('d-none');
                    }
                }
            });
        });

        // Editar tarea
        $('.editTaskButton').on('click', function() {
            $('#errorMessagesTarea').addClass('d-none').html('');
            const tareaRow = $(this).closest('tr');
            const tareaId = tareaRow.data('id');

            $.get('/tareas/' + tareaId, function(tarea) {
                console.log(tarea);
                $('#taskId').val(tarea.id);
                $('#titulo').val(tarea.titulo);
                $('#descripcion').val(tarea.descripcion);
                $('#completada').val(tarea.completada);
                $('#fecha_vencimiento').val(tarea.fecha_vencimiento);
                $('#taskModalLabel').text('Editar Tarea');
                $('#taskModal').modal('show');
            }).fail(function(xhr) {
                console.log(xhr.responseText);
            });
        });

        // Eliminar tarea
        $('.deleteTaskButton').on('click', function() {
            const tareaRow = $(this).closest('tr');
            const tareaId = tareaRow.data('id');

            if (confirm('¿Estás seguro de que deseas eliminar esta tarea?')) {
                $.ajax({
                    url: '/tareas/' + tareaId,
                    method: 'DELETE',
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            }
        });
    });
</script>
@endsection
