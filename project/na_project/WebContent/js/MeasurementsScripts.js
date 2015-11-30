

$(document).ready(function() {
	// hide certain content on load
	$('.add_measurement_section').hide();
	$('.edit_measurement_section').hide();
	$('.editMeasurement').parent().hide();
	$('.deleteMeasurement').parent().hide();
	
	// add listeners for page navigation buttons (i.e. for jumping to a measurement)
	$('#page-nav button').click(function(event) {
		window.location.assign('#' + $(this).attr('name'));
	});
	$('body').click(deselectAllRows);
	
	// add listener for add/edit/delete measurement and cancel buttons
	$('.addMeasurement').click(addMeasurement);
	$('.editMeasurement').click(editMeasurement);
	$('.deleteMeasurement').click(deleteMeasurement);
	$('.cancelMeasurement').click(cancelMeasurement);
	
	// add hover and click listeners for measurement rows
	$('tbody > tr.measurementRow').each(function(index, element) {
		$(element).hover(
			function() { $(element).addClass('rowHover');    },
			function() { $(element).removeClass('rowHover'); }
		);
		$(element).click(rowClicked);
	});
	
	// grab measurement data from server and create tables
	var table_bloodPressure = $('#bloodPressure_table').DataTable(tableOptions('bloodPressure', [ ['systolicPressure', 'Systolic Pressure'], ['diastolicPressure', 'Diastolic Pressure'] ]));
	var table_glucose = $('#glucose_table').DataTable(tableOptions('glucose', [ ['glucose', 'Glucose (mg/dL)'] ]));
	var table_calories = $('#calories_table').DataTable(tableOptions('calorie', [ ['calories', 'Calories'] ]));
	var table_execrise = $('#exercise_table').DataTable(tableOptions('exercise', [ ['duration', 'Exercise (min)'] ]));
	var table_sleep = $('#sleep_table').DataTable(tableOptions('sleep', [ ['duration', 'Sleep (min)', ] ]));
	var table_weight = $('#weight_table').DataTable(tableOptions('weight', [ ['weight', 'Weight (kg)'] ]));
	
	// grab measurement data from server and create charts
	createCharts('glucose', 'Glucose', 'mg/dL',  ['glucose'], 'month');
	createCharts('bloodPressure', 'Blood Pressure', 'mm Hg', ['systolicPressure', 'diastolicPressure'], 'week');
	createCharts('calorie', 'Calories', 'calories', ['calories'], 'day');
	createCharts('exercise', 'Exercise', 'minutes', ['duration'], 'day');
	createCharts('sleep', 'Sleep', 'minutes', ['duration'], 'individual');
	createCharts('weight', 'Weight', 'kg', ['weight'], 'individual');
	
	// assign handlers for chart date range buttons 
	$('.btn-yearly').click(viewYearChart);
	$('.btn-monthly').click(viewMonthChart);
	$('.btn-weekly').click(viewWeekChart);
});

function tableOptions(measType, dataAndTitle) {
	var columns = [
        { data: 'date', title: 'Date' },
        { data: 'time', title: 'Time' },
	    { data: 'notes', title: 'Notes' }
    ];
	var orderIndex = (measType == 'bloodPressure') ? 2 : 1;

	for (var i = dataAndTitle.length-1; i >= 0; i--)
		columns.unshift({ data: dataAndTitle[i][0], title: dataAndTitle[i][1] });
	
	return {
		ajax: { url: '/na_project/measurements_get_' +measType+ '_all' , dataSrc: '' },
		columns: columns,
		order: [[orderIndex, 'desc']],
		scrollY: '35vh',
		scrollCollapse: true,
		paging: false,
		select: true,
		dom: 'ft'
		//buttons: { name: 'primary', buttons: ['columnsToggle'] }
	};
}

function failedRetreivingChartData() {
	alert('Error retreiving measurements');
}

