@extends('layouts.admin')

@section('title', 'Manage Events')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add Event
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($events->count() > 0)
        <div class="row g-4">
            @foreach($events as $event)
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    @if($event->image)
                        <img src="{{ Storage::url($event->image) }}" class="card-img-top" alt="{{ $event->title }}" style="height:200px;object-fit:cover;">
                    @else
                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height:200px;">
                            <span>No Image</span>
                        </div>
                    @endif
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $event->title }}</h5>
                        <p class="card-text mb-1"><strong>Speaker:</strong> {{ $event->speaker }}</p>
                        <p class="card-text mb-1"><strong>Date:</strong> {{ $event->event_date }} <strong>Time:</strong> {{ $event->event_time }}</p>
                        <p class="card-text mb-1"><strong>Location:</strong> {{ $event->location }}</p>
                        <p class="card-text mb-2"><strong>Price:</strong> Rp{{ number_format($event->price, 0, ',', '.') }}</p>
                        <p class="card-text text-muted event-description-preview" style="font-size: 0.95em;">{{ Str::limit(strip_tags($event->description), 80) }}</p>
                        <div class="mt-auto d-flex justify-content-between">
                            <a href="{{ route('admin.events.show', $event) }}" class="btn btn-outline-info btn-sm">View</a>
                            <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-outline-warning btn-sm">Edit</a>
                           <button type="button" class="btn btn-outline-danger btn-sm"
    onclick="showDeleteModal('{{ $event->title }}', '{{ route('admin.events.destroy', $event) }}', '{{ $event->image ? Storage::url($event->image) : '' }}')">
    Delete
</button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4">
            {{ $events->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <h5 class="mb-3">Belum ada event.</h5>
            <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Tambah Event
            </a>
        </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteEventModal" tabindex="-1" aria-labelledby="deleteEventModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="deleteEventModalLabel">Konfirmasi Hapus Event</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img id="modalEventImage" src="#" alt="Event Image" style="max-width:100%;max-height:180px;border-radius:8px;display:none;margin-bottom:15px;">
        <p class="mb-0">Apakah Anda yakin ingin menghapus event <strong id="eventTitle"></strong>?</p>
        <small class="text-danger">Tindakan ini tidak dapat dibatalkan.</small>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <form id="deleteEventForm" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Hapus</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
    // Modal elements
    const deleteEventModal = document.getElementById('deleteEventModal');
    const eventTitleSpan = document.getElementById('eventTitle');
    const deleteEventForm = document.getElementById('deleteEventForm');
    const modalEventImage = document.getElementById('modalEventImage');

    window.showDeleteModal = function(eventTitle, deleteUrl, imageUrl) {
        eventTitleSpan.textContent = eventTitle;
        deleteEventForm.action = deleteUrl;
        if (imageUrl) {
            modalEventImage.src = imageUrl;
            modalEventImage.style.display = 'block';
        } else {
            modalEventImage.style.display = 'none';
        }
        const modal = new bootstrap.Modal(deleteEventModal);
        modal.show();
    }
</script>
@endsection

@section('styles')
<style>
/* Event Description Preview Styling */
.event-description-preview {
    line-height: 1.4;
    color: #6c757d;
}

.event-description-preview strong {
    font-weight: 600;
    color: #495057;
}

.event-description-preview em {
    font-style: italic;
    color: #6c757d;
}
</style>
@endsection