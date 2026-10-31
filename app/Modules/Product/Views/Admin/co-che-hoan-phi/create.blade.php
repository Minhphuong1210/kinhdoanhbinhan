@extends('Core::Admin.layouts.app')
@section('title', 'Them co che hoan phi')
@section('page-title', 'Them co che hoan phi')
@section('content')
<div class="card">
    <div class="card-header">
        <h5>Them co che hoan phi</h5>
        <a href="{{ route('admin.co-che-hoan-phi.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lai</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.co-che-hoan-phi.store') }}">
            @csrf
            @include('Product::Admin.co-che-hoan-phi.form')
            <button class="btn btn-primary"><i class="fas fa-save"></i> Luu lai</button>
        </form>
    </div>
</div>
@endsection
