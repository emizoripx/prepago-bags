<!-- Modal -->
{{-- <div class="modal fade" id="createFormModal" tabindex="-1" aria-labelledby="createFormModalLabel" aria-hidden="false"> --}}
<div class="modal-dialog modal-lg bg-primary">
    <form action="{!! URL::to('dashboard/production-up') !!}" method="post" id="actionFormLabel">
        {{-- @method('post') --}}
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Usuarios</h5>
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

                <div class="form-group  col-md-3">
                    <label for="name">Usuarios</label>
                </div>
                <div class="row col-md-12">
                    <div class="table-prepago-invoices">

                        <table class="table table-sm table-striped" border="1" style="max-width: 100%; margin-left: 3%;">
                            <thead>
                                <tr>
                                    <td>CORREO</td>
                                    <td>NOMBRE</td>
                                    <td>ÚLTIMO INICIO DE SESION</td>
                                    <td>CREADO EN</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>{!! $user->email !!}</td>
                                    <td>{!! $user->first_name .' '.$user->first_name !!}</td>
                                    <td>{!! $user->last_login !!}</td>
                                    <td>{!! $user->created_at !!}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </form>
</div>

{{-- </div> --}}