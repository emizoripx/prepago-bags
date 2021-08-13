<style>
    label{
        font-size: 10px;
    }
</style>
<!-- Modal -->
<div class="modal fade" id="createFormModal" tabindex="-1" aria-labelledby="createFormModalLabel" aria-hidden="false">
    <div class="modal-dialog  modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Agregar Plan Postpago</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="container">
              <form action="">
                  <div class="row">
                    <div class="form-group  col-md-12">
                        <label for="name">Nombre</label>
                        <input id="name" class="col-md-12" type="text">
                    </div>
                  </div>
                  <div class="row">
                        <div class="form-group col-md-3">
                            <label for="name">#Facturas</label>
                            <input class="col-md-12" id="num_invoices" type="text">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="name">#Clientes</label>
                            <input class="col-md-12" id="num_clients" type="text">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="name">#Productos</label>
                            <input class="col-md-12" id="num_products" type="text">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="name">#Usuarios</label>
                            <input class="col-md-12" id="num_users" type="text">
                        </div>
                  </div>
                  <div class="row">
                        <div class="form-group col-md-3">
                            <label for="name">Prorateo Factura</label>
                            <input class="col-md-12" id="prorated_invoice" type="text">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="name">Prorateo Cliente</label>
                            <input class="col-md-12" id="prorated_clients" type="text">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="name">Prorateo Producto</label>
                            <input class="col-md-12" id="prorated_products" type="text">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="name">Prorateo Usuario</label>
                            <input class="col-md-12" id="prorated_users" type="text">
                        </div>
                  </div>
                  <div class="row">
                        <div class="form-group col-md-6">
                            <label for="name">Frecuencia</label>
                            <input class="col-md-12" id="frequency" type="text">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="name">Costo</label>
                            <input class="col-md-12" id="price" type="text">
                        </div>
                        
                  </div>
                  <div class="row">
                        <div class="form-group col-md-3">
                            <label for="name">Todos los Documentos Sector</label>
                            <input class="col-md-12" id="all_sector_docs" type="text">
                        </div>
                        <div class="col-md-3">

                        </div>
                        <div class="form-group col-md-6">
                            <label for="name">Document Sector</label>
                            <input class="col-md-12" id="sector_doc_id" type="text">
                        </div>
                        
                  </div>
                  <div class="row">
                        <div class="form-group col-md-3">
                            <label for="name">Todos los Documentos Sector</label>
                            <input class="col-md-12" id="enable_overflow" type="text">
                        </div>
                        
                  </div>
              </form>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save changes</button>
        </div>
      </div>
    </div>
  </div>