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

    <div class="invoice-terms" style="margin-top: 3rem; font-size: 0.85rem; color: #4b5563;">
        <h4 style="border-bottom: 2px solid #e5e7eb; padding-bottom: 0.5rem; margin-bottom: 1rem;">TERMS AND CONDITIONS</h4>
        <ul style="padding-left: 1.2rem; line-height: 1.5;">
            <li>The prices quoted in our estimates are not binding. The final cost of repair will be based on the actual cost of services and the materials.</li>
            <li>SVP Technologies will not be responsible for any losses or damages due to contingencies beyond our control and will not be responsible for any DATA losses.</li>
            <li>If the customer wishes not to carry out the repair as per the estimate, a fee of Rs.1,000.00 (For Laptop) and Rs. 500.00 (For Desktop) to be paid by the customer as inspection charges when collecting the equipment.</li>
            <li>SVP Technologies shall not be responsible for the item/s not collected within 30 days after informing the customer.</li>
            <li>Equipment will be returned on presentation of this original invoice and purchasing bill, Otherwise claim is not allowed.</li>
        </ul>
    </div>

    <div class="acknowledgement" style="margin-top: 4rem; page-break-inside: avoid;">
        <h4 style="border-bottom: 2px solid #e5e7eb; padding-bottom: 0.5rem; margin-bottom: 2rem;">CUSTOMER ACKNOWLEDGEMENT</h4>
        <p style="font-size: 0.9rem; margin-bottom: 3rem;">I hereby certify by my signature below that I have read the terms and conditions of SVP Technologies and I agree to the terms and condition stated herein above.</p>
        
        <div style="display: flex; justify-content: space-between; margin-top: 4rem;">
            <div style="text-align: center; width: 40%;">
                <div style="border-top: 1px solid #1f2937; padding-top: 0.5rem;">Customer's Signature</div>
            </div>
            <div style="text-align: center; width: 40%;">
                <div style="border-top: 1px solid #1f2937; padding-top: 0.5rem;">Authorized Signature</div>
            </div>
        </div>
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
        width: 210mm;
        min-height: 297mm;
        padding: 20mm;
        margin: 0 auto;
        background: #fff;
        color: #1f2937;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    
    .glass.print\:no-glass {
        backdrop-filter: none;
        border: none;
        box-shadow: none;
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

    @media print {
        @page { size: A4; margin: 0; }
        body { margin: 0; padding: 0; background: #fff; }
        body * { visibility: hidden; }
        .invoice-container, .invoice-container * { visibility: visible; }
        .invoice-container { 
            position: absolute; 
            left: 0; 
            top: 0; 
            width: 210mm; 
            height: 297mm; 
            margin: 0; 
            padding: 20mm; 
            box-shadow: none;
        }
        .print\:hidden { display: none !important; }
        .glass { background: #fff !important; color: #000 !important; }
    }
</style>
@endsection
