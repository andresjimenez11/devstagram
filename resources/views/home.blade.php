@extends('layouts.app')

@section('titulo')
    Descubre
@endsection

@section('contenido')
    <x-listar-post :posts="$posts" />
@endsection