<!-- Modal -->
<div class="modal-dialog modal-lg bg-primary">

    <form action="{!! URL::to('dashboard/company-settings/' . $company->id) !!}" method="post" id="actionFormCreate">
        @method('put')
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Configuración</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body modal-information">


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


                <div class="row col-md-12">
                    <div>
                        Nivel de numeración de facturación
                    </div>
                    <div>
                        <select class="col-md-12 form-group" name="level_invoice_number_generation" id="level_invoice_number_generation" value=" {{ $company->level_invoice_number_generation }}">
                            <option value="0" @if($company->level_invoice_number_generation == 0) selected @endif >Deshabilitado</option>
                            <option value="1" @if($company->level_invoice_number_generation == 1) selected @endif >Por Sucursal</option>
                            <option value="2" @if($company->level_invoice_number_generation == 2) selected @endif >Por Sucursal y punto de venta</option>
                            <option value="3" @if($company->level_invoice_number_generation == 3) selected @endif >Por Sucursal, punto de venta y tipo documento</option>
                        </select>
                    </div>
                </div>
                <div class="row col-md-12">
                    <div>
                        Numeración de facturación habilitada
                    </div>
                    <div style=" overflow-y: scroll; height: 10cm;">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                
                                    <th>Sucursal</th>
                                    <th>POS</th>
                                    <th>Documento Sector</th>
                                    <th>Numeración</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoice_generators as $ig)
                                @php
                                $splited = explode("-",$ig->code);
                                $counter = 3 ;
                                @endphp
                                <tr>
                                    
                                    @foreach($splited as $sp)
                                    @if($counter == 1)
                                    <td>{{ $sector_documents[$sp]}}</td>
                                    @else
                                    <td>{{$sp}}</td>
                                    @endif
                                    @php $counter--; @endphp
                                    @endforeach

                                    @for($i=1; $i <= $counter ;$i++) <td> - </td>
                                        @endfor

                                        <td> <input name="invoice_generators[{{$ig->code}}]" value="{{$ig->number_counter}}"> </td>
                                </tr>
                                @endforeach
                            </tbody>

                        </table>

                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-success">Guardar</button>
            </div>
        </div>
    </form>
</div>