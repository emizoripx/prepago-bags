 <!-- Modal -->

 <div class="modal-dialog" role="document">
     <form action="{!! URL::to('dashboard/production-up')!!}" method="post" id="actionFormLabel">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title">Pasar a producción</h5>
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
                 <div class="form-check">
                     <input class="form-check-input" type="radio" name="account_type" id="exampleRadios1" value="0" checked>
                     <label class="form-check-label" for="exampleRadios1">
                         Prepago
                     </label>
                 </div>
                 <div class="form-check">
                     <input class="form-check-input" type="radio" name="account_type" id="exampleRadios2" value="1">
                     <label class="form-check-label" for="exampleRadios2">
                         Postpago
                     </label>
                 </div>

                 <div class="mt-4" name="docSector" id="docSector">
                    <div class="form-group">
                        <label for="client_id">Frecuencia</label>
                        <input type="number" class="form-control" id="frequency" name="frequency" placeholder="Frequencia en meses" autocomplete="off">
                    </div>
                     <table class="table" >
                         <thead>
                             <th>Documento Sector</th>
                             <th>Limite</th>
                         </thead>
                         <tbody>
                             @foreach ($document_sectors as $ds)
                                 <tr>
                                     <td>{!! $arrayNames[$ds->fel_doc_sector_id] !!}</td>
                                     <td class="form-group">
                                         <input class="form-control" type="number" id="postpago_limit[{{ $ds->fel_doc_sector_id }}]" name="postpago_limit[{{ $ds->fel_doc_sector_id }}]">
                                     </td>
                                 </tr>
                             @endforeach

                         </tbody>
                     </table>
                 </div>


             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                 <button type="submit" class="btn btn-success">Ejecutar</button>
             </div>
     </form>
 </div>
 </div>

 <script>
     $(document).ready(function(){
        $("#docSector").hide();

        $("#exampleRadios1").click(function (){
            console.log("Changed")
                $("#docSector").hide();
        });
        $("#exampleRadios2").click(function (){
            console.log("Changed")
                $("#docSector").show();
        });
    });
 </script>