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

    .modal-information p {
        margin-bottom: 0;
    }

    .modal-information label {
        margin-bottom: 0;
    }

    .table-prepago-invoices thead {

        font-size: 12px;
        font-weight: bold;

    }

    .table-prepago-invoices tbody {
        font-size: 13px;
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
        <nav class="navbar custom-search">

            <form action="{{ url('dashboard/clients') }}">
                <div class="form-group has-search d-inline-block col-md-4">
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
                <div class="form-group d-inline-block" style="float:right">
                    {{ $clientsPrepago->withQueryString()->links() }}
                </div>

            </form>
        </nav>
        <div class="table-style-custom col-md-12">
            <table class="table table-striped" style="border:1px #dee2e6 solid;table-layout: fixed;">
                <thead>

                    <th width="15%">COMPAÑIA</th>
                    <th width="25%">DUEÑO</th>
                    <th width="10%">CREADO</th>
                    <th width="10%">TIPO</th>
                    <th width="8%">FASE</th>
                    <th width="7%">ESTADO</th>
                    <th width="5%"></th>
                </thead>
                <thead>

                    <th width="15%">COMPAÑIA</th>
                    <th width="25%">DUEÑO</th>
                    <th width="10%">CREADO</th>
                    <th width="10%">TIPO</th>
                    <th width="8%">FASE</th>
                    <th width="7%">ESTADO</th>
                    <th width="5%"></th>
                </thead>
                <tbody>
                    @foreach ($clientsPrepago as $client)
                    <tr>

                        <td>{{ json_decode($client->settings)->name }}</td>
                        <td>{{ $client->email }}</td>
                        <td>{{ $client->created_at->diffForHumans() }}</td>
                        <td>{!! $client->account_type_badge !!}</td>
                        <td>{!! $client->phase !!}</td>
                        <td>{{ $client->enabled == 'active' ? 'Activo' : 'Suspendido' }}</td>
                        <td>
                            <div class="dropdown dropleft">
                                <a class="dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
                                        <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z" />
                                    </svg>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    @if ($client->phase == 'Testing')
                                    <a class="dropdown-item" data-type="actions-dashboard" data-href="{{ URL::to('dashboard/form-phase-piloto/' . $client->company_id) }}" data-container=".action-modal">Pasar a pruebas piloto</a>
                                    <a class="dropdown-item" data-type="actions-dashboard" data-href="{{ URL::to('dashboard/form-phase-production/' . $client->company_id) }}" data-container=".action-modal">Pasar a producción</a>
                                    @elseif( $client->phase == 'Piloto testing')
                                    <a class="dropdown-item" data-type="actions-dashboard" data-href="{{ URL::to('dashboard/form-phase-production/' . $client->company_id) }}" data-container=".action-modal">Pasar a producción</a>
                                    @endif
                                    @if ($client->is_postpago)
                                    @if ($client->enabled == 'active')
                                    <a class="dropdown-item" data-type="actions-dashboard" data-href="{{ URL::to('dashboard/form-suspend/'. $client->company_id) }}" data-container=".action-modal">Suspender</a>
                                    @else
                                    <a class="dropdown-item" data-type="actions-dashboard" data-href="{{ URL::to('dashboard/form-up/'. $client->company_id) }}" data-container=".action-modal">Habilitar</a>
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