@extends('layouts.app')

@section('title', 'Inventory Management')

@section('content')
<div class="page-header">
    <div class="header-content">
        <h2>Inventory & Parts</h2>
        <p class="text-muted">Manage stock and suppliers</p>
    </div>
    <button class="btn-primary">Add New Part</button>
</div>

<div class="card glass">
    <div class="placeholder-content">
        <p>Inventory management module content goes here.</p>
        <p class="text-muted">Tracking screens, batteries, keyboards, etc.</p>
    </div>
</div>

<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    .placeholder-content {
        padding: 3rem;
        text-align: center;
    }
</style>
@endsection
