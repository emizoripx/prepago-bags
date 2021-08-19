<!-- Modal -->
{{-- <div class="modal fade" id="createFormModal" tabindex="-1" aria-labelledby="createFormModalLabel" aria-hidden="false"> --}}
<div class="modal-dialog modal-lg bg-primary">
    <form action="{!! URL::to('dashboard/production-up') !!}" method="post" id="actionFormLabel">
        {{-- @method('post') --}}
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pasar a producción</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- <div class=""> --}}
                @csrf
                <div class="row col-md-12">
                    <div class="form-group  col-md-3">
                        <label for="name">Código</label>
                        <p>S/N</p>
                        <input type="hidden" name="company_id" value="{!! $company->id !!}">
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
                <div class="row col-md-12">
                    <div class="form-group col-md-6">
                        <label for="name">Client ID</label>
                        <input class="col-md-12 form-control" id="client_id" name="client_id" type="text"
                            placeholder="Client ID" autocomplete="off">
                        @error('client_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="name">Client Secret</label>
                        <input class="col-md-12 form-control" id="client_secret" name="client_secret" type="text"
                            placeholder="Client Secret" autocomplete="off">
                        @error('client_secret')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
                <div class="row col-md-12">
                    <div class="form-group col-md-3">
                        <label for="name">Tipo de Cuenta</label>
                        {{-- <input class="col-md-12 form-control" id="prorated_invoice" name="prorated_invoice"
                                        type="text"> --}}
                        <div class="form-check btn-group btn-group-toggle" data-toggle="buttons">
                            <label id="prepago_check" class="btn btn-success" for="exampleRadios1">
                                <input class="form-check-input" type="radio" name="account_type" id="exampleRadios1"
                                    value="0" checked>
                                Prepago
                            </label>
                            <label id="postpago_check" class="btn btn-success" for="exampleRadios2">
                                <input class="form-check-input" type="radio" name="account_type" id="exampleRadios2"
                                    value="1">
                                Postpago
                            </label>
                        </div>
                    </div>

                </div>
                <div id="prepago_detail">
                    @include('prepagobags::components.includes.detail-prepago-form')
                </div>
                <div id="postpago_detail">
                    @include('prepagobags::components.includes.detail-postpago-form')
                </div>


                {{-- </div> --}}
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
        $("#prepago_detail").hide();
        $("#postpago_detail").hide();

        // Change form prepago
        $("#prepago_check").click(function() {
            console.log("Changed")
            $("#prepago_detail").show();
            $("#postpago_detail").hide();
        });

        // Change form postpago
        $("#postpago_check").click(function() {
            console.log("Changed")
            $("#postpago_detail").show();
            $("#prepago_detail").hide();

        });

        // Change label enable_overflow
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

        // Get postpago plan selected
        $('#plan').change(function () { 
            console.log('Change')
            $.ajax({
                type: "GET",
                url: "/dashboard/postpago-plans/" + $("#plan").val(),
                dataType: "JSON"
            }).done(function(response){
                var data = response.data;

                $(document).find('[id=tbl_body]').html('<tr> <td> Bs '+ data.price +'</td>'+
                                                            '<td>'+data.frequency +' Meses </td>'+
                                                            '<td>'+data.num_invoices +'</td>'+
                                                            '<td>'+data.num_clients +'</td>'+
                                                            '<td>'+data.num_products +'</td>'+
                                                            '<td>'+data.num_branches +'</td>'+
                                                            '<td>'+data.num_users +'</td>'
                                                            +' </tr>');
                $('#enable_overflow').val(data.enable_overflow);
                $('#enable_overflow').attr('checked', data.enable_overflow == 1 ? true : false);
                var label = data.enable_overflow == 1 ? 'Activado' : 'Desactivado';
                $('#label_enable_overflow').html(label);
            });
            
        });
    });


</script>
{{-- </div> --}}
