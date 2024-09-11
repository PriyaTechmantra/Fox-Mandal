@extends('layouts.app')

@section('content')


<div class="container mt-2">
        <div class="row">
            <div class="col-md-12">

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <div class="card mt-3">
                    <div class="card-header">
                        <h4>Issue List
                            
                        </h4>
                                <div class="search__filter mb-0">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <p class="text-muted mt-1 mb-0">Showing {{$data->count()}} out of {{$data->total()}} Entries</p>
                                        </div>
                                    </div>
                                        <div class="row">
                                        
                                            <div class="col-md-12 text-end">
                                                <form class="row align-items-end" action="">
                                                    <div class="col">
                                                        <input type="date" name="issue_date_from" id="term" class="form-control form-control-sm"  value="{{app('request')->input('issue_date_from')}}">
                                                    </div>
                                                     <div class="col">
                                                        <input type="date" name="issue_date_to" id="term" class="form-control form-control-sm"  value="{{app('request')->input('issue_date_to')}}">
                                                    </div>
                                                    <div class="col">
                                                        <div class="btn-group">
                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                Filter
                                                            </button>
                        
                                                            <a href="{{ url()->current() }}" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="Clear Filter">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                                            </a>
                                                            {{--@can('book csv export')
                                                            <a href="{{ url('books/issue/list/export/csv') }}" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Export data in CSV">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                                                CSV
                                                            </a>
                                                            @endcan--}}
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                           
                                        </div>
                                    
                                </div>
                                
                    </div>
                    <div class="card-body">

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Member Name</th>
                                    <th>Book Name</th>
                                    <th>Issue request date</th>
                                    <th>Approval Status</th>
                                    <th>Approval</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index=> $item)
                                @php
                                $transfer=\App\Models\BookTransfer::where('book_id',$item->id)->where('from_user_id',$item->user_id)->with('toUser')->first();   
                                @endphp
                                <tr>
                                    <td>{{ $index+1 }}</td>
                                    <td><a href="{{ url('members/'.$item->user->id) }}">{{ $item->user->name ??''}}</a></td>
                                    <td><a href="{{ url('books/'.$item->book->id) }}">{{ $item->book->title ??''}}</a></td>
                                    <td><span class="text-dark font-weight-bold mb-2">
                                            {{date('j M Y', strtotime($item->request_date))}}
                                        </span></td>
                                        <td>@if($item->status === 1)
                    							 <span class="badge bg-success">Approved</span>
                    						@elseif($item->status === 0)
                    							 <span class="badge bg-danger">Rejected</span>
                    						@elseif($item->status === null)
                    						<span class="badge bg-warning">Waiting for approval</span>
                    						@endif</td>
                                    <td>
                							<div class="btn-group" role="group">
                								<a href="{{ url('issues/books/'.$item->id.'/status/change', [$item->id, 1]) }}" type="button" class="status_1 btn btn-outline-primary btn-sm {{($item->status == 1) ? 'active' : ''}}">Approved</a>
                
                								<a href="{{ url('issues/books/'.$item->id.'/status/change', [$item->id, 0]) }}" type="button" class="status_2 btn btn-outline-danger btn-sm {{($item->status == 0) ? 'active' : ''}}">Rejected</a>
                							</div>
                    						
            						</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {!! $data->render() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>




@endsection


@section('script')
<script>
    $('select[name="office_id"]').on('change', (event) => {
        var value = $('select[name="office_id"]').val();
        OfficeChange(value);
    });
    @if (request()->input('office_id'))
        OfficeChange({{request()->input('office_id')}})
    @endif

    function OfficeChange(value) {
        $.ajax({
            url: '{{url("/")}}/bookshelves/list/officewise/'+value,
            method: 'GET',
            success: function(result) {
                var content = '';
                var slectTag = 'select[name="bookshelves_id"]';
                var displayCollection =  "All";

                content += '<option value="" selected>'+displayCollection+'</option>';
                $.each(result.data, (key, value) => {
                    let selected = ``;
                    @if (request()->input('bookshelves_id'))
                        if({{request()->input('bookshelves_id')}} == value.id) {selected = 'selected';}
                    @endif
                    content += '<option value="'+value.id+'"'; content+=selected; content += '>'+value.number+'</option>';
                });
                $(slectTag).html(content).attr('disabled', false);
            }
        });
    }
    
</script>
@endsection