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
</head>

<body>
    <div class="container">
        <h3>Clientes de la plataforma</h3>
        <div >
            <table class="table table-striped table-bordered" style="table-layout: fixed;">
                <thead>
                    <th width="5%">#</th>
                    <th width="10%">Compañia</th>
                    <th width="20%">Dueño</th>
                    <th width="15%">Cant disponible facturas</th>
                    <th width="10%">Producción</th>
                    <th width="10%">Tipo de Cuenta</th>
                    <th width="10%">Fase</th>
                    <th width="10%">Estado</th>
                    <th width="10%">Acciones</th>
                </thead>
                <tbody>
                    @foreach($clientsPrepago as $client)
                    <tr>
                        <td>{{ $client->id }}</td>
                        <td>{{ json_decode($client->settings)->name }}</td>
                        <td>{{ $client->email }}</td>
                        <td>{{ $client->invoice_number_available }}</td>

                        <td align="center">{{ $client->production ? "Sí" : "No" }}</td>
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
                                    <a class="dropdown-item disabled" data-type="data-action" data- onclick="enablePostpago( $client->company_id )">Habilitar</a>
                                    @endif
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>  
            {{ $clientsPrepago->links() }}


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
    </script>

</body>

</html>