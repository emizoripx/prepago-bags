<!-- Modal -->
{{-- <div class="modal fade" id="editFormModal" tabindex="-1" aria-labelledby="editFormModalLabel" aria-hidden="false"> --}}
<div class="modal-dialog  modal-dialog-centered modal-lg">
    <form action="{!! URL::to('dashboard/postpago-plans/' . $plan->id) !!}" method="post" id="actionFormCreate">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Agregar Plan Postpago</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    @method('put')
                    @csrf
                    <div class="row">
                        <div class="form-group  col-md-12">
                            <label for="name">Nombre</label>
                            <input id="name" name="name" class="col-md-12 form-control" type="text"
                                value="{{ $plan->name }}">
                            @error('name')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row d-flex justify-content-between">
                        <div class="form-group col-md-2">
                            <label for="name">#Facturas</label>
                            <input class="col-md-12 form-control" id="num_invoices" name="num_invoices" type="number"
                                value="{{ $plan->num_invoices }}">
                            @error('num_invoices')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-2">
                            <label for="name">#Clientes</label>
                            <input class="col-md-12 form-control" id="num_clients" name="num_clients" type="number"
                                value="{{ $plan->num_clients }}">
                            @error('num_clients')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-2">
                            <label for="name">#Productos</label>
                            <input class="col-md-12 form-control" id="num_products" name="num_products" type="number"
                                value="{{ $plan->num_products }}">
                            @error('num_products')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror

                        </div>
                        <div class="form-group col-md-2">
                            <label for="num_branches">#Sucursales</label>
                            <input class="col-md-12 form-control" id="num_branches" name="num_branches" type="number"
                                value="{{ $plan->num_branches }}">
                            @error('num_branches')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror

                        </div>
                        <div class="form-group col-md-2">
                            <label for="name">#Usuarios</label>
                            <input class="col-md-12 form-control" id="num_users" name="num_users" type="number"
                                value="{{ $plan->num_users }}">
                            @error('num_users')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row d-flex justify-content-between">
                        <div class="form-group col-md-2">
                            <label for="name">Prorateo Factura</label>
                            <div class="input-group input-group-sm mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-sm">$</span>
                                </div>
                                <input class="col-md-12 form-control" id="prorated_invoice" name="prorated_invoice"
                                    type="text" value="{{ $plan->prorated_invoice }}">
                            </div>
                            @error('prorated_invoice')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-2">
                            <label for="name">Prorateo Cliente</label>
                            <div class="input-group input-group-sm mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-sm">$</span>
                                </div>
                                <input class="col-md-12 form-control" id="prorated_clients" name="prorated_clients"
                                    type="text" value="{{ $plan->prorated_clients }}">
                            </div>
                            @error('prorated_clients')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-2">
                            <label for="name">Prorateo Producto</label>
                            <div class="input-group input-group-sm mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-sm">$</span>
                                </div>
                                <input class="col-md-12 form-control" id="prorated_products" name="prorated_products"
                                    type="text" value="{{ $plan->prorated_products }}">
                            </div>
                            @error('prorated_products')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-2">
                            <label for="prorated_branches">Prorateo Sucursales</label>
                            <div class="input-group input-group-sm mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-sm">$</span>
                                </div>
                                <input class="col-md-12 form-control" id="prorated_branches" name="prorated_branches"
                                    type="number" value="{{ $plan->prorated_branches }}">
                            </div>
                            @error('prorated_branches')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror

                        </div>
                        <div class="form-group col-md-2">
                            <label for="name">Prorateo Usuario</label>
                            <div class="input-group input-group-sm mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-sm">$</span>
                                </div>
                                <input class="col-md-12 form-control" id="prorated_users" name="prorated_users"
                                    type="text" value="{{ $plan->prorated_users }}">
                            </div>
                            @error('prorated_users')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="name">Frecuencia</label>
                            <select class="col-md-12 form-control" id="frequency" name="frequency" value>
                                <option value="1" @if ($plan->frequency == 1) selected @endif>1 Mes</option>
                                <option value="2" @if ($plan->frequency == 2) selected @endif>2 Meses</option>
                                <option value="3" @if ($plan->frequency == 3) selected @endif>3 Meses</option>
                                <option value="4" @if ($plan->frequency == 4) selected @endif>4 Meses</option>
                                <option value="5" @if ($plan->frequency == 5) selected @endif>5 Meses</option>
                                <option value="6" @if ($plan->frequency == 6) selected @endif>6 Meses</option>
                                <option value="7" @if ($plan->frequency == 7) selected @endif>7 Meses</option>
                                <option value="8" @if ($plan->frequency == 8) selected @endif>8 Meses</option>
                                <option value="9" @if ($plan->frequency == 9) selected @endif>9 Meses</option>
                                <option value="10" @if ($plan->frequency == 10) selected @endif>10 Meses</option>
                                <option value="11" @if ($plan->frequency == 11) selected @endif>11 Meses</option>
                                <option value="12" @if ($plan->frequency == 12) selected @endif>12 Meses</option>
                            </select>
                            @error('frequency')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="price">Costo</label>
                            <input class="col-md-12 form-control" id="price" name="price" type="number"
                                value="{{ $plan->price }}">
                            @error('price')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="all_sector_docs">Todos los Documentos Sector</label>
                            {{-- <input class="col-md-12" id="all_sector_docs" type="text"> --}}
                            <div class="d-flex">
                                <span class="switch-span col-md-6" id="label_all_sector_docs">
                                @if ($plan->enable_overflow == 1) Activado @else
                                        Desactivado @endif
                                </span> &nbsp;

                                <div class="switch d-flex align-items-end col-md-6">
                                    <input id="all_sector_docs" name="all_sector_docs"
                                        class="cmn-toggle cmn-toggle-round " type="checkbox" @if ($plan->all_sector_docs) checked value="1" @endif>
                                    <label for="all_sector_docs"></label>
                                </div>
                            </div>
                            @error('all_sector_docs')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">

                        </div>
                        <div class="form-group col-md-6">
                            <label for="name">Documento Sector</label>
                            <Select class="col-md-12 form-control" id="sector_doc_id" name="sector_doc_id" @if ($plan->all_sector_docs == 1) disabled @endif >
                                @foreach ($document_sectors as $key => $value)
                                    <option value="{{ $key }}" @if ($key == $plan->sector_doc_id) selected @endif>
                                        {{ $value }}</option>
                                @endforeach
                            </Select>
                            @error('sector_doc_id')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="enable_overflow">Prorateo Habilitado</label>
                            {{-- <input class="col-md-12" id="enable_overflow" type="text"> --}}

                            <div class="d-flex">
                                <span class="switch-span col-md-6" id="label_enable_overflow">
                                @if ($plan->enable_overflow == 1) Activado @else
                                        Desactivado @endif
                                </span> &nbsp;
                                <div class="switch d-flex align-items-end col-md-6">
                                    <input id="enable_overflow" name="enable_overflow"
                                        class="cmn-toggle cmn-toggle-round" type="checkbox" @if ($plan->enable_overflow) checked value="1" @endif>
                                    <label for="enable_overflow"></label>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Ejecutar</button>
            </div>
        </div>
    </form>
</div>
<script>
    $(document).ready(function() {


        $('#enable_overflow').click(function() {
            var enable_overflow = $('#enable_overflow').val();

            console.log("Checked");
            console.log($('#enable_overflow').prop("checked"));
            if (enable_overflow == 'on' || $('#enable_overflow').prop("checked")) {
                $('#enable_overflow').val(1);
                $('#label_enable_overflow').html('Activado');

            } else {
                $('#enable_overflow').val(0);
                $('#label_enable_overflow').html('Desactivado');
            }
        });

        $('#all_sector_docs').click(function() {
            var all_sector_docs = $('#all_sector_docs').val();
            console.log(all_sector_docs);
            if (all_sector_docs == 'on' || $('#all_sector_docs').prop("checked")) {
                $('#all_sector_docs').val(1);


                $('#label_all_sector_docs').html('Activado');
                $('#sector_doc_id').prop("disabled", true);
                $('#sector_doc_id').val(0);
            } else {
                $('#all_sector_docs').val(0);

                $('#label_all_sector_docs').html('Desactivado');
                $('#sector_doc_id').prop("disabled", false);
                $('#sector_doc_id').val(1);
            }
        });
    });
</script>
{{-- </div> --}}
