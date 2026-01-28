@extends('layouts.app')

@section('title', 'Payment - ' . ($course->name ?? 'Course'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Payment for {{ $course->name ?? '-' }}</div>
                <div class="card-body">
                    <h5>Order Details</h5>
                    <ul>
                        <li><strong>Course:</strong> {{ $course->name ?? '-' }}</li>
                        <li><strong>Category:</strong> {{ $course->category->name ?? '-' }}</li>
                        <li><strong>Price:</strong> Rp{{ number_format($course->price ?? 0, 0, ',', '.') }}</li>
                        <li><strong>Duration:</strong> {{ $course->duration ?? '-' }} minutes</li>
                    </ul>
                    <hr>
                    <form>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" placeholder="Enter your email">
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" placeholder="Enter your full name">
                        </div>
                        <div class="mb-3">
                            <label for="whatsapp" class="form-label">WhatsApp Number</label>
                            <input type="text" class="form-control" id="whatsapp" placeholder="Enter your WhatsApp number">
                        </div>
                        <button type="submit" class="btn btn-warning w-100">Pay Now</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
