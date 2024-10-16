@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Lista de Proyectos</h1>

    <button class="btn btn-primary mb-3" id="createProjectButton">Crear Proyecto</button>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Fecha de Inicio</th>
                <th>Fecha de Fin</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proyectos as $proyecto)
            <tr class="project-row" data-id="{{ $proyecto->id }}"  style="cursor: pointer">
                <td>{{ $proyecto->id }}</td>
                <td>{{ $proyecto->nombre }}</td>
                <td>{{ $proyecto->descripcion }}</td>
                <td>{{ $proyecto->fecha_inicio }}</td>
                <td>{{ $proyecto->fecha_fin }}</td>
                <td style="min-width: 140px;">
                    <button class="btn btn-warning editProjectButton" data-id="{{ $proyecto->id }}" style="padding: 5px;">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger deleteProjectButton" data-id="{{ $proyecto->id }}" style="padding: 5px;">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                    <button onclick="location.href='/tareas/proyecto/{{ $proyecto->id }}'" class="btn btn-primary viewTasksButton" data-id="{{ $proyecto->id }}" style="padding: 5px;">
                        <i class="fas fa-tasks"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal para crear/editar proyecto -->
<div class="modal fade" id="projectModal" tabindex="-1" aria-labelledby="projectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="projectModalLabel">Crear/Editar Proyecto</h5>
            </div>
            <div class="modal-body">
                <form id="projectForm">
                    @csrf
                    <input type="hidden" id="projectId" name="id">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" minlength="5" required>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                    </div>
                </form>
            </div>
            <div id="errorMessages" class="alert alert-danger d-none"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="saveProjectButton">Guardar Proyecto</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Abrir el modal para crear un nuevo proyecto
        $('#createProjectButton').click(function() {
             // Limpia los mensajes de error anteriores
             $('#errorMessages').addClass('d-none').html('');
            $('#projectForm')[0].reset();
            $('#projectModalLabel').text('Crear Proyecto');
            $('#projectId').val('');
            $('#projectModal').modal('show');

        });

        // Guardar el proyecto (crear o editar)
        $('#saveProjectButton').click(function() {
            const id = $('#projectId').val();
            const url = id ? `/proyectos/${id}` : '/proyectos';
            const method = id ? 'PUT' : 'POST';

            // Limpia los mensajes de error anteriores
            $('#errorMessages').addClass('d-none').html('');

            $.ajax({
                url: url,
                method: method,
                data: $('#projectForm').serialize(),
                success: function(proyecto) {
                    if (id) {
                        // Actualizar fila existente
                        const row = $(`tr[data-id="${id}"]`);
                        row.find('td:eq(1)').text(proyecto.nombre);
                        row.find('td:eq(2)').text(proyecto.descripcion);
                        row.find('td:eq(3)').text(proyecto.fecha_inicio);
                        row.find('td:eq(4)').text(proyecto.fecha_fin);
                    } else {
                        // Agregar nueva fila
                        $('#projectTableBody').append(`
                            <tr data-id="${proyecto.id}">
                                <td>${proyecto.id}</td>
                                <td>${proyecto.nombre}</td>
                                <td>${proyecto.descripcion}</td>
                                <td>${proyecto.fecha_inicio}</td>
                                <td>${proyecto.fecha_fin}</td>
                                <td>
                                    <button class="btn btn-warning editProjectButton">Editar</button>
                                    <button class="btn btn-danger deleteProjectButton">Eliminar</button>
                                </td>
                            </tr>
                        `);
                    }
                    $('#projectModal').modal('hide');
                },
                error: function(xhr) {
                    // Maneja los errores
                    if (xhr.status === 422) {
                        // Muestra los mensajes de error personalizados
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('#errorMessages').append('<p>' + value[0] + '</p>');
                        });
                        $('#errorMessages').removeClass('d-none');
                    } else {

                        $('#errorMessages').append('<p>Error en el servidor. Inténtalo más tarde.</p>');
                        $('#errorMessages').removeClass('d-none');
                    }
                }
            });
        });

        // Abrir el modal para editar un proyecto
        $(document).on('click', '.editProjectButton', function() {
             // Limpia los mensajes de error anteriores
             $('#errorMessages').addClass('d-none').html('');
            const row = $(this).closest('tr');
            const id = row.data('id');
            const nombre = row.find('td:eq(1)').text();
            const descripcion = row.find('td:eq(2)').text();
            const fecha_inicio = row.find('td:eq(3)').text();
            const fecha_fin = row.find('td:eq(4)').text();

            $('#projectId').val(id);
            $('#nombre').val(nombre);
            $('#descripcion').val(descripcion);
            $('#fecha_inicio').val(fecha_inicio);
            $('#fecha_fin').val(fecha_fin);
            $('#projectModalLabel').text('Editar Proyecto');
            $('#projectModal').modal('show');
        });

        // Eliminar un proyecto
        $(document).on('click', '.deleteProjectButton', function() {
            const row = $(this).closest('tr');
            const id = row.data('id');

            if (confirm('¿Estás seguro de que deseas eliminar este proyecto?')) {
                $.ajax({
                    url: `/proyectos/${id}`,
                    method: 'DELETE',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success: function() {
                        row.remove();
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
