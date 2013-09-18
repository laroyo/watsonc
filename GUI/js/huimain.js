/*!
* Crowd-Watson GUI js 1.0  
* @requires jQuery V1.9.1
*
* Copyright (c) 2013 Crowd-Watson
* http://crowd-watson.nl/wcs/GUI/
*
* @type jQuery & javaScript
* @name Crowd-Watson GUI js
* @author Hui Lin/alice8linhui@gmail.com
*/

function checkForFilters1() {
	if (!document.getElementById("filters1").checked) {
		var radioButtons = document.getElementsByName('specialcases'); 
		for (var i = 0; i < 5; i ++) {
			if(radioButtons[i].checked == true)
				radioButtons[i].checked = false;
		}
	}
	else {
		var radioButtons = document.getElementsByName('specialcases');
		radioButtons[0].checked = true;

	}
}

function checkForFilters2() {
	if (!document.getElementById("filters2").checked) {
		var radioButtons = document.getElementsByName('relation'); 
		for (var i = 0; i < 3; i ++) {
			if(radioButtons[i].checked == true)
				radioButtons[i].checked = false;
		}
	}
	else {
		var radioButtons = document.getElementsByName('relation');
		radioButtons[0].checked = true;
	}
}

function checkForFilters3() {
	if (!document.getElementById("filters3").checked) {
		var radioButtons = document.getElementsByName('length'); 
		for (var i = 0; i < 2; i ++) {
			if(radioButtons[i].checked == true)
				radioButtons[i].checked = false;
		}
	}
	else {
		var radioButtons = document.getElementsByName('length');
		radioButtons[0].checked = true;
	}
}


/*
** Compute the payment based on the user input when creating a job
*/
function computePayment()
{
	
	var payment_per_sentence = document.getElementById("payment_per_sentence");
	var payment_per_job = document.getElementById("payment_per_job");

	var judgments_per_unit = document.getElementById("judgments_per_unit").value;
	var units_per_assignment = document.getElementById("units_per_assignment").value;
	var payment_per_assignment = document.getElementById("payment").value;
    var total_sentences = document.getElementById("sentences").value;	
     	
	if (judgments_per_unit != "" && units_per_assignment != "" && payment_per_assignment != "" && total_sentences != "") {
	 	payment_per_sentence.value = ((parseInt(judgments_per_unit) * (parseInt(payment_per_assignment) / parseInt(units_per_assignment))) + (parseInt(judgments_per_unit) * (parseInt(payment_per_assignment) / parseInt(units_per_assignment))) * 46.35 / 100 ) / 100 ;
		payment_per_job.value = parseInt(total_sentences) * payment_per_sentence.value;
	}

	computePaymentPerHour();
}

/*
** Compute the time based on the user input when creating a job
*/
function computeTime() {
	var seconds_per_unit = document.getElementById("seconds_per_unit").value;
	var units_per_assignment = document.getElementById("units_per_assignment").value;
	var seconds_per_assignment = document.getElementById("seconds_per_assignment");
	seconds_per_assignment.value = parseInt(seconds_per_unit) * parseInt(units_per_assignment);

	computePaymentPerHour();
}

/*
** Compute the payment per hour based on the user input when creating a job
*/
function computePaymentPerHour() {
	var payment_per_assignment = document.getElementById("payment").value;
	var seconds_per_assignment = document.getElementById("seconds_per_assignment").value;
        var payment_per_hour = document.getElementById("payment_per_hour");
        if (seconds_per_assignment != "" && payment_per_assignment != "") {
                payment_per_hour.value = ((60 * 60) / parseInt(seconds_per_assignment)) * (parseInt(payment_per_assignment) / 100);
        }
}

