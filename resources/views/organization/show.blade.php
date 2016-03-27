@extends('layouts.app')

@section('styling')
    <style media="screen">
        .jumbotron h1{
            text-align: center;
        }

        .vol-act{
            float: right;
            background-color: orange;
            width: 120px;
        }
    </style>
@endsection

@section('content')
    @include('errors')
    <div class="jumbotron">
        <h1>{{$organization->name}}</h1>
        @if($state==3)
             {!! Form::open(['action' => ['OrganizationController@subscribe',$organization->id],'method'=>'get']) !!}
             <div>
                {!! Form::submit('Subscribe' , array('class' => 'vol-act btn btn-default' )) !!}
             </div>
             {!! Form::close() !!}
        @elseif($state==2)
             {!! Form::open(['action' => ['OrganizationController@unsubscribe',$organization->id],'method'=>'get']) !!}
             <div>
                {!! Form::submit('Unsubscribe' , array('class' => 'vol-act btn btn-default' )) !!}
             </div>
             {!! Form::close() !!}
        @endif
        <p>Slogan: {{$organization->slogan}}</p>
        @if($state==2 || $state==3)
            {!! Form::open(['action' => ['OrganizationController@recommend', $organization->id],'method'=>'get']) !!}
            <div>
                {!! Form::submit('Recommend' , array('class' => 'vol-act btn btn-default' )) !!}
            </div>
            {!! Form::close() !!}
        @endif
        <p>Bio: {{$organization->bio}}</p>
        <p>Location: {{$organization->location}}</p>
        <p>Phone: {{$organization->phone}}</p>
        <p>Rate:
            @if($organization->rate)
                {{number_format($organization->rate, 1)}}
            @else
                No rate yet!
            @endif
        </p>
        <p>Subscribers: {{count($organization->subscribers)}}</p>
        <p>Events submitted: {{count($organization->events()->withTrashed())}}</p>
        <p>Events held: {{count($organization->events)}}</p>

        <h4>Events</h4>
        <ul>
            @for ($i = 0; $i < 3 && $i < count($organization->events); $i++)
                <li>{{$organization->events[$i]->name}}</li>
            @endfor
            @if(count($organization->events) > 1)
                    <a href="{{ action('EventController@index', [$organization->id])}}">View More >></a>
            @endif

        </ul>

        <h4>Reviews</h4>
        <ul>
            @for($i = 0; $i < 3 && $i < count($organization->reviews); $i++)
                <li>{{$organization->reviews[$i]->review}}, {{$organization->reviews[$i]->rate}}</li>
            @endfor
            @if(count($organization->reviews) > 3)
                    <a href="{{ action('OrganizationReviewController@index', [$organization->id])}}">View More >></a>
            @endif
        </ul>
    </div>
@stop