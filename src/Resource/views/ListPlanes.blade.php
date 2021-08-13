@extends('prepagobags::layouts.dashboard')

@section('content')
<style>
    .split{
        background-image: -webkit-gradient(linear, left top, right bottom, color-stop(49%, rgba(255, 255, 255, 0)), color-stop(50%, rgba(121, 121, 121, 0.452)), color-stop(51%, rgba(255, 255, 255, 0)));
        background-image: -webkit-linear-gradient(top left, rgba(255, 255, 255, 0) 49%, rgb(121, 121, 121, 0.452) 50%, rgba(255, 255, 255, 0) 51%);
        background-image: -o-linear-gradient(top left, rgba(255, 255, 255, 0) 49%, rgb(121, 121, 121, 0.452) 50%, rgba(255, 255, 255, 0) 51%);
        background-image: linear-gradient(to bottom right, rgba(255, 255, 255, 0) 49%, rgb(121, 121, 121, 0.452) 50%, rgba(255, 255, 255, 0) 51%);
    }
    td .content-top {
        -webkit-transform: translate(-65px,5px);
            -ms-transform: translate(-65px,5px);
                transform: translate(-65px,5px);
    }
    td .content-bottom {
        -webkit-transform: translate(35px,10px);
            -ms-transform: translate(35px,10px);
                transform: translate(35px,10px);
    }
</style>

    <div class="">
        <div class="" style="margin-top:50px;">
            <nav class="">
                <h3>Planes Postpago</h3>
                <form action="{{ url('dashboard/postpago-plans') }}">
                    <div class="form-group has-search d-inline-block col-md-8">
                        
                        <input type="text" class="form-control" placeholder="Buscar por nombre de plan" id="search"
                            name="search" value="{!! $search !!}" autocomplete="off" autofocus="on">
                    </div>
                    <div class="form-group d-inline-block">
                        <button type="submit" class="btn form-control btn-primary d-inline-block"> Filtrar</button>
                    </div>
                    <div class="form-group d-inline-block">
                        <button type="button" class="btn form-control btn-success d-inline-block" data-toggle="modal" data-target="#createFormModal" > Adicionar</button>
                    </div>
                </form>
                @include('prepagobags::components.createFormModal')
            </nav>

            <div class="table-style-custom col-md-12">
                <table class="table table-striped table-bordered" style="table-layout: fixed;">
                    <thead align="center">

                        <th width="10%">Nombre</th>
                        <th width="10%">Costo</th>
                        <th width="10%">Frecuencia</th>
                        <th width="15%">#Facturas</th>
                        <th width="15%">#Clientes</th>
                        <th width="15%">#Productos</th>
                        <th width="15%">#Usuarios</th>
                        <th width="10%">#Sucursales</th>
                        <th width="8%">Acciones</th>
                    </thead>
                    <tbody>
                        @foreach ($postpago_plans as $plan)
                            <tr>

                                <td>{{ $plan->name }}</td>
                                <td>Bs {{ $plan->price }}</td>
                                <td>{{ $plan->frequency }}</td>

                                <x-prepagobags::cell-split number="{{ $plan->num_invoices }}" prorated="{{ $plan->prorated_invoice }}"/>

                                <x-prepagobags::cell-split number="{{ $plan->num_clients }}" prorated="{{ $plan->prorated_clients }}"/>

                                <x-prepagobags::cell-split number="{{ $plan->num_products }}" prorated="{{ $plan->prorated_products }}"/>
                                    
                                <x-prepagobags::cell-split number="{{ $plan->num_users }}" prorated="{{ $plan->prorated_users }}"/>

                                <td align="center">
                                    <div>{{ $plan->num_branches == 0 ? 'Ilimitado' : $plan->num_branches }}</div>
                                </td>

                                
                                <td align="center">
                                    <div class="">
                                        <button class="btn btn-light " type="button"
                                            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-start" aria-labelledby="dropdownMenuButton">

                                            <a class="dropdown-item " href="#">Editar</a>
                                            <a class="dropdown-item" data-type="data-action"
                                                data-onclick="enablePostpago( $client->company_id )">Publicar/Ocultar</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $postpago_plans->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection
