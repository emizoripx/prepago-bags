<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Clientes</title>
</head>
<body>
    <div class="container">
        <h3>Clientes</h3>
        <table class="table table-striped table-bordered" style="table-layout: fixed;">
            <thead>
                <th width="50%">Compañia</th>
                <th width="10%">Producción</th>
                <th width="10%">Tipo de Cuenta</th>
                <th width="10%">Estado</th>
                <th width="20%">Acciones</th>
            </thead>
            <tbody>
                @foreach($clientsPrepago as $client)
                    <tr>
                        <td>{{ json_decode($client->settings)->name }}</td>
                        <td align="center">{{ $client->production }}</td>
                        <td align="center">{{ $client->is_postpago }}</td>
                        <td align="center">{{ $client->enabled }}</td>
                        <td align="center">
                            <button>Dar alta</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>   
    </div>
</body>
</html>