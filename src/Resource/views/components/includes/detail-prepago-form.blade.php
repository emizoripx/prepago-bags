{{-- @props(['document_sectors', 'arrayNames']) --}}
<div>
    <div class="row col-md-12">
        <div class="form-group col-md-6">
            <label for="name">Fecha de Vencimiento</label>
            <input class="col-md-12 form-control" id="duedate" name="duedate" type="date">
        </div>

    </div>
    <div class="row d-flex justify-content-center col-md-12">
        <div class="form-group">
            <table class="table table-striped table-sm">
                <thead align="center">
                    <th width="10%">#</th>
                    <th width="25%">NOMBRE DOCUMENTO</th>
                    <th width="10%">DISPONIBLES</th>
                </thead>
                <tbody>
                    @foreach ($document_sectors as $ds)
                        <tr>
                            <td align="center">{{ $ds->fel_doc_sector_id }}</td>
                            <td>{!! $arrayNames[$ds->fel_doc_sector_id] !!}</td>
                            <td class="form-group">
                                <input class="form-control" type="number" value="0"
                                    id="invoice_number_available[{{ $ds->fel_doc_sector_id }}]"
                                    name="invoice_number_available[{{ $ds->fel_doc_sector_id }}]">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>