/*
** Call and configure the Tablesorter plugin 
*/
function configureTablesorter() {
	
	$("table.tablesorter").tablesorter(
			{
				theme : 'default',

				// hidden filter input/selects will resize the columns,
				// so try to minimize the change
				widthFixed : true,

				showProcessing : true,
				headerTemplate : '{content} {icon}',
				// initialize zebra striping and filter widgets
				widgets : [ "zebra", "filter", "stickyHeaders",
				            "resizable", 'col-reorder' ],

			 // the example to disable the sorter and filter funcitons of a particular column	            
			 // headers: { 5: { sorter: false, filter: false } },

				            widgetOptions : {

				            	zebra : [ "ui-widget-content even",
				            	          "ui-state-default odd" ],

				            	          // If there are child rows in the table (rows with class name from "cssChildRow" option)
				            	          // and this option is true and a match is found anywhere in the child row, then it will make that row
				            	          // visible; default is false
				            	          filter_childRows : true,

				            	          // if true, a filter will be added to the top of each table column;
				            	          // disabled by using -> headers: { 1: { filter: false } } OR add class="filter-false"
				            	          // if you set this to false, make sure you perform a search using the second method below
				            	          filter_columnFilters : true,

				            	          // css class applied to the table row containing the filters & the inputs within that row
				            	          filter_cssFilter : 'tablesorter-filter',

				            	          // add custom filter functions using this option
				            	          // see the filter widget custom demo for more specifics on how to use this option
				            	          filter_functions : null,

				            	          // if true, filters are collapsed initially, but can be revealed by hovering over the grey bar immediately
				            	          // below the header row. Additionally, tabbing through the document will open the filter row when an input gets focus
				            	          filter_hideFilters : false,

				            	          // Set this option to false to make the searches case sensitive
				            	          filter_ignoreCase : true,

				            	          // jQuery selector string of an element used to reset the filters
				            	          filter_reset : 'button.reset',

				            	          // Delay in milliseconds before the filter widget starts searching; This option prevents searching for
				            	          // every character while typing and should make searching large tables faster.
				            	          filter_searchDelay : 300,

				            	          // Set this option to true to use the filter to find text from the start of the column
				            	          // So typing in "a" will find "albert" but not "frank", both have a's; default is false
				            	          filter_startsWith : false,

				            	          // Filter using parsed content for ALL columns
				            	          // be careful on using this on date columns as the date is parsed and stored as time in seconds
				            	          filter_useParsedData : false,

				            	          resizable_addLastColumn : true,

				            	          stickyHeaders : 'tablesorter-stickyHeader',

				            }

			});

	
}

/*
** Add Classes to Tablesorter filter row based on the classes of header row
*/
function addClassesToFilterRow() {
	var table = $("#historytable");
	var headerRows = $(table).find(".tablesorter-headerRow").children();
	var filterRows = $(table).find(".tablesorter-filter-row").children();
	for ( var int = 0; int < headerRows.length; int++) {
		var hrow = $(headerRows[int]);
		var classname = $(headerRows[int]).attr('class').split(' ');
		if (classname[0].substring(0, 1) == 'c') {
			$(filterRows[int]).addClass(classname[0]);
		}
	}

}

/*
** Configure the filters for Tablesorter
*/
function tableFilters() {
	/*** first method *** data-filter-column="1" data-filter-text="!son"
	  add search value to Discount column (zero based index) input */
	var filters = $('table.tablesorter').find(
	'input.tablesorter-filter'), col = $(this).data(
	'filter-column'), // zero-based index
	txt = $(this).data('filter-text'); // text to add to filter

	filters.val(''); // clear all filters
	filters.eq(col).val(txt).trigger('search', false);

	/*** second method ***
	  this method bypasses the filter inputs, so the "filter_columnFilters"
	  option can be set to false (no column filters showing)
	 ******/
	/*
	var columns = [];
	columns[4] = '2?%'; // or define the array this way [ '', '', '', '2?%' ]
	$('table').trigger('search', [columns]);
	 */

}

