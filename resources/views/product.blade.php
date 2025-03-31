@extends('layouts.app')
@section('title', $product->name)
@section('content')
    <livewire:product-redesign wire:key="product-main-{{ $product->id }}" :product="$product" />
@endsection
