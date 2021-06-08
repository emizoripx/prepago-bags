<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.9.0/jquery.validate.min.js"></script>
    <title>Clientes</title>

    <style>
        .has-search .form-control-feedback {
            position: absolute;
            z-index: 2;
            display: block;
            width: 2.375rem;
            height: 2.375rem;
            line-height: 2.375rem;
            text-align: center;
            pointer-events: none;
            color: #aaa;
        }

        .table-style-custom {
            position: fixed;
            margin-top: 5%;
            width: 80%;
            height: 600px;
            overflow-y: scroll;
        }
    </style>
</head>

<body>
    <div class="col-md-10 offset-md-1" style="margin-top:50px;">
        <nav class="position-fixed col-md-12">
            <h3>Clientes de la plataforma</h3>

            <form action="{{url('dashboard/clients')}}">

                <div class="form-group has-search d-inline-block col-md-6">
                    <span class="fa fa-search form-control-feedback"></span>
                    <input type="text" class="form-control" placeholder="Buscar por correo del dueño" id="search" name="search" value="{!! $search !!}" autocomplete="off" autofocus="on">
                </div>
                <div class="form-group d-inline-block">
                    <select name="phase" value="{!! $phase !!}" class="form-control">
                        @if ($phase == "")
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
                    
                    <th width="10%">Compañia</th>
                    <th width="20%">Dueño</th>
                    <th width="15%">Fecha creación</th>
                    <th width="10%">Tipo de Cuenta</th>
                    <th align="center" width="10%">Fase</th>
                    <th align="center" width="10%">Estado</th>
                    <th width="10%">Acciones</th>
                </thead>

                <tbody>
                    @foreach($clientsPrepago as $client)
                    <tr>
                        
                        <td>{{ json_decode($client->settings)->name }}</td>
                        <td>{{ $client->email }}</td>
                        <td>{{ $client->created_at }}</td>

                        <td align="center">{!! $client->account_type_badge !!}</td>
                        <td align="center">{!! $client->phase !!}</td>
                        <td align="center">{{ $client->enabled == 'active' ? "Activo": "Suspendido" }}</td>
                        <td align="center">
                            <div class="dropdown">
                                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Acciones
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    @if ($client->phase == "Testing")
                                    <a class="dropdown-item" data-type="actions-dashboard" data-href="{{ URL::to('dashboard/form-phase-piloto/' . $client->company_id)}}" data-container=".action-modal">Pasar a pruebas piloto</a>
                                    <a class="dropdown-item" data-type="actions-dashboard" data-href="{{ URL::to('dashboard/form-phase-production/' . $client->company_id)}}" data-container=".action-modal">Pasar a producción</a>
                                    @elseif( $client->phase == 'Piloto testing')
                                    <a class="dropdown-item" data-type="actions-dashboard" data-href="{{ URL::to('dashboard/form-phase-production/' . $client->company_id)}}" data-container=".action-modal">Pasar a producción</a>

                                    @endif


                                    @if($client->is_postpago )
                                    @if($client->enabled == 'active')
                                    <a class="dropdown-item disabled" href="#">Suspender</a>
                                    @else
                                    <a class="dropdown-item disabled" data-type="data-action" data-onclick="enablePostpago( $client->company_id )">Habilitar</a>
                                    @endif
                                    @endif
                                    <a class="dropdown-item" data-type="actions-dashboard" data-href="{{ URL::to('dashboard/form-information/' . $client->company_id)}}" data-container=".action-modal">Información</a>
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

    <script>
        $(document).on('click', '[data-type=actions-dashboard]', function() {
            jQuery.get($(this).attr('data-href'), function(res) {
                $(".action-modal").html(res);
                $(".action-modal").modal("show");
            });
        });
        //On display of add contact modal
        $('.action-modal').on('show.bs.modal', function(e) {

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
                        $(form)
                            .find('button[type="submit"]')
                            .attr('disabled', true);
                        $.ajax({
                            method: 'POST',
                            url: $(form).attr('action'),
                            dataType: 'json',
                            data: data,
                            success: function(result) {
                                if (result.success == true) {
                                    $('div.action-modal').modal('hide');
                                    // alert("Se paso a pruebas piloto exitosamente");
                                    location.reload();
                                } else {
                                    alert(result.msg);
                                }
                            },
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

</body>

</html>