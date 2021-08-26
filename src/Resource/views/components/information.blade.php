<!-- Modal -->
{{-- <div class="modal fade" id="createFormModal" tabindex="-1" aria-labelledby="createFormModalLabel" aria-hidden="false"> --}}
<div class="modal-dialog modal-lg bg-primary">
    <form action="{!! URL::to('dashboard/production-up') !!}" method="post" id="actionFormLabel">
        {{-- @method('post') --}}
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Información</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body modal-information">

                @csrf
                <div class="row col-md-12">
                    <div class="form-group  col-md-1">
                        <label for="name">Código</label>
                        <p>{{ $company->client_id ?? 'S/N' }}</p>
                    </div>
                    <div class="form-group  col-md-3">
                        <label for="name">Nombre</label>
                        <p>{!! json_decode($company->settings)->name !!}</p>
                    </div>
                    <div class="form-group  col-md-3">
                        <label for="name">Razón Social</label>
                        <p>{!! json_decode($company->settings)->name !!}</p>
                    </div>
                    <div class="form-group  col-md-3">
                        <label for="name">NIT</label>
                        <p>{!! json_decode($company->settings)->id_number !!}</p>
                    </div>
                    <div class="form-group  col-md-2">
                        <label for="name">Tipo Cuenta</label>
                        <p>{!! ($company->is_postpago == 1)? "POSTPAGO":"PREPAGO" !!}</p>
                    </div>
                </div>
                <hr>
                @if($company->is_postpago == 1)
                <div class="row col-md-12">
                    <div class="form-group  col-md-3">
                        <label for="name">Plan Postpago</label>
                        <p>{!! $post_pago_plan_companies->name !!}</p>
                    </div>
                    <div class="form-group  col-md-3">
                        <label for="name">Fecha de creación </label>
                        <p>{!! \Carbon\Carbon::parse($post_pago_plan_companies->created_at)->format("Y-m-d") !!}</p>
                    </div>
                    <div class="form-group  col-md-3">
                        <label for="name">Fecha inicio </label>
                        <p>{!! $post_pago_plan_companies->start_date !!}</p>
                    </div>
                    <div class="form-group  col-md-3">
                        <label for="name">Fecha fin </label>
                        <p>{!! \Carbon\Carbon::parse($post_pago_plan_companies->start_date)->addMonths($post_pago_plan_companies->frequency)->format("Y-m-d") !!}</p>
                    </div>
                </div>

                <div class="row col-md-12">
                    <div class="table-postpago-plan-company">

                        <table class="table table-sm table-striped" border="1">
                            <thead>
                                <tr>
                                    <td></td>
                                    <td>#Facturas <p style="font-size:10px;">En base a frecuencia</p>
                                    </td>
                                    <td>#Clientes</td>
                                    <td>#Productos</td>
                                    <td>#Sucursales</td>
                                    <td>#Usuarios</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Plan de {!! $post_pago_plan_companies->frequency !!} mes(es) </td>
                                    <td>{!! $post_pago_plan_companies->num_invoices !!}</td>
                                    <td>{!! $post_pago_plan_companies->num_clients !!}</td>
                                    <td>{!! $post_pago_plan_companies->num_products !!}</td>
                                    <td>{!! $post_pago_plan_companies->num_branches !!}</td>
                                    <td>{!! $post_pago_plan_companies->num_users !!}</td>
                                </tr>
                                <tr>
                                    <td>Uso actual </td>
                                    <td>{!! $sum_all_documents !!}</td>
                                    <td>{!! $sum_all_clients !!}</td>
                                    <td>{!! $sum_all_products !!}</td>
                                    <td>{!! $sum_all_branches !!}</td>
                                    <td>{!! $sum_all_users !!}</td>

                                </tr>
                            </tbody>

                        </table>
                    </div>
                </div>
                <div class="row col-md-12">
                    <div class="form-group  col-md-6">
                        <label for="name">Facturas pendientes de pago</label>
                        <p>Son {!! collect($due_invoices)->count()!!} facturas pendientes de pago</p>
                    </div>
                </div>
                <div class="row col-md-12">
                    <div class="table-due-invoices">

                        <table class="table table-sm table-striped" border="1" style="max-width:50%;marginleft:15%;">
                            <thead>
                                <tr>
                                    <td>#</td>
                                    <td>FECHA DE EMISIÓN</td>
                                    <td>IMPORTE</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($due_invoices as $di)
                                <tr>
                                    <td>{!! $di->number !!}</td>
                                    <td>{!! $di->date !!}</td>
                                    <td>{!! "Bs ". round($di->balance,2) !!}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row col-md-12">
                    <div class="form-group  col-md-6">
                        <label for="name">Suspención de Servicio por Facturación Pendiente</label>
                        <p>{!! ($post_pago_plan_companies->due_invoice_suspend == 1)? "Activado" : "Inactivo"!!}</p>
                    </div>
                    @if ($post_pago_plan_companies->due_invoice_suspend == 1)
                    <div class="form-group  col-md-6">
                        <label for="name"># Facturas Pendientes para suspención </label>
                        <p>{!! $post_pago_plan_companies->due_invoice_limit!!}</p>
                    </div>
                    @endif
                </div>

                @else

                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Ejecutar</button>
            </div>
        </div>
    </form>
</div>

{{-- </div> --}}