@extends('layouts.admin')

@section('title', 'Edit Anggota')
@section('page-title', 'Edit Anggota')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('members.index') }}">Anggota</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('members.update', $member) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('members._form')
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">Perbarui</button>
                    <a href="{{ route('members.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
