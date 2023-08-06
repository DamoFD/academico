@extends('backpack::blank')

@section('header')
    <section class="container-fluid">
	  <h2>
        {{ __('Students by age') }}
      </h2>
    </section>
@endsection

@section('content')

    <div class="row">

        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-header-actions">
                        <span>@lang('Start from period:')</span>
                        <!-- Period selection dropdown -->
                        @include('partials.period_selection')
                    </div>
                </div>

                <div class="card-body">
                    <table id ="reportsTable" class="table table-striped">
                        <thead>
                            <th>@lang('Year')</th>
                            <th>@lang('Period')</th>
                            <th>@lang('0-6')</th>
                            <th>@lang('7-12')</th>
                            <th>@lang('8-14')</th>
                            <th>@lang('15-20')</th>
                            <th>@lang('21+')</th>
                        </thead>

                        <tbody>
                            @foreach($data as $year)
                                @foreach($year['periods'] as $period)
                                    <tr>
                                        <td></td>
                                        <td>{{ $period['period'] }}</td>
                                        <td>
                                            @if ($period['unknown'] < 25) {{ number_format($period['female']) }} %
                                            @else {{__('Insufficient data')}} @endif
                                        </td>
                                        <td>
                                            @if ($period['unknown'] < 25) {{ number_format($period['male']) }} %
                                            @else {{__('Insufficient data')}} @endif
                                        </td>
                                        <td>
                                            <span class="{{ $period['unknown'] < 25 ? '' : 'text-danger' }}">{{ number_format($period['unknown']) }} %</span>

                                        </td>
                                    </tr>
                                @endforeach
                                <tr style="font-weight: bold">
                                    <td>{{ $year['year'] }}</td>
                                    <td></td>
                                    <td>{{ number_format($year['female']) }} %</td>
                                    <td>{{ number_format($year['male']) }} %</td>
                                    <td>{{ number_format($year['unknown']) }} %</td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection
