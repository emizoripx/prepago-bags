<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <title>Clientes</title>
</head>

<body>
    <div class="container">
        @php
        $company = (object)["business_name" => "mi compania", "nit" => "2342342342342"];
        @endphp
        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Pasar a pruebas piloto</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            
                            <div class="form-group ">
                                <div class="form-group">
                                Razón Social : {{ $company->business_name }} <br />
                                NIT : {{ $company->nit }} <br />
                                </div>

                            </div>
                            <div class="form-group">
                                <label for="client_id">Client ID</label>
                                <input type="text" class="form-control" id="client_id" placeholder="Client ID" required>
                            </div>
                            <div class="form-group">
                                <label for="client_secret">Client Secret</label>
                                <input type="text" class="form-control" id="client_secret" placeholder="Client Secret" required>
                            </div>
                                             
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-success">Ejecutar</button>
                    </div>
                </div>
            </div>
        </div>


        <h3>Clientes</h3>
        <table class="table table-striped table-bordered" style="table-layout: fixed;">
            <thead>
                <th width="50%">Compañia</th>
                <th width="10%">Producción</th>
                <th width="10%">Tipo de Cuenta</th>
                <th width="10%">Fase</th>
                <th width="10%">Estado</th>
                <th width="20%">Acciones</th>
            </thead>
            <tbody>
                @foreach($clientsPrepago as $client)
                <tr>
                    <td>{{ json_decode($client->settings)->name }}</td>
                    
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
                                @if($client->phase != "Production")
                                <a class="dropdown-item" data-toggle="modal" data-target="#exampleModal">Pasar a pruebas piloto</a>
                                @else
                                <a class="dropdown-item disabled">Pasar a pruebas piloto</a>
                                @endif
                                <a class="dropdown-item" href="#">Pasar a produccion</a>

                                @if($client->is_postpago )
                                @if($client->enabled == 'active')
                                <a class="dropdown-item disabled" href="#">Suspender</a>
                                @else
                                <a class="dropdown-item disabled" href="#">Habilitar</a>
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
</body>

</html>