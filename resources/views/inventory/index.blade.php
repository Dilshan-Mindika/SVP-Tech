@extends('layouts.app')

@section('title', 'Manage Inventory')

@section('content')
<div class="page-header">
    <div class="header-content">
        <h2>Inventory & Parts</h2>
        <p class="text-muted">Manage stock levels and pricing</p>
    </div>
    <a href="{{ route('inventory.create') }}" class="btn-primary">
        <i class="fas fa-plus" style="margin-right: 0.5rem;"></i> New Part
    </a>
</div>

<div class="card glass">
    <div class="toolbar-container">
        <!-- Search Form -->
        <form action="{{ route('inventory.index') }}" method="GET" class="search-form">
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by part, brand, model..." class="search-input">
            </div>
        </form>
    </div>

    <div class="table-responsive">
    <table class="data-table">
        <thead>
            <tr>
                <th style="text-align: left;">Part Details</th>
                <th>Brand / Model</th>
                <th>Stock Level</th>
                <th>Pricing (LKR)</th>
                <th style="width: 120px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($parts as $part)
            <tr class="clickable-row" onclick="window.location='{{ route('inventory.edit', $part->id) }}'">
                <td style="text-align: left;">
                    <div class="table-text-main">{{ $part->name }}</div>
                    <div class="table-text-sub">{{ Str::limit($part->description, 30) }}</div>
                </td>
                <td>
                    <div style="font-size: 0.95rem;">{{ $part->brand ?? '-' }}</div>
                    <div class="table-text-sub">{{ $part->model ?? '-' }}</div>
                </td>
                <td>
                    @php
                        $statusClass = 'badge-success';
                        if($part->stock_quantity <= 0) $statusClass = 'badge-danger';
                        elseif($part->stock_quantity <= $part->low_stock_threshold) $statusClass = 'badge-warning';
                    @endphp
                    <span class="badge {{ $statusClass }}">
                        {{ $part->stock_quantity }} Units
                    </span>
                    @if($part->stock_quantity <= $part->low_stock_threshold)
                        <div style="font-size: 0.7rem; color: var(--danger); margin-top: 0.2rem;">Low Stock</div>
                    @endif
                </td>
                <td>
                    <div class="table-text-main">Sell: {{ number_format($part->selling_price, 2) }}</div>
                    <div class="table-text-sub">Cost: {{ number_format($part->cost_price, 2) }}</div>
                </td>
                <td onclick="event.stopPropagation()">
                    <a href="{{ route('inventory.edit', $part->id) }}" class="action-icon edit-icon" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('inventory.destroy', $part->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this part?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-icon delete-icon" title="Delete" style="border:none; background:transparent; padding:0; cursor:pointer;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted" style="padding: 3rem;">
                    <i class="fas fa-box-open" style="font-size: 2rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
                    No parts found matching your search.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

<style>
    .clickable-row { cursor: pointer; transition: background-color 0.15s ease; }
    .clickable-row:hover { background-color: rgba(255, 255, 255, 0.02); }
    [data-theme="light"] .clickable-row:hover { background-color: #f8fafc; }
</style>
@endsection
