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
                        <h4>Books Detail
                            <a href="{{ url('books') }}" class="btn btn-danger float-end">Back</a>
                            <a type="button" id="basic" class="btn btn-danger float-end">Download Qrcode</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <table class="">
                                <tr>
                                    <td class="text-muted">Office: </td>
                                    <td>{{$data->office->name}}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Office Location: </td>
                                    <td>{{ $data->office->address ??''}}</td>
                                </tr>
                                
                                <tr>
                                    <td class="text-muted">Bookshelf Number :  </td>
                                    <td>{{ $data->bookshelves->number }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Category : </td>
                                    <td>{{$data->category->name}}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Uid : </td>
                                    <td>{{ $data->uid ??''}}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Title : </td>
                                    <td>{{ $data->title ??''}}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Author : </td>
                                    <td>{{ $data->author ??''}}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Publisher : </td>
                                    <td>{{ $data->publisher ??''}}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Edition : </td>
                                    <td>{{ $data->edition ??''}}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Pages : </td>
                                    <td>{{ $data->page ??''}}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Quantity : </td>
                                    <td>{{ $data->quantity ??''}}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Status : </td>
                                    <td>{{($data->status == 1) ? 'Active' : 'Inactive'}}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Created By: </td>
                                    <td>{{ $data->user->name ??'' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Qrcode: </td>
                                    <td><img src="https://bwipjs-api.metafloor.com/?bcid=qrcode&text={{$data->qrcode}}&height=6&textsize=10&scale=6&includetext" alt="" style="height: 105px;width:105px" id="{{$data->qrcode}}"></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Created At: </td>
                                    <td>{{ date('j M Y h:m A', strtotime($data->created_at)) }}</td>
                                </tr>
                            </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
                <div class="card-body" >
                        <table class="" style="display:none" id="print-code">
                                <tr>
                                    <td class="text-muted">Book Name :  </td>
                                    <td>{{ $data->title }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Qrcode: </td>
                                    <td><img src="https://bwipjs-api.metafloor.com/?bcid=qrcode&text={{$data->qrcode}}&height=6&textsize=10&scale=6&includetext" alt="" style="height: 105px;width:105px" id="{{$data->qrcode}}"></td>
                                </tr>
                        </table>
                </div>
@endsection


@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- printThis Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/printThis/1.15.0/printThis.min.js"></script>
<script>

   $(document).ready(function() {
            $('#basic').on('click', function() {
                $('#print-code').show();
                $('#print-code').printThis({
                importCSS: true,        // Import page CSS
                importStyle: true,      // Import style tags
                loadCSS: "",            // Load an additional CSS file
                pageTitle: "Books Info", // Title for the printed document
                removeInline: false,    // Keep the inline styles
                printDelay: 333,        // Delay before printing to allow images to load
                afterPrint: function() {
                    $('#print-code').hide(); // Hide the table again after printing
                }
            });
            });
        });
</script>
@endsection