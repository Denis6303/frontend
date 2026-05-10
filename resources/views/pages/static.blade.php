@extends('layouts.app')

@section('title', ($title ?? __('Information')) . ' - Votix')

@section('content')
    <div class="wrapper">
        <div class="breadcrumb-block">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 col-md-10">
                        <div class="barren-breadcrumb">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('home', ['locale' => $locale ?? app()->getLocale()]) }}">{{ __('Home') }}</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ $title ?? __('Information') }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-80">
            <div class="container">
                <div class="main-card p-4 p-md-5">
                    <h1 class="h3 mb-4">{{ $title ?? __('Information') }}</h1>
                    @include('pages.static.pages.' . ($page ?? 'about'))
                </div>
            </div>
        </div>
    </div>
@endsection
