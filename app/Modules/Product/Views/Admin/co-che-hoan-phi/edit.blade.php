@extends('Core::Admin.layouts.app')
@section('title', 'Sua co che hoan phi')
@section('page-title', 'Sua co che hoan phi')
@section('content')
<div class="card">
    <div class="card-header">
        <h5>Sua: {{ $coCheHoanPhi->name }}</h5>
        <a href="{{ route('admin.co-che-hoan-phi.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lai</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.co-che-hoan-phi.update', $coCheHoanPhi) }}">
            @csrf @method('PUT')
            @include('Product::Admin.co-che-hoan-phi.form', ['item' => $coCheHoanPhi])
            <button class="btn btn-primary"><i class="fas fa-save"></i> Cap nhat</button>
        </form>
    </div>
</div>
@endsection
