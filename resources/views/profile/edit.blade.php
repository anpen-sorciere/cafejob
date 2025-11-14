@extends('layouts.app')

@section('title', 'プロフィール編集')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">
                <i class="fas fa-user-circle me-2"></i>プロフィール編集
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">プロフィール情報</h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">プロフィール写真（最大2枚）</h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-profile-images-form')
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">パスワード変更</h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
