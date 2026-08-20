<x-prepagobags::modal formAction="update">
    <x-slot name="title">
       Pasar a pruebas piloto
    </x-slot>

    <x-slot name="content">
       <div class="lg:col-span-4">
            <div class="grid gap-4 gap-y-2 text-sm grid-cols-1 md:grid-cols-5">
                <div class="form-group ">
                    <div class="form-group">
                        Razón Social : {!! $company->settings->name ?? $company->company_name ?? '' !!} <br />
                        NIT : {!! $company->settings->id_number ?? $company->company_id_number ?? '' !!} <br />
                    </div>
                </div>
                <div class="md:col-span-5">
                    <p class="text-sm text-gray-600 mt-2">La empresa se registrará automáticamente como piloto usando la API de Partner.</p>
                </div>
            </div>
        </div>
    </x-slot>

    <x-slot name="buttons">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success">Ejecutar</button>
    </x-slot>
</x-prepagobags::modal>