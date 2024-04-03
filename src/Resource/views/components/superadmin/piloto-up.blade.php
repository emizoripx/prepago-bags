<x-modal formAction="update">
    <x-slot name="title">
       Pasar a pruebas piloto
    </x-slot>

    <x-slot name="content">
       <div class="lg:col-span-4">
            <div class="grid gap-4 gap-y-2 text-sm grid-cols-1 md:grid-cols-5">
                <div class="form-group ">
                    <div class="form-group">
                        Razón Social : {!! $company->company_name !!} <br />
                        NIT : {!! $company->company_id_number !!} <br />
                    </div>
                </div>
                <div class="md:col-span-5">
                    <label for="client_id" class="font-bold">Client ID</label>
                    <input type="text" wire:model="client_id" class="h-10 border mt-1 rounded px-4 w-full bg-gray-50" />
                    @error('client_id') <p class="text-red-700"> {{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-5">
                    <label for="client_secret" class="font-bold">Client Secret</label>
                    <input type="text" wire:model="client_secret" class="h-10 border mt-1 rounded px-4 w-full bg-gray-50" />
                    @error('client_secret') <p class="text-red-700"> {{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-5">
                    <label for="host" class="font-bold">Client Secret</label>
                    <input type="text" wire:model="host" class="h-10 border mt-1 rounded px-4 w-full bg-gray-50" />
                    @error('host') <p class="text-red-700"> {{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </x-slot>

    <x-slot name="buttons">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success">Ejecutar</button>
    </x-slot>
</x-modal>