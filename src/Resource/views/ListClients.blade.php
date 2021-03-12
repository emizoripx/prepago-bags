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
                <th width="60%">Email</th>
                <th width="20%">Tipo de Cuenta</th>
                <th width="20%">Acciones</th>
            </thead>
            <tbody>
                @foreach($clientsPrepago as $client)
                    <tr>
                        <td>{{ $client->email }}</td>
                        <td>{{ $client->production }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>   
    </div>
</body>
</html>