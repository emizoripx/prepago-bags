<div class="modal-dialog  modal-lg">
    <form action="{!! URL::to('dashboard/linked-client') !!}" method="post" id="actionFormLabel">
        @method('post')
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Vincular Cliente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    @csrf
                    <div class="row col-md-12">
                        <div class="form-group  col-md-3">
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
                    </div>
                    <input type="hidden" name="company_id" value="{!! $company->id !!}">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="client_id">Buscar Clientes Desvinculados</label>
                            <select class="form-control selectpicker" name="company_client_id" id="company_client_id"  data-live-search="true"  required>
                                <option value="" disabled selected>Selecciona un cliente</option>
                                @foreach($clients as $key => $value)
                                    <option data-tokens="{{ $value }}" value="{{ $key }}"> {{ $value }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success">Vincular</button>
            </div>
        </div>
    </form>
</div>