/*
** Change the job status in History Table 
*/
function changeJobStatus() {

	alert($("option:selected", this).text()
			+ " "
			+ $(this).closest('tr')
			.children().slice(1, 2)
			.text());
	$(this).parent().parent().children(
	"td.cStatus").text(
			$("option:selected", this)
			.val());
	var selectedstatus = $(
			"option:selected", this).text();
	if (selectedstatus == "Pause") {
		var xmlRequest = $
		.ajax({
			type : 'POST',
			data : ({
				status : $(
						"option:selected",
						this).val(),
						job_id : $(this)
						.closest(
						'tr')
						.children()
						.slice(1, 2)
						.text()
			}),
			url : '/wcs/GUI/statuschange/pause_job.php'
		});

		xmlRequest
		.done(alert("CrowdFlower Status Changed and Database Updated!"));
	} else if (selectedstatus == "Resume") {
		var xmlRequest = $
		.ajax({
			type : 'POST',
			data : ({
				status : $(
						"option:selected",
						this).val(),
						job_id : $(this)
						.closest(
						'tr')
						.children()
						.slice(1, 2)
						.text()
			}),
			url : '/wcs/GUI/statuschange/resume_job.php'
		});

		xmlRequest
		.done(alert("CrowdFlower Status Changed and Database Updated!"));
	} else if (selectedstatus == "Cancel") {
		var xmlRequest = $
		.ajax({
			type : 'POST',
			data : ({
				status : $(
						"option:selected",
						this).val(),
						job_id : $(this)
						.closest(
						'tr')
						.children()
						.slice(1, 2)
						.text()
			}),
			url : '/wcs/GUI/statuschange/cancel_job.php'
		});

		xmlRequest
		.done(alert("CrowdFlower Status Changed and Database Updated!"));
	} else if (selectedstatus == "Delete") {
		var xmlRequest = $
		.ajax({
			type : 'POST',
			data : ({
				status : $(
						"option:selected",
						this).val(),
						job_id : $(this)
						.closest(
						'tr')
						.children()
						.slice(1, 2)
						.text()
			}),
			url : '/wcs/GUI/statuschange/delete_job.php'
		});

		xmlRequest
		.done(alert("CrowdFlower Status Changed and Database Updated!"));
	}

}

/*
** Pass the selected job id(s) to Analytics module and show Analytics data
*/
function passJobidtoAnalytics() {

	var arr = [];

	$(':checkbox[name=job_ids]:checked').each(function() {
		arr.push(this.value);
	});

	// $("#testjobidarray").val(arr);

	/*
	 * var xmlRequest = $.ajax({ type: 'POST', data: ({'job_ids': arr}), //
	 * url: '/wcs/set_analytics.php' url: '/wcs/testjobidarrary.php' });
	 * 
	 * xmlRequest.done( function(data) { $("#testjobidarray").val(data); //
	 * window.location="/wcs/index.php";
	 * //window.open('/wcs/set_analytics.php'); //analytics = window.open(); //
	 * analytics.parent.document.body.appendChild(data);
	 * //analytics.focus(); window.open(); document.body.appendChild(data);
	 * });
	 */

	var StatisticsForm = document.createElement("form");
	StatisticsForm.target = "Analysis";
	StatisticsForm.method = "POST";
	StatisticsForm.action = "/wcs/analytics/job.php";

	var hiddenInput = document.createElement("input");
	hiddenInput.type = "hidden";
	hiddenInput.name = "postback";
	hiddenInput.value = "1";
	StatisticsForm.appendChild(hiddenInput);

	for ( var i = 0; i < arr.length; i++) {
		var StatisticsInput = document.createElement("input");
		StatisticsInput.type = "hidden";
		StatisticsInput.name = "job_ids[]";
		StatisticsInput.value = arr[i];
		StatisticsForm.appendChild(StatisticsInput);
	}

	document.body.appendChild(StatisticsForm);

	// Statistics = window.open("", "Analysis", "status=0,title='Statistical
	// Analyses',height=700,width=1000,scrollbars=1");

	Statistics = $(this).attr('target', '_blank');

	if (Statistics) {
		StatisticsForm.submit();
	} else {
		alert('You must allow popups for this Statistics to work.');
	}

}


