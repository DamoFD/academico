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
                            @foreach($ageRanges as $key => $range)
                                <th class="editable" data-age-range="{{ $key }}">@lang($range)</th>
                            @endforeach
                        </thead>

                        <tbody>
                            @foreach($data as $yearKey => $year)
                                @foreach($year['periods'] as $periodKey => $period)
                                    <tr class="period-row" data-year="{{ $yearKey }}" data-period="{{ $periodKey }}">
                                        <td></td>
                                        <td>{{ $period['period'] }}</td>
                                        @foreach($ageRanges as $range)
                                            <td data-period="{{ $period['period'] }}">
                                                {{ number_format($period[$range]) }} %
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr class="year-row" data-year="{{ $yearKey }}" style="font-weight: bold">
                                    <td>{{ $year['year'] }}</td>
                                    <td></td>
                                    @foreach($ageRanges as $range)
                                        <td>
                                            {{ number_format($year[$range]) }} %
                                        </td>
                                    @endforeach
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
        function randomcolor() {
            var letters = '0123456789ABCDEF';
            var color = '#';
            for (var i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }

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
                    backgroundColor: randomcolor()
                }
            })
        };

        for (const key in data) {
            if (data.hasOwnProperty(key)) {
                const yearData = data[key];
                for (const periodKey in yearData.periods) {
                    if (yearData.periods.hasOwnProperty(periodKey)) {
                        const periodData = yearData.periods[periodKey];
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
    <script>

        $(document).ready(() => {
            var startYear, startPeriod
    $('.editable').on('click', handleEditableClick);

    function handleEditableClick() {
        console.log('clicked')
        var range = $(this).data('age-range');
        var content = $(this).text(); // this will be accessible within the inner functions
        var startPeriod = $(this).data('period');
        $(this).text('');

        $('<input>')
            .attr({
                'type': 'text',
                'name': 'fname',
                'id': 'txt_' + range,
                'size': '30',
                'value': content
            })
            .appendTo(this)
            .click(function(event) {
                event.stopPropagation();
            })
            .on('blur', function() {
                handleInputBlur.call(this, content); // pass the original content
            });

        // Unbind the click handler temporarily
        $('.editable').off('click', handleEditableClick);
    }

        function updateTableWithData(updatedData, ageRanges) {
    $.each(updatedData, function(yearKey, yearData) {
        // Update period rows
        $.each(yearData['periods'], function(periodKey, periodData) {
            var periodRow = $('.period-row[data-year="' + yearKey + '"][data-period="' + periodKey + '"]');
            $.each(ageRanges, function(rangeIndex, ageRange) {
                var cellValue = periodData[ageRange];
                var cellElement = periodRow.find('td[data-age-range="' + ageRange + '"]');
                cellElement.text(cellValue + " %");
            });
        });

        // Update year summary row
        var yearRow = $('.year-row[data-year="' + yearKey + '"]');
        $.each(ageRanges, function(rangeIndex, ageRange) {
            var cellValue = yearData[ageRange];
            var cellElement = yearRow.find('td[data-age-range="' + ageRange + '"]');
            cellElement.text(cellValue + " %");
        });
    });
}

            function handleInputBlur(originalContent) {
    var newText = $(this).val();
    var range = $(this).parent().data('age-range');

    // Attach the click handler again
    $('.editable').on('click', handleEditableClick);

    var inputElement = $(this); // save reference to input
                console.log(startPeriod)

    $.ajax({
        url: '/report/age',
        type: 'post',
        data: {
            range: range,
            value: newText,
            startPeriod: startPeriod,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                alert("Update successful");
                inputElement.parent().text(newText);
                console.log(response.data)
                updateTableWithData(response.data, response.ageRanges);
            } else {
                alert("Error: " + response.error);
                inputElement.parent().text(originalContent);
            }
            inputElement.remove(); // move this inside the success callback
        }
    });
}

            $('.editable').on('keydown', 'input', function (e) {
                if (e.keyCode == 13) {
                    $(this).trigger( "blur" );
                }
            });

        })
    </script>
@endsection
