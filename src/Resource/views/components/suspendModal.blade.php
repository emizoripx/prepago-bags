<div class="modal-dialog  modal-lg">
    <form action="{!! URL::to('dashboard/suspend-client') !!}" method="post" id="actionFormLabel">
        @method('post')
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Suspención</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    @csrf
                    <div class="row col-md-12">
                        
                        <div class="form-group">
                            <p style="margin-bottom: 0px;">Esta Seguro de Suspender la Cuenta De <strong> {!! json_decode($company->settings)->name !!} </strong> ?</p>
                            <label>Al suspender una cuenta el usuario no podrá emitir facturas</label>
                        </div>
                    </div>
                    <input type="hidden" name="company_id" value="{!! $company->id !!}">
                    

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success">Aceptar</button>
            </div>
        </div>
    </form>
</div>
