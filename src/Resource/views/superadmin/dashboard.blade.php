@extends('prepagobags::layouts.app')
@section('meta_title', "Panel de control")

@section('header')

@endsection

@section('body')
    <div>
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Panel de control
        </h3>

        <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">
            <div class="bg-white overflow-hidden shadow rounded">
                <div class="px-4 py-5 sm:p-6">
                    <dl>
                        <dt class="text-sm leading-5 font-medium text-gray-500 truncate text-red">
                            Compañias suscritas
                        </dt>
                        <dd class="mt-1 text-3xl leading-9 font-semibold text-gray-900">
                            {{$enrolled_quantity}}
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow rounded">
                <div class="px-4 py-5 sm:p-6">
                    <dl>
                        <dt class="text-sm leading-5 font-medium text-gray-500 truncate">
                            Compañias probando
                        </dt>
                        <dd class="mt-1 text-3xl leading-9 font-semibold text-gray-900">
                            {{$trial_quantity}}
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow rounded">
                <div class="px-4 py-5 sm:p-6">
                    <dl>
                        <dt class="text-sm leading-5 font-medium text-gray-500 truncate">
                            Compañias con solicitud pendiente
                        </dt>
                        <dd class="mt-1 text-3xl leading-9 font-semibold text-gray-900">
                            {{$request_quantity}}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

  
@endsection
