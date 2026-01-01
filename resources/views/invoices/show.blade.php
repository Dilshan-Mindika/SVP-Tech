@extends('layouts.app')

@section('title', 'Invoice #' . $invoice->id)

@section('content')
<div class="page-header print:hidden">
    <div class="header-content">
        <h2>Invoice #{{ $invoice->id }}</h2>
        <p class="text-muted">{{ ucfirst($invoice->invoice_type) }} Invoice</p>
    </div>
    <button onclick="window.print()" class="btn-primary">Print Invoice</button>
</div>

<div class="invoice-container glass print:no-glass">
    <!-- Invoice Header -->
    <div class="invoice-header">
        <div class="company-details">
            <h1>SVP Tech</h1>
            <p>123 Laptop Lane, Tech City</p>
            <p>support@svp.tech</p>
            <p>+94 77 123 4567</p>
        </div>
        <div class="invoice-meta">
            <p><strong>Date:</strong> {{ $invoice->created_at->format('M d, Y') }}</p>
            <p><strong>Due Date:</strong> {{ $invoice->created_at->addDays(7)->format('M d, Y') }}</p>
        </div>
    </div>

    <hr class="divider">

    <!-- Client Details -->
    <div class="client-details">
        <h3>Bill To:</h3>
        <p><strong>{{ $invoice->repairJob->customer->name }}</strong></p>
        <p>{{ $invoice->repairJob->customer->address ?? 'No Address Provided' }}</p>
        <p>{{ $invoice->repairJob->customer->phone }}</p>
        <p>{{ $invoice->repairJob->customer->email }}</p>
    </div>

    <!-- Job Details -->
    <div class="job-details">
        <h3>Job Reference: #{{ $invoice->repairJob->job_number }}</h3>
        <p><strong>Device:</strong> {{ $invoice->repairJob->laptop_brand }} {{ $invoice->repairJob->laptop_model }} (S/N: {{ $invoice->repairJob->serial_number }})</p>
        <p><strong>Fault:</strong> {{ $invoice->repairJob->fault_description }}</p>
    </div>

    <!-- Line Items -->
    <table class="invoice-table">
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Cost</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->repairJob->parts as $part)
            <tr>
                <td>{{ $part->part_name }} (x{{ $part->quantity_used }})</td>
                <td class="text-right">LKR {{ number_format($part->part_cost * $part->quantity_used, 2) }}</td>
            </tr>
            @endforeach
            <tr>
                <td>Service / Labor Charges</td>
                <td class="text-right">LKR {{ number_format($invoice->labor_cost, 2) }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td>Total Amount</td>
                <td class="text-right">LKR {{ number_format($invoice->total_amount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="invoice-footer">
        <p>Thank you for choosing SVP Tech!</p>
        <p class="small">Terms: Payment due within 7 days. Warranty covers replaced parts for 3 months.</p>
    </div>
</div>

<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    
    .invoice-container {
        padding: 4rem;
        background: #fff; /* White bg for invoice look even in dark mode app */
        color: #1f2937; /* Dark text for readability */
        border-radius: 0.5rem;
        max-width: 800px;
        margin: 0 auto;
    }
    
    .glass.print\:no-glass {
        /* Override glass effect for actual paper look */
        backdrop-filter: none;
        border: none;
    }

    .invoice-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
    }
    
    .company-details h1 { color: #6d28d9; margin-bottom: 0.5rem; }
    .company-details p { margin: 0.2rem 0; color: #4b5563; }
    
    .divider { border: 0; border-top: 1px solid #e5e7eb; margin: 2rem 0; }
    
    .client-details, .job-details { margin-bottom: 2rem; }
    .client-details h3, .job-details h3 { font-size: 1.1rem; color: #374151; margin-bottom: 0.5rem; }
    
    .invoice-table { width: 100%; border-collapse: collapse; margin: 2rem 0; }
    .invoice-table th { text-align: left; padding: 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; }
    .invoice-table td { padding: 1rem; border-bottom: 1px solid #e5e7eb; }
    .invoice-table .text-right { text-align: right; }
    
    .total-row { font-weight: 700; font-size: 1.2rem; background: #f3f4f6; }
    
    .invoice-footer { text-align: center; margin-top: 4rem; color: #6b7280; }
    .invoice-footer .small { font-size: 0.8rem; margin-top: 0.5rem; }

    @media print {
        body * { visibility: hidden; }
        .invoice-container, .invoice-container * { visibility: visible; }
        .invoice-container { position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 2rem; }
        .print\:hidden { display: none !important; }
        .glass { background: #fff !important; color: #000 !important; box-shadow: none !important; }
    }
</style>
@endsection
