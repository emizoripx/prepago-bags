@extends('prepagobags::layouts.dashboard')

@section('content')
    <style>
        .split {
            background-image: -webkit-gradient(linear, left top, right bottom, color-stop(49%, rgba(255, 255, 255, 0)), color-stop(50%, rgba(121, 121, 121, 0.452)), color-stop(51%, rgba(255, 255, 255, 0)));
            background-image: -webkit-linear-gradient(top left, rgba(255, 255, 255, 0) 49%, rgb(121, 121, 121, 0.452) 50%, rgba(255, 255, 255, 0) 51%);
            background-image: -o-linear-gradient(top left, rgba(255, 255, 255, 0) 49%, rgb(121, 121, 121, 0.452) 50%, rgba(255, 255, 255, 0) 51%);
            background-image: linear-gradient(to bottom right, rgba(255, 255, 255, 0) 49%, rgb(121, 121, 121, 0.452) 50%, rgba(255, 255, 255, 0) 51%);
        }

        td .content-top {
            -webkit-transform: translate(-65px, 5px);
            -ms-transform: translate(-65px, 5px);
            transform: translate(-65px, 5px);
        }

        td .content-bottom {
            -webkit-transform: translate(35px, 10px);
            -ms-transform: translate(35px, 10px);
            transform: translate(35px, 10px);
        }

        label {
            font-size: 10px;
        }

        .alert {
            font-size: 10px !important;
            padding: 2px;
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
                        {{-- <button type="button" class="btn form-control btn-success d-inline-block" data-toggle="modal"
                            data-target="#createFormModal"> Adicionar</button> --}}
                        <button type="button" class="btn form-control btn-success d-inline-block"
                            data-type="actions-dashboard" data-href="{{ URL::to('dashboard/form-create-plans') }}"
                            data-container=".action-modal"> Adicionar</button>
                    </div>
                </form>
                {{-- @include('prepagobags::components.createFormModal') --}}
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

                                <x-prepagobags::cell-split number="{{ $plan->num_invoices }}"
                                    prorated="{{ $plan->prorated_invoice }}" />

                                <x-prepagobags::cell-split number="{{ $plan->num_clients }}"
                                    prorated="{{ $plan->prorated_clients }}" />

                                <x-prepagobags::cell-split number="{{ $plan->num_products }}"
                                    prorated="{{ $plan->prorated_products }}" />

                                <x-prepagobags::cell-split number="{{ $plan->num_users }}"
                                    prorated="{{ $plan->prorated_users }}" />

                                <td align="center">
                                    <div>{{ $plan->num_branches == 0 ? 'Ilimitado' : $plan->num_branches }}</div>
                                </td>


                                <td align="center">
                                    <div class="">
                                        <button class="btn btn-light " type="button" id="dropdownMenuButton"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-start" aria-labelledby="dropdownMenuButton">

                                            <a class="dropdown-item " data-type="actions-dashboard"
                                                data-href="{{ URL::to('dashboard/form-edit-plans/' . $plan->id) }}"
                                                data-container=".action-modal">Editar</a>
                                            <a class="dropdown-item" data-type="data-action"
                                                data-onclick="">Publicar/Ocultar</a>
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
        <div class="modal fade action-modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    </div>
    <script>
        $(document).on('click', '[data-type=actions-dashboard]', function() {
            jQuery.get($(this).attr('data-href'), function(res) {
                $(".action-modal").html(res);
                $(".action-modal").modal("show");
            });
        });
        


        $('.action-modal').on('show.bs.modal', function(e) {

            var modal = $(this);

            $('form#actionFormCreate')
                .submit(function(e) {
                    e.preventDefault();
                })
                .validate({
                    rules: {

                    },
                    submitHandler: function(form) {
                        e.preventDefault();
                        console.log($('#enable_overflow').val());

                        var data = $(form).serialize();


                        $(form)
                            .find('button[type="submit"]')
                            .attr('disabled', true);
                        $.ajax({
                            method: 'POST',
                            url: $(form).attr('action'),
                            headers: {},
                            dataType: 'json',
                            data: data,
                            success: function(result) {
                                if (result.success == true) {
                                    $('div.action-modal').modal('hide');
                                    alert("Plan creado exitosamente");
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
                            }
                        });
                    },
                });
        });
    </script>
@endsection
