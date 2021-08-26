@extends('prepagobags::layouts.dashboard')

@section('content')
<style>
    label.btn {
        font-size: 10px !important;
        text-transform: uppercase !important;
        color: white !important;
    }

    .switch-span {
        font-size: 14px;
        padding-bottom: 2px;
    }

    table#tbl_detalle th {
        font-size: 14px;
    }

    table#tbl_detalle td {
        font-size: 12px;
    }

    label {
        font-size: 10px;
    }

    .alert {
        font-size: 10px !important;
        padding: 2px;
        display: block;
    }

    .table-due-invoices {

        width: 100%;
        margin-left: 10%;
        height: 150px;
        overflow-y: scroll;
        padding: 10px;

    }

    .table-postpago-plan-company {
        margin-left: 10%;
        text-align: center;
    }
    .modal-information p{
        margin-bottom: 0;
    }
    .modal-information label{
        margin-bottom: 0;
    }
    /*
        *
        * ==========================================
        * CUSTOM UTIL CLASSES
        * ==========================================
        *
        */
    /* toggle switches with bootstrap default colors */
    /* Switch Flat
    ==========================*/
    .cmn-toggle {
        position: absolute;
        margin-left: -9999px;
        visibility: hidden;
    }

    .cmn-toggle+label {
        display: block;
        position: relative;
        cursor: pointer;
        outline: none;
        user-select: none;
    }

    input.cmn-toggle-round+label {
        padding: 1px;
        width: 25px;
        height: 13px;
        background-color: #dddddd;
        border-radius: 35px;
    }

    input.cmn-toggle-round+label:before,
    input.cmn-toggle-round+label:after {
        display: block;
        position: absolute;
        top: 1px;
        left: 1px;
        bottom: 1px;
        content: "";
    }

    input.cmn-toggle-round+label:before {
        right: 1px;
        background-color: #f1f1f1;
        border-radius: 25px;
        transition: background 0.4s;
    }

    input.cmn-toggle-round+label:after {
        width: 11px;
        background-color: #fff;
        border-radius: 100%;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        transition: margin 0.4s;
    }

    input.cmn-toggle-round:checked+label:before {
        background-color: #279DF3;
    }

    input.cmn-toggle-round:checked+label:after {
        margin-left: 12px;
    }
</style>

<div class="">
    <div class="" style="margin-top:50px;">
        <nav class="">
            <h3>Clientes de la plataforma</h3>
            <form action="{{ url('dashboard/clients') }}">
                <div class="form-group has-search d-inline-block col-md-8">
                    <input type="text" class="form-control" placeholder="Buscar por correo del dueño" id="search" name="search" value="{!! $search !!}" autocomplete="off" autofocus="on">
                </div>
                <div class="form-group d-inline-block">
                    <select name="phase" value="{!! $phase !!}" class="form-control">
                        @if ($phase == '')
                        <option value="" selected>Todas las fases</option>
                        <option value="Testing">Fase Testing</option>
                        <option value="Piloto testing">Fase Piloto</option>
                        <option value="Production">Fase Producción</option>
                        @elseif ($phase == "Testing")
                        <option value="">Todas las fases</option>
                        <option value="Testing" selected>Fase Testing</option>
                        <option value="Piloto testing">Fase Piloto</option>
                        <option value="Production">Fase Producción</option>
                        @elseif ($phase == "Piloto testing")
                        <option value="">Todas las fases</option>
                        <option value="Testing">Fase Testing</option>
                        <option value="Piloto testing" selected>Fase Piloto</option>
                        <option value="Production">Fase Producción</option>
                        @elseif ($phase == "Production")
                        <option value="">Todas las fases</option>
                        <option value="Testing">Fase Testing</option>
                        <option value="Piloto testing">Fase Piloto</option>
                        <option value="Production" selected>Fase Producción</option>
                        @endif
                    </select>
                </div>
                <div class="form-group d-inline-block">
                    <button type="submit" class="btn form-control btn-primary d-inline-block"> Filtrar</button>
                </div>
            </form>
        </nav>
        <div class="table-style-custom col-md-12">
            <table class="table table-striped table-bordered" style="table-layout: fixed;">
                <thead>

                    <th width="15%">Compañia</th>
                    <th width="25%">Dueño</th>
                    <th width="15%">Fecha creación</th>
                    <th width="10%">Tipo de Cuenta</th>
                    <th align="center" width="15%">Fase</th>
                    <th align="center" width="10%">Estado</th>
                    <th width="10%">Acciones</th>
                </thead>
                <tbody>
                    @foreach ($clientsPrepago as $client)
                    <tr>

                        <td>{{ json_decode($client->settings)->name }}</td>
                        <td>{{ $client->email }}</td>
                        <td>{{ $client->created_at }}</td>
                        <td align="center">{!! $client->account_type_badge !!}</td>
                        <td align="center">{!! $client->phase !!}</td>
                        <td align="center">{{ $client->enabled == 'active' ? 'Activo' : 'Suspendido' }}</td>
                        <td align="center">
                            <div class="dropdown">
                                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Acciones
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    @if ($client->phase == 'Testing')
                                    <a class="dropdown-item" data-type="actions-dashboard" data-href="{{ URL::to('dashboard/form-phase-piloto/' . $client->company_id) }}" data-container=".action-modal">Pasar a pruebas piloto</a>
                                    <a class="dropdown-item" data-type="actions-dashboard" data-href="{{ URL::to('dashboard/form-phase-production/' . $client->company_id) }}" data-container=".action-modal">Pasar a producción</a>
                                    @elseif( $client->phase == 'Piloto testing')
                                    <a class="dropdown-item" data-type="actions-dashboard" data-href="{{ URL::to('dashboard/form-phase-production/' . $client->company_id) }}" data-container=".action-modal">Pasar a producción</a>
                                    @endif
                                    @if ($client->is_postpago)
                                    @if ($client->enabled == 'active')
                                    <a class="dropdown-item disabled" href="#">Suspender</a>
                                    @else
                                    <a class="dropdown-item disabled" data-type="data-action" data-onclick="enablePostpago( $client->company_id )">Habilitar</a>
                                    @endif
                                    @endif
                                    <a class="dropdown-item" data-type="actions-dashboard" data-href="{{ URL::to('dashboard/form-information/' . $client->company_id) }}" data-container=".action-modal">Información</a>

                                    @if ($client->production == 0 || $client->company_id == env('COMPANY_ADMIN'))
                                    <a class="dropdown-item disabled" href="#">Vincular/Desvincular</a>
                                    @else

                                    <a class="dropdown-item" data-type="actions-dashboard" data-href="{{ URL::to('dashboard/form-linked/' . $client->company_id) }}" data-container=".action-modal">Vincular/Desvincular</a>

                                    @endif

                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $clientsPrepago->withQueryString()->links() }}
        </div>
    </div>
    <div class="modal fade action-modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
</div>

<script>
    $(document).on('click', '[data-type=actions-dashboard]', function() {
        jQuery.get($(this).attr('data-href'), function(res) {
            $(".action-modal").html(res);
            $(".action-modal").modal("show");
        });
    });
    //On display of add contact modal
    $('.action-modal').on('show.bs.modal', function(e) {
        $('#company_client_id').selectpicker();
        var modal = $(this);

        $('form#actionFormLabel')
            .submit(function(e) {
                e.preventDefault();
            })
            .validate({
                rules: {
                    client_id: {
                        required: true
                    },
                    client_secret: {
                        required: true
                    }
                },
                messages: {
                    client_id: {
                        required: "El client Id es requerido",
                    },
                    client_secret: {
                        required: "El client secret es requerido"
                    }
                },
                submitHandler: function(form) {
                    e.preventDefault();
                    console.log("submit en este formulario");
                    var data = $(form).serialize();
                    var user = @json($user_hash);

                    $(form)
                        .find('button[type="submit"]')
                        .attr('disabled', true);
                    $.ajax({
                        method: 'POST',
                        url: $(form).attr('action'),
                        headers: {
                            'user': user.hash
                        },
                        dataType: 'json',
                        data: data,
                        success: function(result) {
                            if (result.success == true) {
                                $('div.action-modal').modal('hide');
                                alert("Cliente Vinculado Satisfactoriamente");
                                location.reload();
                            } else {
                                alert(result.msg);
                            }
                        },
                        error: function(response) {
                            $.each(response.responseJSON.errors, function(field_name,
                                error) {
                                $(document).find('[name=' + field_name + ']').after(
                                    '<span class="alert alert-danger">' +
                                    error + '</span>');
                            })

                            if (response.success == false) {
                                alert(result.msg);
                            }

                            $(form)
                                .find('button[type="submit"]')
                                .attr('disabled', false);
                        }
                    });
                },
            });
    });


    $('#search').on("keypress", function(e) {
        if (e.which == 13) {
            location.reload();
        }
    });
</script>

@endsection