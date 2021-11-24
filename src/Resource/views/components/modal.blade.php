 <!-- Modal -->

 <div class="modal-dialog" role="document">
     <form action="{!! URL::to('dashboard/pilot-up')!!}" method="post" id="actionFormLabel">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title">Pasar a pruebas piloto</h5>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </button>
             </div>
             <div class="modal-body">

                 @csrf
                 <div class="form-group ">
                     <div class="form-group">
                         Razón Social : {!! json_decode($company->settings)->name !!} <br />
                         NIT : {!! json_decode($company->settings)->id_number !!} <br />
                     </div>
                 </div>

                 <input type="hidden" name="company_id" value="{!! $company->id !!}">
                 <div class="form-group">
                     <label for="client_id">Client ID</label>
                     <input type="text" class="form-control" id="client_id" name="client_id" placeholder="Client ID" autocomplete="off">
                 </div>
                 <div class="form-group">
                     <label for="client_secret">Client Secret</label>
                     <input type="text" class="form-control" id="client_secret" name="client_secret" placeholder="Client Secret" autocomplete="off">
                 </div>
                 <div class="form-group">
                     <label for="client_secret">Host</label>
                     <input type="text" class="form-control" id="host" name="host" placeholder="Host" autocomplete="off" required>
                 </div>

             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                 <button type="submit" class="btn btn-success">Ejecutar</button>
             </div>
     </form>
 </div>
 </div>