{{-- @props(['document_sectors', 'arrayNames']) --}}
<div>
    <div class="row col-md-12">
        <div class="form-group col-md-6">
            <label for="name">Fecha de Incio</label>
            <input class="col-md-12 form-control" id="start_date" name="start_date" type="date">
        </div>

    </div>
    <div class="row col-md-12">
        <div class="form-group col-md-6">
            <label for="name">Plan Postpago</label>
            <select class="form-control" name="plan_id" id="plan" required>
                <option value="">Seleccionar un Plan</option>
                @foreach($plans as $key => $value)
                    <option value="{{ $key }}"> {{ $value }} </option>
                @endforeach
            </select>
        </div>

    </div>
    <div class="row d-flex justify-content-center col-md-12">
        <div class="form-group">
            <table id="tbl_detalle" class="table table-striped table-sm">
                <thead align="center">
                    <th width="30%"> Costo </th>
                    <th width="20%">Frecuencia</th>
                    <th width="10%">#Facturas</th>
                    <th width="10%">#Clientes</th>
                    <th width="10%">#Productos</th>
                    <th width="10%">#Sucursales</th>
                    <th width="10%">#Usuarios</th>
                </thead>
                <tbody id="tbl_body">
                    
                </tbody>
            </table>
        </div>

    </div>
    <div class="row">
        <div class="form-group col-md-3">
            <label for="enable_overflow">Habilitación de Prorateo</label>
            {{-- <input class="col-md-12" id="enable_overflow" type="text"> --}}
            <div class="d-flex d-flex align-items-end ">
                <span class=" col-md-6 switch-span" id="label_enable_overflow"> Desactivado</span>
                &nbsp;
                <div class="switch col-md-6">
                    <input id="enable_overflow" name="enable_overflow"
                        class="cmn-toggle cmn-toggle-round form-control" type="checkbox" >
                    <label for="enable_overflow"></label>
                </div>
            </div>
        </div>

    </div>
</div>
