@extends('ypi.layout.admin_template')
@section('main')
    <div class="d-flex justify-content-between m-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}"><?= get_label('home', 'Home') ?></a>
                    </li>
                    <li class="breadcrumb-item"><a href="#">
                            <?= get_label('booking', 'Booking') ?></a>
                    </li>
                    <li class="breadcrumb-item active">
                        <?= get_label('send_invitation', 'Send Invitation') ?>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container">

        <div class="card shadow-none border my-4 col-md-8" style="margin:0 auto;" data-component-card="data-component-card">
            @php
                $link = registerUrl(17);
            @endphp

            <div class="input-group">
                <input type="text" class="form-control" value="{{ $link }}" readonly id="registerLink">

                <button class="btn btn-outline-secondary" type="button"
                    onclick="navigator.clipboard.writeText('{{ $link }}')">
                    Copy
                </button>
            </div>

            <div class='fw-bold'> Link to register new user for event <span class="text-success">{{ getEventNameById(session()->get('EVENT_ID')) }}</span>
            </div>

            <!-- <br /> -->
            <!-- &nbsp; -->
        </div>
    </div>
@endsection
