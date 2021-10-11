@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Cities list') }}</div>
                <div class="card-body">
                    <table id="cities-table" class="table table-striped table-hover table-bordered" style=" width: 100%">
                        <thead>
                            <tr>
                                <th>{{ __('#') }}</th>
                                <th>{{ __('Api_id') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Country') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <div>
                        {{ __('Cities selected') }}
                    </div>
                    <div>
                        <div class="btn-group" role="group">
                            <button id="get-cities" type="button" class="btn btn-primary" title="{{ __('Get seleted cities') }}">Get</button>
                            <button id="clean-cities" type="button" class="btn btn-danger" title="{{ __('Clean selection') }}">Clean</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form id="form-compare-cities" method="POST" action="{{route('cities.compare.weather')}}">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script_page')
    <script>
        $(document).ready(function(){
            var table = $('#cities-table').DataTable({
                "processing": true,
                "serverSide": true,
                "select": {
                    "style": "single",
                    "info": false
                },
                "pagingType": "simple",
                "columnDefs": [
                    {
                        "targets": [0],
                        "name": "id",
                        "data": "id"
                    },
                    {
                        "targets": [1],
                        "name": "api_id",
                        "data": "api_id",
                        "visible": false
                    },
                    {
                        "targets": [2],
                        "name": "name",
                        "data": "name"
                    },
                    {
                        "targets": [3],
                        "name": "country_code",
                        "data": "country_code",
                    }
                ],
                "ajax" : {
                    "url": "{{route('cities.get')}}",
                    "type": "POST",
                    "dataType": "json",
                    "data": function(d){
                        d._token = $('meta[name="csrf-token"]').attr('content');
                    }
                }
            });
            table.on( 'select', function ( e, dt, type, indexes ) {
                var form = $('#form-compare-cities');
                var data = table.rows( indexes ).data()[0];
                var containerId = 'city-'+data.id;
                var isSelected = false;
                $(form).children().each(function(){
                    if(this.id === containerId){
                        isSelected = true;
                    }
                });
                if(isSelected === false){
                    var container = '<div id="city-'+data.id+'" class="card"><div class="card-body">';
                    container += '<b>Country: </b>'+data.country_code+' <b>City: </b>'+data.name;
                    container += '<input type="hidden" name="city-id[]" value="'+data.api_id+'">'
                    container += '</div><div>';
                    form.append(container);
                } else {
                    alert('Ya seleccionaste esa ciudad');
                }
            } );
            $('#get-cities').on('click', function (e) {
                e.preventDefault();
                var form = $('#form-compare-cities');
                var citiesCompareCount = $('#form-compare-cities :input').length;
                var token = '<input type="hidden" name="_token" value="'+$('meta[name="csrf-token"]').attr('content')+'">';
                console.log(citiesCompareCount);
                if( citiesCompareCount >= 4){
                    form.prepend(token);
                    form.submit();
                } else if (citiesCompareCount > 0) {
                    alert('Necesitas al menos 4 ciudades para poder comparar el clima');
                } else {
                    alert('No has seleccionado ninguna ciudad para comparar');
                }
            })
            $('#clean-cities').on('click', function (e) {
                e.preventDefault();
                var form = $('#form-compare-cities');
                form.empty();
            })
        });
    </script>
@endsection
