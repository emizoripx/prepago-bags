<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
    integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css" integrity="sha512-ARJR74swou2y0Q2V9k0GbzQ/5vJ2RBSoCWokg4zkfM29Fb3vZEQyv0iWBMW/yvKgyHSR/7D64pFMmU8nYmbRkg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.9.0/jquery.validate.min.js"></script>
    <script src="https://kit.fontawesome.com/81ac794241.js" crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js" integrity="sha512-yDlE7vpGDP7o2eftkCiPZ+yuUyEcaBwoJoIhdXv71KZWugFqEphIS3PU60lEkFaz8RxaVsMpSvQxMBaKVwA5xg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <title>Dashboard</title>

    <style>
        .nav .nav-item .nav-link {
            color: #908C8C;
        }

        .nav .nav-item .nav-link:hover {
            color: black;
        }

        .active {
            color: black !important ;
        }
        .nav .nav-item .nav-link:focus {
            color: black;
        }
        

    </style>


</head>

<body>
    <div class="m-auto col-10 ">
        <ul class="nav my-5 col-md-12">
            <li class="nav-item">
                <a id="clients" class="nav-link" href="{{ route('dashboard.getClients') }}">ADMINISTRACIÓN DE CLIENTES</a>
            </li>
            <li class="nav-item">
                <a id="plans" class="nav-link" href="{{ route('postpago.index') }}">PLANES POSTPAGO</a>
            </li>
        </ul>

        <main>
            @yield('content')
        </main>
    </div>

</body>

<script>
    var path = window.location.pathname;

    if (path.substring(11) == 'clients') {
        $( "#clients" ).last().addClass( "active" );
    }
    if (path.substring(11) == 'postpago-plans') {
        $( "#plans" ).last().addClass( "active" );
    }

</script>

</html>