function createCharts(measType, properType, units, measNames, per) {
	$.ajax({
		'url': 'measurements_get_' +measType+ '_' +per,
		'dataType': 'json',
		'async': false,
		'success': function(response) {
			var data = [];
			var periodName;
			
			switch (per) {
				case 'all':
				case 'individual':
					periodName = 'dateAndTime';
					break;
				default:
					periodName = per;
					break;
			}
			
			for (var i = 0; i < measNames.length; i++) {
				var currentData = [];
				for (var j = 0; j < response.length; j++)
					currentData.push( [ response[j][periodName], parseFloat(response[j][measNames[i]]) ] );
				data.push(currentData);
			}	
			createCharts_helper(measType, properType, units, data, measNames, per);
		},
		'error': failedRetreivingChartData
	});
}

function createCharts_helper(measType, properType, units, data, name, per) {
	
	for (var i = 0; i < 2; i++) {
		
		var min =      (i == 0) ? lastWeek()  : lastMonth();
		var subtitle =    (i == 0) ? 'Past Week' : 'Past Month';
		var idSuffix = (i == 0) ? 'primary'   : 'secondary';
		var series = [];
		var numOfSeriesData = data.length;
		var minY = 100000;
		var maxY = 0;
		var formatStr;
		
		for (var j = 0; j < numOfSeriesData; j++) {
			for (var k = 0; k < data[j].length; k++) {
				if (data[j][k][1] < minY)
					minY = data[j][k][1];
				if (data[j][k][1] > maxY)
					maxY = data[j][k][1];
			}
			if (data[j][1] < minY)
				minY = data[j][1];
			if (data[j][1] > maxY)
				maxY = data[j][1];
			series.push( {
				name: name[j],
				data: data[j]
			} );
		}
		
		$('#' + measType + '_chart_' + idSuffix).highcharts({
			chart: { type: 'line' },
			title: { text: properType },
			subtitle: { text: subtitle },
			xAxis: {
				type: 'category',
//				min: min,
//				max: todaysEnd(),
				labels: {
					formatter: function () {
						var result = '';
						var date;
						var pieces;
						
						switch (per) {
							case 'all':
							case 'individual':
							case 'day':
								date = new Date(this.value);
								return monthNumToShortName(date.getMonth(), true)+ ' ' +date.getDate();
							case 'week':
								pieces = this.value.split('-');
								if (this.isFirst || pieces[1] == 1)
									result += '(' +pieces[0]+ ') ';
								return result+ 'Week ' +pieces[1];
							case 'month':
								pieces = this.value.split('-');
								if (this.isFirst || pieces[1] == 1)
									result += '(' +pieces[0]+ ') ';
								return result + monthNumToShortName(parseInt(pieces[1]), false);
							case 'year':
								return this.value;
						}
					}
				}
			},
			yAxis: { title: { text: units }, min: minY, max: maxY },
			series: series,
			tooltip: {
				formatter: function() {
					var date = new Date(this.x);
					var dateStr = date.toDateString();
					var timeStr = dateTo12HourLocalTimeString(date);
					
					var resultStr = '<span style="font-size: smaller;">' +dateStr+ '</span>';
					resultStr += '<br /><span style="font-size: smaller;">' +timeStr+ '</span>';
					resultStr += '<br /><span style="color: ' +this.series.color+ '">\u25CF</span> ' +this.series.name+ ': <strong>' +this.y+ '</strong>';

			        return resultStr;
				}
			}
		});
	}
}

function monthNumToShortName(num, isZeroBased) {
	if (isZeroBased)
		num++;
	switch (num) {
		case 1: return 'Jan';
		case 2: return 'Feb';
		case 3: return 'Mar';
		case 4: return 'Apr';
		case 5: return 'May';
		case 6: return 'Jun';
		case 7: return 'Jul';
		case 8: return 'Aug';
		case 9: return 'Sep';
		case 10: return 'Oct';
		case 11: return 'Nov';
		case 12: return 'Dec';
		default: return 'error';
	}
}

// takes a Date object and returns a time string in a 12-hour format, e.g.: 2:06 pm (CST) 
function dateTo12HourLocalTimeString(date) {
	var pieces = date.toTimeString().split(' ');
	
	var timeZone = '(';
	$.each(pieces.slice(2), function(index, string) { timeZone += (index == 0) ? string[1] : string[0]; } );
	timeZone += ')';
	var time = pieces[0];
	var timePieces = time.split(':');
	var hour = timePieces[0];
	var minute = timePieces[1];
	var amOrPm = (hour >= 12) ? 'pm' : 'am';
	hour = hour % 12;
	
	return hour+ ':' +minute+ ' ' +amOrPm+ ' ' +timeZone
}