/*
** Open the dialog for selecting the server file 
*/
function selectServerFilePreprocessing() {
	$("#dialog-confirm-preprocessing").dialog("open");
}

/*
** Configure the dialog for selecting the server file 
*/
function selectServerFileDialogPreprocessing() {
$("#dialog-confirm-preprocessing")
.dialog(
		{
			autoOpen : false,
			resizable : true,
			height : 650,
			width : 1245,
			modal : true,
			buttons : {
				"Confirm" : confirmSelectedServerFilePreprocessing,
				Cancel : closeDialogPreprocessing
			}
		});
}

/*
** Retrieve data for the selected server file 
*/
function confirmSelectedServerFilePreprocessing() {
	$(this).dialog("close");
	// check whether users select a file
	if ($('input:radio[name=radiofilepreprocessing]:checked',
			this).closest('tr').children().slice(0,
					1).text().length == 0) {
		alert("Please select a folder with the input data!");
		$("#dialog-confirm-preprocessing").dialog("open");
	} else {
		alert($(
				'input:radio[name=radiofilepreprocessing]:checked',
				this).closest('tr').children()
				.slice(0, 1).text()
				+ ' is selected!');
		// take the name of the selected folder
                $("#foldername")
                .val(
                                $(
                                                'input:radio[name=radiofilepreprocessing]:checked',
                                                this).closest('tr')
                                                .children().slice(
                                                                0, 1)
                                                                .text())

		// take the names of the selected files
		$("#filenames")
		.val(
				$(
						'input:radio[name=radiofilepreprocessing]:checked',
						this).closest('tr')
						.children().slice(
								1, 2)
								.text())
		// take the name of the selected folder
		$("label[for='uploadedfilepreprocessing']")
		.text(
				$(
						'input:radio[name=radiofilepreprocessing]:checked',
						this).closest('tr')
						.children().slice(
								0, 1)
								.text());
	}
}

/*
** Close the dialog 
*/
function closeDialogPreprocessing() {
	$(this).dialog("close");
}

/*
** Open the dialog for selecting the server file 
*/
function selectServerFile() {
	$("#dialog-confirm").dialog("open");
}

/*
** Configure the dialog for selecting the server file 
*/
function selectServerFileDialog() {
$("#dialog-confirm")
.dialog(
		{
			autoOpen : false,
			resizable : true,
			height : 650,
			width : 1245,
			modal : true,
			buttons : {
				"Confirm" : confirmSelectedServerFile,
				Cancel : closeDialog
			}
		});
}

/*
** Retrieve data for the selected server file 
*/
function confirmSelectedServerFile() {
	$(this).dialog("close");
	// check whether users select a file
	if ($('input:radio[name=radiofile]:checked',
			this).closest('tr').children().slice(1,
					2).text().length == 0) {
		alert("Please select a file!");
		$("#dialog-confirm").dialog("open");
	} else {
		alert($(
				'input:radio[name=radiofile]:checked',
				this).closest('tr').children()
				.slice(1, 2).text()
				+ ' is selected!');
		// take the selected file id
		$("#fileid")
		.val(
				$(
						'input:radio[name=radiofile]:checked',
						this).closest('tr')
						.children().slice(
								0, 1)
								.text());
		// take the number of the sentences of the selected file
		$("#sentences")
		.val(
				$(
						'input:radio[name=radiofile]:checked',
						this).closest('tr')
						.children().slice(
								3, 4)
								.text());
		// take the name of the selected file
		$("label[for='uploadedfile']")
		.text(
				$(
						'input:radio[name=radiofile]:checked',
						this).closest('tr')
						.children().slice(
								1, 2)
								.text());
	}
}

/*
** Close the dialog 
*/
function closeDialog() {
	$(this).dialog("close");
}

