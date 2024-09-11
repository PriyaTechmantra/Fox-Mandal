@extends('layouts.app')

@section('content')


<div class="container mt-5">
        <div class="row">
            <div class="col-md-12">

                @if ($errors->any())
                <ul class="alert alert-warning">
                    @foreach ($errors->all() as $error)
                        <li>{{$error}}</li>
                    @endforeach
                </ul>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h4>Member Detail
                            <a href="{{ url('members') }}" class="btn btn-danger float-end">Back</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <table class="">
                                <tr>
                                    <td class="text-muted">Name: </td>
                                    <td>{{$user->name}}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Email: </td>
                                    <td>{{ $user->email ??''}}</td>
                                </tr>
                                
                                <tr>
                                    <td class="text-muted">Contact Number :  </td>
                                    <td>{{ $user->mobile }}</td>
                                </tr>
                               
                                <tr>
                                    <td class="text-muted">Created At: </td>
                                    <td>{{ date('j M Y h:m A', strtotime($user->created_at)) }}</td>
                                </tr>
                            </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
                
@endsection


@section('script')

@endsection