function todaysEnd() {
	var today = new Date();
	
	var endOfToday = Date.UTC(today.getFullYear(), today.getMonth(), today.getDate(), 23, 59, 59, 59);
	
	return endOfToday;
}

function lastWeek() {
	var today = new Date();
	
	var sixDaysAgoDate = new Date(today.valueOf() - (1000 * 60 * 60 * 24 * 6))
	var sixDaysAgoUTC = Date.UTC(sixDaysAgoDate.getFullYear(), sixDaysAgoDate.getMonth(), sixDaysAgoDate.getDate());
	
	return sixDaysAgoUTC;
}

function lastMonth() {
	var today = new Date();
	
	var thirtyDaysAgoDate = new Date(today.valueOf() - (1000 * 60 * 60 * 24 * 30));
	var thirtyDaysAgoUTC = Date.UTC(thirtyDaysAgoDate.getFullYear(), thirtyDaysAgoDate.getMonth(), thirtyDaysAgoDate.getDate());
	
	return thirtyDaysAgoUTC;
}

function lastYear() {
	var today = new Date();
	
	var threeFiftySixDaysAgoDate = new Date(today.valueOf() - (1000 * 60 * 60 * 24 * 356))
	var threeFiftySixDaysAgoUTC = Date.UTC(threeFiftySixDaysAgoDate.getFullYear(), threeFiftySixDaysAgoDate.getMonth(), threeFiftySixDaysAgoDate.getDate());
	
	return threeFiftySixDaysAgoUTC;
}

function viewYearChart() {
	// deactivate/activate associated buttons
	$(this).parent().parent().find('.active').removeClass('active');
	$(this).addClass('active');
	
	// update chart
	var chart = getChart($(this).attr('id'));
	chart.setTitle( { text: $(this).attr('name') }, { text: 'Past Year' } );
	chart.xAxis[0].setExtremes(lastYear(), todaysEnd());
}

function viewWeekChart() {
	// deactivate/activate associated buttons
	$(this).parent().parent().find('.active').removeClass('active');
	$(this).addClass('active');
	
	// update chart
	var chart = getChart($(this).attr('id'));
	chart.setTitle( { text: $(this).attr('name') }, { text: 'Past Week' } );
	chart.xAxis[0].setExtremes(lastWeek(), todaysEnd());
}

function viewMonthChart() {
	// deactivate/activate associated buttons
	$(this).parent().parent().find('.active').removeClass('active');
	$(this).addClass('active');
	
	// update chart
	var chart = getChart($(this).attr('id'));
	chart.setTitle( { text: $(this).attr('name') }, { text: 'Past Month' } );
	chart.xAxis[0].setExtremes(lastMonth(), todaysEnd());
}

// takes the id attribute of a chart, and returns the chart's javascript object
function getChart(chartID) {
	var index = -1;
	var idPieces = chartID.split('_');
	var measType = idPieces[0];
	var primaryOrSecondary = idPieces[4];
	
	switch (measType) {
		case 'glucose': index = 0; break;
		case 'bloodPressure': index = 2; break;
		case 'calories': case 'calorie': index = 4; break;
		case 'exercise': index = 6; break;
		case 'sleep': index = 8; break;
		case 'weight': index = 10; break;
	}
	
	if (primaryOrSecondary == 'secondary')
		index++;
	
	return Highcharts.charts[index];
}

// an add measurement button was clicked
function addMeasurement(event) {
	event.stopPropagation();
	
	// show the add measurement section for the associated measurement type and jump to it
	var btnID_pieces = $(this).attr('id').split('_');
	var form_type = btnID_pieces[0];
	var meas_type = btnID_pieces[1];
	showFormSection(meas_type, form_type);	
	window.location.assign('#add_' + meas_type + '_section');
}

