KB.component('chart-project-analytics-completed-complexity', function (containerElement, options) {
    this.render = function () {
        var metrics = options.metrics;

        // Set the formats for the dates
        var inputFormat = d3.time.format("%Y-%m-%d");
        var outputFormat = d3.time.format(options.dateFormat);
        var weeksColumn = ["Week"];

        // Deal with getting a variable function based on the 
        // passed option
        //console.log(options.first_day);
        var weekDayFunc = d3.time[options.first_day.toLowerCase()];
        var firstWeek = weekDayFunc.floor(inputFormat.parse(metrics[1][0]));
        var lastWeek = weekDayFunc.floor(inputFormat.parse(metrics[metrics.length - 1][0]));
        var weeks = d3.time[options.first_day.toLowerCase() + 's'](firstWeek, lastWeek);
        for (var i = 0; i < weeks.length; i++) {
            weeksColumn.push(outputFormat(weeks[i]));
        }

        var columns = [weeksColumn, ["Complexity Completed"]];

        var previousValue = metrics[1][metrics[1].length - 1];
        for (var i = 1; i < metrics.length; i++) {
            var currentValue = metrics[i][metrics[i].length - 1];
            var currentWeek = weekDayFunc.floor(inputFormat.parse(metrics[i][0]));
            var weekIndex = columns[0].indexOf(outputFormat(currentWeek));

            if (typeof columns[1][weekIndex] === 'undefined') {
                if (currentValue > previousValue) {
                    columns[1].push(currentValue - previousValue);
                } else {
                    columns[1].push(0);
                }
            } else {
                if (currentValue > previousValue){
                    columns[1][weekIndex] += currentValue - previousValue;
                }
            }
            previousValue = currentValue;
        }

        KB.dom(containerElement).add(KB.dom('div').attr('id', 'chart').build());

        c3.generate({
            data: {
                columns: columns,
                x: 'Week'
            },
            axis: {
                x: {
                    type: 'timeseries',
                }
            },
            tooltip: {
                show: true,
                format: {
                    title: function (x, index) {
                        // Gets the year that we need to use
                            var weekYearFormat = d3.time.format("%Y")
                            var weekYear = weekYearFormat(d3.time.year(x));
                            //console.log(weekYear);
    
                            // Gets the end date of the week we're looking at in the number
                            // of the day of the year
                            var weekStart = d3.time.dayOfYear(x);
                            // The difference between this function and the %j format is -1, since
                            // the former is 0-indexed, while the latter is 1-indexed.
                            // Therefore, to get six days from the beginning of the week, we have to
                            // add 7 days to get 6 more when we format it.
                            var weekEnd = d3.time.dayOfYear(x) + 7;
                            //console.log(weekStart);
                        //console.log(weekEnd);

                        // Formats the end date with the year to get the right date object
                        var weekEndFormat = d3.time.format("%j %Y");
                        var weekEndDate = weekEndFormat.parse(weekEnd + ' ' + weekYear)
                            //console.log(outputFormat(weekEndDate));
                            //var week_end = weekEndFormat(d3.time.dayOfYear(weeks[i]));
                            //console.log(outputFormat(weeks[i]) + '-' + outputFormat(weekEndDate));
                        var ret_val = outputFormat(x) + ' ~ ' + outputFormat(weekEndDate);
                        return ret_val;
                    }
                }
            }
        });
    };
});

