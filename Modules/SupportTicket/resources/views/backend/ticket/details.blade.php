@extends('layouts/layoutMaster')

@section('title', __('Support Ticket Details'))

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/select2/select2.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/select2/select2.js'])
@endsection

@section('page-style')
    <style>
        .chat-container {
            height: 500px;
            overflow-y: auto;
        }

        .message-item {
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            padding: 1rem;
        }

        .user-message {
            background-color: #f0f8ff;
            margin-right: 2rem;
        }

        .admin-message {
            background-color: #f5f5f5;
            margin-left: 2rem;
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .attachment-preview {
            max-width: 300px;
            border-radius: 0.25rem;
            margin-top: 0.5rem;
        }

        .attachment-link {
            display: inline-block;
            margin-top: 0.5rem;
        }
    </style>
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Scroll chat to bottom on load
            const chatContainer = document.querySelector('.chat-container');
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }

            // Toggle ticket status
            document.querySelector('.toggle-ticket-status')?.addEventListener('click', function() {
                const ticketId = this.dataset.id;
                const currentStatus = this.dataset.status;
                const newStatus = currentStatus === 'open' ? 'close' : 'open';

                fetch(`/admin/support-ticket/change-status/${ticketId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            status: newStatus
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            toastr.success('Status updated successfully!');
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            toastr.error('Failed to update status');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        toastr.error('An error occurred');
                    });
            });

            // Preview file upload
            const attachmentInput = document.getElementById('attachment');
            const previewContainer = document.getElementById('preview-container');

            if (attachmentInput) {
                attachmentInput.addEventListener('change', function() {
                    previewContainer.innerHTML = '';

                    if (this.files && this.files[0]) {
                        const file = this.files[0];
                        const fileType = file.type.split('/')[0];

                        if (fileType === 'image') {
                            const img = document.createElement('img');
                            img.src = URL.createObjectURL(file);
                            img.className = 'img-fluid attachment-preview';
                            previewContainer.appendChild(img);
                        } else {
                            const fileInfo = document.createElement('div');
                            fileInfo.className = 'alert alert-info';
                            fileInfo.textContent =
                                `Selected file: ${file.name} (${(file.size / 1024).toFixed(2)} KB)`;
                            previewContainer.appendChild(fileInfo);
                        }
                    }
                });
            }
        });
    </script>
@endsection

@section('content')
    @if (session()->has('msg'))
        <div class="alert alert-{{ session('type') ?? 'success' }}">
            {{ session('msg') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <!-- Ticket Information -->
        <div class="col-xl-4 col-lg-5 col-md-5">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">{{ __('Ticket Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="info-container">
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <span class="fw-medium me-2">{{ __('Ticket ID:') }}</span>
                                <span>#{{ $ticket_details->id }}</span>
                            </li>
                            <li class="mb-3">
                                <span class="fw-medium me-2">{{ __('User:') }}</span>
                                <span>{{ $ticket_details->user?->first_name }}
                                    {{ $ticket_details->user?->last_name }}</span>
                            </li>
                            <li class="mb-3">
                                <span class="fw-medium me-2">{{ __('Department:') }}</span>
                                <span>
                                    @php
                                        $department = \Modules\SupportTicket\app\Models\Department::find(
                                            $ticket_details->department_id,
                                        );
                                    @endphp
                                    {{ $department ? $department->name : __('Not Found') }}
                                </span>
                            </li>
                            <li class="mb-3">
                                <span class="fw-medium me-2">{{ __('Status:') }}</span>
                                <span
                                    class="badge bg-label-{{ $ticket_details->status == 'open' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($ticket_details->status) }}
                                </span>
                            </li>
                            <li class="mb-3">
                                <span class="fw-medium me-2">{{ __('Priority:') }}</span>
                                <span
                                    class="badge bg-label-{{ $ticket_details->priority == 'high' ? 'danger' : ($ticket_details->priority == 'medium' ? 'warning' : 'success') }}">
                                    {{ ucfirst($ticket_details->priority) }}
                                </span>
                            </li>
                            <li class="mb-3">
                                <span class="fw-medium me-2">{{ __('Created At:') }}</span>
                                <span>{{ $ticket_details->created_at->format('d M Y H:i') }}</span>
                            </li>
                        </ul>
                        <div class="d-flex justify-content-center pt-3">
                            <button type="button"
                                class="btn {{ $ticket_details->status == 'open' ? 'btn-danger' : 'btn-success' }} toggle-ticket-status"
                                data-id="{{ $ticket_details->id }}" data-status="{{ $ticket_details->status }}">
                                {{ $ticket_details->status == 'open' ? __('Close Ticket') : __('Reopen Ticket') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">{{ __('Ticket Description') }}</h5>
                </div>
                <div class="card-body">
                    <p>{{ $ticket_details->description }}</p>
                </div>
            </div>
        </div>

        <!-- Ticket Messages -->
        <div class="col-xl-8 col-lg-7 col-md-7">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">{{ __('Messages') }}</h5>
                    <a href="{{ route('admin.ticket') }}" class="btn btn-primary btn-sm">
                        <i class="ti ti-arrow-left me-1"></i>{{ __('Back to Tickets') }}
                    </a>
                </div>
                <div class="card-body">
                    <div class="chat-container">
                        @if ($ticket_details->message->count() > 0)
                            @foreach ($ticket_details->message as $message)
                                <div
                                    class="message-item {{ $message->type == 'admin' ? 'admin-message' : 'user-message' }}">
                                    <div class="message-header">
                                        <span
                                            class="fw-medium">{{ $message->type == 'admin' ? __('Admin') : $ticket_details->user->first_name . ' ' . $ticket_details->user->last_name }}</span>
                                        <span
                                            class="text-muted small">{{ $message->created_at->format('d M Y H:i') }}</span>
                                    </div>
                                    <div class="message-content">
                                        <p>{{ $message->message }}</p>
                                        @if (!empty($message->attachment))
                                            @php
                                                $file_ext = pathinfo($message->attachment, PATHINFO_EXTENSION);
                                                $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                            @endphp

                                            @php
                                                $file_ext = pathinfo($message->attachment, PATHINFO_EXTENSION);
                                                $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                                $image_path = in_array($file_ext, $image_extensions)
                                                    ? asset('storage/tickets/images/' . $message->attachment)
                                                    : asset('storage/tickets/files/' . $message->attachment);
                                            @endphp

                                            @if (in_array($file_ext, $image_extensions))
                                                <a href="{{ $image_path }}" target="_blank">
                                                    <img src="{{ $image_path }}" alt="Attached Image"
                                                        class="attachment-preview">
                                                </a>
                                            @else
                                                <a href="{{ $image_path }}" target="_blank"
                                                    class="attachment-link btn btn-sm btn-outline-primary">
                                                    <i class="ti ti-download me-1"></i>{{ __('Download Attachment') }}
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="ti ti-messages fs-1 text-muted mb-3"></i>
                                <p>{{ __('No messages yet') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Reply Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">{{ __('Reply to Ticket') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.ticket.details', $ticket_details->id) }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="message">{{ __('Message') }}</label>
                            <textarea id="message" name="message" class="form-control" rows="4"
                                placeholder="{{ __('Type your reply...') }}"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="attachment">{{ __('Attachment') }}</label>
                            <input id="attachment" name="attachment" type="file" class="form-control"
                                accept="image/*,.pdf,.doc,.docx,.txt">
                            <div class="form-text">{{ __('Allowed: JPG, PNG, PDF, DOC, TXT. Max size: 2MB') }}</div>
                            <div id="preview-container" class="mt-2"></div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="email_notify" name="email_notify">
                                <label class="form-check-label" for="email_notify">
                                    {{ __('Send email notification to user') }}
                                </label>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-send me-1"></i>{{ __('Send Reply') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