// an edit measurement button was clicked
function editMeasurement(event) {
	event.stopPropagation();
	
	// fill the edit form with data from the currently selected measurement, then show it and jump to it
	var meas_type = fillEditForm();
	showFormSection(meas_type, 'edit');
	window.location.assign('#edit_' + meas_type + '_section');
}

// a delete measurement button was clicked
function deleteMeasurement(event) {
	if (window.confirm('Are you sure you want to delete the selected measurement?')) {
		event.stopPropagation();
		
		// determine the measurement to delete
		var id = $('tbody > tr.rowSelected').attr('id');
		var measurementPieces = id.split('_');
		var measurementType = measurementPieces[0];
		var measurementDate = measurementPieces[1];
		var measurementTime = measurementPieces[2];
		
		// send delete request to measurements controller
		window.location.assign('measurements_delete_' + measurementType + '_' + measurementDate + ' ' + measurementTime);
	}
}

// a cancel button for an add or edit form was clicked
function cancelMeasurement(event) {
	event.stopPropagation();
	
	// determine which measurement and which form (add/edit) to hide
	var btnID_pieces = $(this).attr('id').split('_');
	var form_type = btnID_pieces[1];
	var meas_type = btnID_pieces[2];
	
	// hide form and jump to the associated measurements table
	hideFormSection(meas_type, form_type);
	window.location.assign('#view_' + meas_type + '_section');
}

// selects a row when clicked, and fills in the edit form if it is visible
function rowClicked(event) {
	event.stopPropagation();
	var meas_type = $(this).attr('id').split('_')[0];
	deselectAllRows();
	selectRow(this);
	if ($('#edit_' + meas_type + '_section').is(':visible'))
		fillEditForm();
}

function hideFormSection(meas_type, form_type) {
	
	// hide form
	$('#' + form_type + '_' + meas_type + '_section').hide();
	$('#view_' + meas_type + '_section').removeClass('col-sm-8');
	$('#view_' + meas_type + '_section').addClass('col-sm-12');
	
	// deactivate button
	$('#' + form_type + '_' + meas_type + '_btn').removeClass('active');
}

function showFormSection(meas_type, form_type) {
	
	// hide other section if it is visible, and deactivate its corresponding button
	if (form_type == 'add' && $('#edit_' + meas_type + '_section').is(':visible'))
		hideFormSection(meas_type, 'edit');
	else if (form_type == 'edit' && $('#add_' + meas_type + '_section').is(':visible'))
		hideFormSection(meas_type, 'add');
	
	// show form
	$('#' + form_type + '_' + meas_type + '_section').show();
	$('#view_' + meas_type + '_section').removeClass('col-sm-12');
	$('#view_' + meas_type + '_section').addClass('col-sm-8');
	
	// activate button
	$('#' + form_type + '_' + meas_type + '_btn').addClass('active');
}

// collects data from currently selected row, and fills in its corresponding edit form
// returns the type of measurement of the currently selected row
function fillEditForm() {
	
	// collect data from selected row
	var selected_row = $('tbody > tr.rowSelected');
	var rowID_pieces = selected_row.attr('id').split('_');
	var meas_type = rowID_pieces[0];
	var date = rowID_pieces[1];
	var time = rowID_pieces[2];
	
	// fill in edit form fields
	selected_row.children().each(function(index, element) {
		var col_name = $(element).attr('id').split('_')[0];
		$('#' + col_name + '_' + meas_type + '_edit').val($(element).text());
	});
	$('#oldDateTime_' + meas_type).val(date + ' ' + time);
	
	return meas_type;
}

function selectRow(row) {
	
	// show edit/delete buttons
	var section = $(row).attr('id').split('_')[0];
	$('#edit_' + section + '_btn').parent().show();
	$('#delete_' + section + '_btn').parent().show();
	
	// select row
	$(row).addClass('rowSelected');
}

function deselectAllRows() {
	$('tbody > tr.measurementRow').each(function(index, element) {
		
		// hide edit/delete buttons
		var section = $(element).attr('id').split('_')[0];
		$('#edit_' + section + '_btn').parent().hide();
		$('#delete_' + section + '_btn').parent().hide();
		
		// deselect rows
		$(element).removeClass('rowSelected');
	});
}


