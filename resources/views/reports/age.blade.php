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
                            <th>@lang('13-18')</th>
                            <th>@lang('19-21')</th>
                            <th>@lang('21+')</th>
                            <th>@lang('unknown')</th>
                        </thead>

                        <tbody>
                            @foreach($data as $year)
                                @foreach($year['periods'] as $period)
                                    <tr>
                                        <td></td>
                                        <td>{{ $period['period'] }}</td>
                                        <td>
                                            @if ($period['unknown'] < 25) {{ number_format($period['0-6']) }} %
                                            @else {{__('Insufficient data')}} @endif
                                        </td>
                                        <td>
                                            @if ($period['unknown'] < 25) {{ number_format($period['7-12']) }} %
                                            @else {{__('Insufficient data')}} @endif
                                        </td>
                                        <td>
                                            @if ($period['unknown'] < 25) {{ number_format($period['13-18']) }} %
                                            @else {{__('Insufficient data')}} @endif
                                        </td>
                                        <td>
                                            @if ($period['unknown'] < 25) {{ number_format($period['19-21']) }} %
                                            @else {{__('Insufficient data')}} @endif
                                        </td>
                                        <td>
                                            @if ($period['unknown'] < 25) {{ number_format($period['21+']) }} %
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
                                    <td>
                                        {{ number_format($period['0-6']) }} %
                                    </td>
                                    <td>
                                        {{ number_format($period['7-12']) }} %
                                    </td>
                                    <td>
                                        {{ number_format($period['13-18']) }} %
                                    </td>
                                    <td>
                                        {{ number_format($period['19-21']) }} %
                                    </td>
                                    <td>
                                        {{ number_format($period['21+']) }} %
                                    </td>
                                    <td>
                                        {{ number_format($period['unknown']) }} %
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

@section('before_scripts')

@endsection

@section('after_scripts')

    <script>
        $(document).ready(() => {
        var data = @json($data);
        var axisLabel = @json(__('% of students in period'));

        var ageRanges = Object.keys(data[1]).filter(key => key !== 'year' && key !== 'periods');

        var chartData = {
            labels: [],
            datasets: ageRanges.map((range) => {
                return {
                    label: range,
                    data: [],
                    // Add your own color styling for each dataset
                    backgroundColor: 'rgba(245,255,152,0.6)',
                    borderColor: '#f5e700'
                }
            })
        };

        for (const key in data) {
            if (data.hasOwnProperty(key)) {
                const yearData = data[key];
                for (const periodKey in yearData.periods) {
                    if (yearData.periods.hasOwnProperty(periodKey)) {
                        const periodData = yearData.periods[periodKey];
                        // Check if 'unknown' percentage is less or equal to 25%
                        if (periodData['unknown'] <= 25) {
                            chartData.labels.push(periodData.period);
                            for (const range of ageRanges) {
                                let dataset = chartData.datasets.find(ds => ds.label === range);
                                // You may need to check if dataset exists before trying to push data to it
                                if (dataset) {
                                    dataset.data.push(periodData[range]);
                                }
                            }
                        }
                    }
                }
            }
        }

        var ctx = document.getElementById("myChart");

        var myChart = new Chart(ctx, {
            type: 'horizontalBar',
            data: chartData,
            options: {
                scales: {
                    xAxes: [{
                        scaleLabel: {
                            labelString: axisLabel,
                            display: true,
                        },
                        ticks: {
                            beginAtZero: true,
                            max: 100,
                        }
                    }]
                },
                legend: {
                    display: true
                },
                aspectRatio: '4'
            }
        });
    });
    </script>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-html5-1.6.1/b-print-1.6.1/datatables.min.css"/>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-html5-1.6.1/b-print-1.6.1/datatables.min.js"></script>
    <script>
        $(document).ready(() =>
            $('#reportsTable').DataTable({
                dom: 'Bfrtip',
                "paging": false,
                "searching": false,
                "ordering": false,
                buttons: [
                    'excel',
                    'pdf',
                    'print'
                ]
            }));
    </script>
@endsection
