@extends('prepagobags::layouts.app')
@section('meta_title',"Compañias")

@section('header')
    @if($errors->any())
        <div class="alert alert-failure mb-4">
            @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif
@endsection

@section('body')
  
    <div class="flex flex-col mt-4">
        
        <livewire:company-table /> 
        <livewire:prepagobags::superadmin.create-company-modal />
    </div>
@endsection