/*
** Open the dialog for blocking spammers and load the spammers of the selected job 
*/
function blockSpammers() {
	$("#dialog-blockspammers").dialog("open");
	// get selected job id
	$("#spamblockjobid").val(
			$(this).closest('tr').children().slice(1, 2)
			.text());
	var xmlRequest = $.ajax({
		type : 'POST',
		data : ({
			'job_id' : $("#spamblockjobid").val()
		}),
		url : '/wcs/services/getSpammers.php'
	});         
	// load the spammers data of the selected job to the Block Spammers pop-up dialog
	xmlRequest
	.done(function(data) {
		$("#spammerfound").children().remove();
		var tbl = document
		.getElementById("spammerfound");

		addTableHeaders(tbl);

		var obj = jQuery.parseJSON(data);
		var keys = Object.keys(obj[0]);

		for (i = 0; i < obj.length; i++) {
			var row = document.createElement("tr");
			for (j = 0; j < keys.length; j++) {
				if (j == 0) {
					var cell = document
					.createElement("td");
					var labelValue = obj[i][keys[j]];
					var checkbox = document
					.createElement("input");
					checkbox.setAttribute("type",
					"checkbox");
					checkbox.setAttribute("name",
					"workerId[]");
					checkbox.setAttribute("value",
							labelValue);
					cell.appendChild(checkbox);

					var link = document
					.createElement("a");
					link.setAttribute('href',
							'/wcs/analytics/worker.php?worker_id='
							+ labelValue)
							link.setAttribute('target',
							'_blank')
							link
							.appendChild(document
									.createTextNode(labelValue));

					cell.appendChild(link);
					cell.appendChild(document
							.createElement("br"));
					row.appendChild(cell);
				} else {
					var cell = document
					.createElement("td");

					var abbr = new Array();
					abbr['CF'] = 'Content Filter';
					abbr['NO'] = "None Other";
					abbr['RT'] = "Repeated text";
					abbr['RR'] = "Repeated response";
					abbr['RND'] = "Random text";
					abbr['NR'] = "No relation";

					if (keys[j] == 'filters') {
						for (k = 0; k < obj[i][keys[j]].length; k++) {
							var filter = document
							.createElement("span");
							var filterCode = obj[i][keys[j]][k];
							filter
							.appendChild(document
									.createTextNode(filterCode
											+ ' '));
							filter
							.setAttribute(
									'title',
									abbr[filterCode]);
							cell
							.appendChild(filter);
						}
					} else {

						var cellText = document
						.createTextNode(obj[i][keys[j]]);
						cell.appendChild(cellText);
					}
					row.appendChild(cell);
				}
			}
			tbl.appendChild(row);
		}
		tbl.setAttribute("border", "1");
	});
}

/*
** Configure the dialog for blocking spammers 
*/
function blockSpammersDialog() {	
	$("#dialog-blockspammers").dialog({
		autoOpen : false,
		resizable : true,
		height : 600,
		width : 1000,
		modal : true,
		buttons : {
			"Block" : confirmBlockSpammers,
			Cancel : closeDialog
		}
	});
}

/*
** Add the table header in the Block Spammers pop-up dialog 
*/
function addTableHeaders(table) {
	var fields = [
	              {
	            	  'label' : 'ID',
	            	  'alt' : 'Worker ID'
	              },
	              {
	            	  'label' : 'Agr',
	            	  'alt' : "Worker-worker Agreement"
	              },
	              {
	            	  'label' : 'Agr. Diff',
	            	  'alt' : 'Difference between agreement of worker and average agreement of the job workers'
	              },
	              {
	            	  'label' : 'Cos',
	            	  'alt' : 'Cosine'
	              },
	              {
	            	  'label' : 'W-S Score',
	            	  'alt' : 'Worker-Sentence Score: Sentence Clarity - Cosine'
	              },
	              {
	            	  'label' : 'Annot/Sent',
	            	  'alt' : 'Annotations per Sentence'
	              },
	              {
	            	  'label' : 'Avg. Annot/Sent',
	            	  'alt' : 'avg(#Annot/Sentence[Set]) - #Annot/Sentence[Worker]'
	              },
	              {
	            	  'label' : 'Avg. Time',
	            	  'alt' : 'Average task completion time of the worker (for all the tasks she has completed)'
	              },
	              {
	            	  'label' : 'Diff Avg. Time',
	            	  'alt' : 'avg(Task completion time[Set]) - avg(Task Completion Time[Worker])'
	              },
	              {
	            	  'label' : 'Filters',
	            	  'alt' : 'Filters that have identified the worker as a possible spammer'
	              }, {
	            	  'label' : 'Channel'
	              } ];

	var row = document.createElement("tr");
	for (i = 0; i < fields.length; i++) {
		var cell = document.createElement("td");
		if (fields[i].alt) {

			var elem = document.createElement("span");
			elem.setAttribute('title', fields[i].alt)
			var cellText = document.createTextNode(fields[i].label);
			elem.appendChild(cellText)
		} else {
			var elem = document.createTextNode(fields[i].label);
		}
		cell.appendChild(elem)
		row.appendChild(cell)
	}
	table.appendChild(row)
}

