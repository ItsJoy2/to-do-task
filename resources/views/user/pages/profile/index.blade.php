@extends('user.layouts.app')

@section('userContent')

<div class="page-header">
    <h3 class="page-title">My Profile</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">User</a></li>
            <li class="breadcrumb-item active" aria-current="page">Profile</li>
        </ol>
    </nav>
</div>

<div class="row">


                @include('user.layouts.alert')

    {{-- Profile Summary & Edit Form --}}
    <div class="col-md-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">

                {{-- Profile Image Upload --}}
                <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="text-center position-relative mb-4">
                        <label for="profileImageInput" class="cursor-pointer position-relative d-inline-block">
                            <img
                                src="{{ $user->image ? asset('storage/' . $user->image) : url('assets/profile-icon.png') }}"
                                alt="Profile Image"
                                id="profilePreview"
                                class="rounded-circle shadow bg-secondary border {{ $user->is_active == 1 ? 'border-success' : 'border-secondary' }}"
                                width="130"
                                height="130"
                                style="object-fit: cover; border-width: 3px !important;"
                                onerror="this.src='{{ url('public/assets/profile-icon.png') }}'"
                            >
                            <div class="position-absolute bg-dark text-white rounded-circle" style="bottom: 0; right: 0; padding: 3px 8px; cursor: pointer;">
                                <i class="mdi mdi-camera"></i>
                            </div>
                        </label>
                        <input type="file" name="image" id="profileImageInput" class="d-none" accept="image/*">
                        @error('image') <small class="text-danger d-block">{{ $message }}</small> @enderror
                    </div>

                    {{-- Name --}}
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control text-white">
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                     {{-- Email --}}
                    <div class="form-group">
                        <label>Email</label>
                        <input readonly type="text" name="email" value="{{ old('email', $user->email) }}" class="form-control text-white" style="background: none">
                        @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary mt-2">Update Profile</button>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection


@push('scripts')

    <script>
        document.querySelectorAll('.btn-copy').forEach(button => {
            button.addEventListener('click', function () {
                const text = this.getAttribute('data-copy');
                navigator.clipboard.writeText(text).then(() => {
                    this.innerText = 'Copied!';
                    setTimeout(() => this.innerText = 'Copy', 2000);
                });
            });
        });

        document.getElementById('profileImageInput').addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const preview = document.getElementById('profilePreview');
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
    <script>
        document.getElementById('nomineeImageInput')?.addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('nomineePreview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
@endpush
