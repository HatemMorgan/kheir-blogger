@extends('layouts.app')

@section('content')

    <h1 class="well well-lg">Upload Photos to your event:</h1>
    <div class="container">
        @if(isset($success))
            <div class="alert alert-success"> {{$success}} </div>
        @endif
            {!! Form::model(null, array( 'method' => 'POST','files'=> true,'action' =>array('EventController@test1'))) !!}
        <div class="form-group">
            {!! Form::label('image', 'Choose photos') !!}
            {!! Form::file('images[]',array('multiple'=>true)) !!}
        </div>
            {!! Form::submit('Save', array( 'class'=>'btn btn-danger form-control' )) !!}
            {!! Form::close() !!} 
    @include('errors')
@stop