/*
** Send the spammers to be blocked for further processing 
*/
function confirmBlockSpammers() {
	// $( this ).dialog( "close" );
	// alert("ok");
	// $('#myform').bind('submit', function (event) {
	// event.preventDefault();
	// var reason = document.getElementById("reason").value;
	// if (reason.length < 25) {
	// alert("The reason should have at least 25 characters!");
	// return ;
	// }
	// else {
	$.ajax({
		type : 'POST',
		url : '/wcs/crowdflower/blockusers.php',
		datatype : 'text',
		data : $("#myform").serialize(),
		success : function() {
		// window.location.reload(true);
		}
	}).done(function(msg) {
		alert(msg);
	});
	// }
	// });
	// return false;
}

/*
** Show or Hide columns of History Table
*/
function showHideColumns(event, ui) {

	if (ui.checked) {
		$("." + ui.value).show();
	} else {
		$("." + ui.value).hide();
	}

}

/*
** Shorten the display data of Channels Percentage
*/
function shortenChannelsPercentage() {
	var len = $(this).text().length;
	if (len >= 10) {
		$(this).text($(this).text().substring(0, 10));
	}
}

/*
** Show or Hide columns of History Table
*/
function checkAllColumns() {
	$("#hidecolumns > option").each(function() {
		$("." + this.value).show();
	});
}

/*
** Show or Hide columns of History Table
*/
function uncheckAllColumns() {
	$("#hidecolumns > option").each(function() {
		$("." + this.value).hide();
	});
}

/*
** Trigger to show the mage in the pop-up dialog
*/
function showImage(event) {

	event.preventDefault();
	PreviewImage($(this).attr('href'), $(this).attr('value'));

}

/*
** Configure the dialog for showing the image
*/
function PreviewImage(uri, image_name) {

	imageDialog = $("#dialog-image");
	imageTag = $('#statisticsimage');

	// Get statistics_image_id
	uriParts = uri.split("=");
	image_id = uriParts[uriParts.length - 1];

	imageTag.attr('src', uri);

	imageTag
	.load(function() {

		$("#dialog-image")
		.dialog(
				{
					resizable : true,
					height : 'auto',
					width : 'auto',
					modal : true,
					title : image_name,
					buttons : {
						"Download" : function() {
							$(this).dialog("close");
							window
							.open("http://crowd-watson.nl/wcs/services/getFile.php?id="
									+ image_id);
						},
						Cancel : function() {
							$(this).dialog("close");
						}
					}
				});
	});

}

