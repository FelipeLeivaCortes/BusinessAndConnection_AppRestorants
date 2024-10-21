@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">{{ _lang('Invitation History') }}</h4>
            </div>

            <div class="card-body">
                <table class="table data-table">
                    <thead>
                        <tr>
                            <th>{{ _lang('Email') }}</th>
                            <th>{{ _lang('Business') }}</th>
                            <th>{{ _lang('Role') }}</th>
                            <th class="text-center">{{ _lang('Status') }}</th>
                            <th class="text-center">{{ _lang('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invitation_list as $invitation)
                        <tr>
                            <td>{{ $invitation->email }}</td>
                            <td>{{ $invitation->business->name }}</td>
                            <td>{{ $invitation->role->name }}</td>    
                            <td class="text-center">
                                @if($invitation->status == 1)
                                {!! show_status('Active', 'primary') !!}
                                @else
                                {!! show_status('Accepted', 'success') !!}
                                @endif
                            </td>
                            <td class="text-center">
                            <span class="dropdown">
								  <button class="btn btn-outline-primary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								  {{ _lang('Action') }}
								  
								  </button>
								  <form action="{{ route('invitation_history.destroy_invitation', $invitation->id) }}" method="post">
									@csrf
									<input name="_method" type="hidden" value="DELETE">

									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
										<button class="btn-remove dropdown-item" type="submit"><i class="ti-trash"></i>&nbsp;{{ _lang('Delete') }}</button>
									</div>
								  </form>
								</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection