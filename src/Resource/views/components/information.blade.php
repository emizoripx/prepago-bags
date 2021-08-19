 <!-- Modal -->

 <div class="modal-dialog modal-lg" role="document">
     
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title">Información de la compañia</h5>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </button>
             </div>
             <div class="modal-body">
                 <b>Compañia:</b> {!! json_decode($company->settings)->name !!} <br>
                 <b>Cantidad Sucursales: </b> {!! $branches_number !!} <br>
                 <b>Dueño:</b> {!! $company->email !!} <br>
                 <b>Fase: </b> {!! $company->phase !!} <br>
                 <b>Tipos de documento sector: </b> <br>


                 <table class="table table-bordered">
                     <thead>
                         <th>ID</th>
                         <th>Nombre</th>
                         <th>Fact. disponible</th>
                         <th>Fact. emitidas</th>
                         <th>fecha vencimiento</th>
                         <th>Limite Postpago</th>
                         <th>Excedente</th>
                     </thead>
                     <tbody>
                     @foreach ($document_sectors as $ds)
                            <tr>
                                <td>{!! $ds->fel_doc_sector_id !!}</td>
                                <td>{!! $arrayNames[$ds->fel_doc_sector_id] !!}</td>
                                <td>{!! $ds->invoice_number_available !!}</td>
                                <td>{!! $ds->counter !!}</td>
                                <td>{!! $ds->duedate !!}</td>
                                <td>{!! $ds->postpago_limit !!}</td>
                                <td>{!! $ds->postpago_exceded_limit !!}</td>
                            </tr>
                            @endforeach
                        </tbody>
                 </table>
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-success" data-dismiss="modal">Aceptar</button>
             </div>
     </div>
 </div>