/*
** Configure Back to top button
*/
(function($) {

	$.scrollUp = function(options) {

		// Defaults
		var defaults = {
				scrollName : 'scrollUp', // Element ID
				topDistance : 80, // Distance from top before showing element (px)
				topSpeed : 300, // Speed back to top (ms)
				animation : 'fade', // Fade, slide, none
				animationInSpeed : 200, // Animation in speed (ms)
				animationOutSpeed : 200, // Animation out speed (ms)
				scrollText : 'Back to top', // Text for element
				scrollImg : false, // Set true to use image
				activeOverlay : false
				// Set CSS color to display scrollUp active point, e.g '#00FFFF'
		};

		var o = $.extend({}, defaults, options), scrollId = '#' + o.scrollName;

		// Create element
		$('<a/>', {
			id : o.scrollName,
			href : '#top',
			// title: o.scrollText
		}).appendTo('body');

		// If not using an image display text
		if (!o.scrollImg) {
			$(scrollId).text(o.scrollText);
		}

		// Minium CSS to make the magic happen
		$(scrollId).css({
			'display' : 'none',
			'position' : 'fixed',
			'z-index' : '2147483647'
		});

		// Active point overlay
		if (o.activeOverlay) {
			$("body").append("<div id='" + o.scrollName + "-active'></div>");
			$(scrollId + "-active").css({
				'position' : 'absolute',
				'top' : o.topDistance + 'px',
				'width' : '100%',
				'border-top' : '1px dotted ' + o.activeOverlay,
				'z-index' : '2147483647'
			});
		}

		// Scroll function
		$(window).scroll(
				function() {
					switch (o.animation) {
					case "fade":
						$(($(window).scrollTop() > o.topDistance) ? $(scrollId)
								.fadeIn(o.animationInSpeed) : $(scrollId)
								.fadeOut(o.animationOutSpeed));
						break;
					case "slide":
						$(($(window).scrollTop() > o.topDistance) ? $(scrollId)
								.slideDown(o.animationInSpeed) : $(scrollId)
								.slideUp(o.animationOutSpeed));
						break;
					default:
						$(($(window).scrollTop() > o.topDistance) ? $(scrollId)
								.show(0) : $(scrollId).hide(0));
					}
				});

		// To the top
		$(scrollId).click(function(event) {
			$('html, body').animate({
				scrollTop : 0
			}, o.topSpeed);
			event.preventDefault();
		});

	};
})(jQuery);


/*
** Main line of running js functions
*/
$(function() {
	
	/* trigger jQuery UI tabs */
	$("#tabs").tabs();
	
	/* trigger jQuery UI accordion */
	$("div#accordion").accordion({collapsible : true, heightStyle : "content",});
	
	/* trigger Back to top button */
	$.scrollUp();
	
	/* trigger Galleria to have slideshow */
	Galleria.loadTheme('/wcs/GUI/plugins/galleria/themes/classic/galleria.classic.min.js');
	Galleria.run('#galleria');
	
	/* load the preprocessing interface to GUI Input tab */
//	$("#preprocessarea").load("/wcs/preprocessing/preprocinterface.php");
	
	/* trigger Tablesorter plugin */
	configureTablesorter();
	addClassesToFilterRow();

	/* trigger Tablesorter filter function */
	$('button.search').click(tableFilters);
	
	/* to change job status in History Table */
	$(".changeStatus").change(changeJobStatus);

	/* send the selected job id(s) to the analytics module */
	$('#passjobid').click(passJobidtoAnalytics);
    
	/* show the dialog for selecting the server file */
	$("#uploadedfile").click(selectServerFile);
	selectServerFileDialog();

	/* show the dialog for selecting the server file */
        $("#uploadedfilepreprocessing").click(selectServerFilePreprocessing);
        selectServerFileDialogPreprocessing();

	
	/* show the dialog for blocking spammers */
	$(".blockspammers").click(blockSpammers);
	blockSpammersDialog();
	
	/* hide or show columns in the History tab */
	$("#hidecolumns").multiselect({click : showHideColumns});
	
	// by default, showing all the columns
	$("#hidecolumns").multiselect("checkAll");
    
	// show channel percentage data with the shorter length
	$('td.cChannelsPercentage').load(shortenChannelsPercentage);
    
	// to show all the columns
	$('.ui-multiselect-all').click(checkAllColumns);
    
	// to hide all the columns except the job id
	$('.ui-multiselect-none').click(uncheckAllColumns);
	
	/* the trigger for the pop-up dialog containing the statistics image */
	$('#showimage').click(showImage);
		
});
