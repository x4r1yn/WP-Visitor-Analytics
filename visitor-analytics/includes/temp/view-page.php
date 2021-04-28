<div class="wrap">
	<h1 style="font-size:35px;"><?php esc_html_e( get_admin_page_title() ); ?></h1>
	<div class="admin-account">
		<?php
			global $wpdb;
			$result = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix ."visitor_analytics");
		 ?>
	  <div class="title">	<h3><strong>Total Site Visitors:</strong> <?php echo sizeof($result); ?></h3></div>
		<form action="" method="get" class="header_form">
			<input type="hidden" name="page" value="proweaver_visitor_analytics"/>
	    <div>
				<label class="year-label">Select Year: </label>
					<select name="year" class="form-control year">
						<?php for ($all_year = date('Y'); $all_year >= $starting_year; $all_year--){ ?>
							<option value="<?php echo $all_year; ?>" <?php echo ($all_year == $year)?'selected':''; ?>><?php echo $all_year; ?></option>
						<?php } ?>
					</select>
					<div class="buttons">
						<button type="submit" class="btn btn-primary btn-block btn-rounded z-depth-1">Overall Results</button>
						<button type="submit" class="btn btn-info btn-block btn-rounded z-depth-1" name="view_visitors">Site Visitors</button>
					</div>
	    </div>
		</form>
	  <br/>
	  <?php if (isset($_GET['view_visitors'])): ?>
		  <div class="gen_pdf">
			  <button class="btn btn-indigo btn-block btn-rounded" type="button" name="gen_pdf">Generate Reports <span class="dashicons dashicons-chart-line" style="margin: -3px -11px 0px 10px;"></span></button>
			  <div id="pdf_options" class="" style="display:none; position:relative">
				  <div class="checkbox boxes">
					  <div class="checkbox_container">
						  <input type="checkbox" class="pdf_option" id="main_table"  data-option="main_table" checked>
						  <label for="main_table">Main Visitor</label>
						  <div class="">
							  <span class="dashicons pdf-tooltip dashicons-info"></span>
							  <div class="option_tooltip">
								  Main visitors table (visitors overall information)
							  </div>
						  </div>

						  <input type="checkbox" class="pdf_option" id="time_table"  data-option="time_table" checked>
						  <label for="time_table">Time Duration</label>
						  <div class="">
							  <span class="dashicons pdf-tooltip dashicons-info"></span>
							  <div class="option_tooltip">
								  Time duration table w/ chart
							  </div>
						  </div>

						  <input type="checkbox" class="pdf_option" id="country_table"  data-option="country_table" checked>
						  <label for="country_table">Per Country</label>
						  <div class="">
							  <span class="dashicons pdf-tooltip dashicons-info"></span>
							  <div class="option_tooltip">
								  Visitors per country data table w/ chart
							  </div>
						  </div>

						  <input type="checkbox" class="pdf_option" id="state_table" data-option="state_table" checked>
						  <label for="state_table">Per State</label>
						  <div class="">
							  <span class="dashicons pdf-tooltip dashicons-info"></span>
							  <div class="option_tooltip">
								  List of states per country table
							  </div>
						  </div>
					  </div>

					  <button id="gen_pdf" class="btn_genpdf" type="button" name="button">Generate</button>
					</div>
			  </div>
		  </div>
	  <?php endif; ?>
<div class="table-container">
	<?php if (!isset($_GET['view_visitors'])): ?>

		<h3 class="sv_overall_graph_lbl"><?php echo $year ?> Graph</h3>
			<div id="graph">
					<script type="text/javascript">
						var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
					</script>

					<?php
						$graph = array($year => array());

						for ($i=1; $i <= 12; $i++) {
							$month = $i;
							if ($month < 10) {
								$month = '0'.$month;
							}
							$result = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix ."visitor_analytics WHERE count_date like '".$year."-".$month."%'");
							array_push($graph[$year], sizeof($result));
						}

						$decoded = json_encode($graph);
						foreach ($graph[$year] as $value) {
							$cnt+=$value;
						}

						if($cnt != 0){
					?>

						<div style="margin:0 auto; width:90%">
						  <canvas id="myChart<?php echo $year; ?>" style="background: #f5f5f5; border: 1px solid #a1a1a1;"></canvas>
						</div>

						<script type="text/javascript">
						var ctx = document.getElementById("myChart<?php echo $year; ?>").getContext('2d');
						var all_counter = JSON.parse('<?php echo $decoded; ?>');
						var myChart<?php echo $year; ?> = new Chart(ctx, {
							type: 'bar',
							data: {
								labels: months ,
								datasets: [{
									label: '<?php echo $year; ?> Site Visitors',
									data: all_counter['<?php echo $year; ?>'],
									backgroundColor: [
										'rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(2, 216, 168, 0.4)', 'rgba(251, 186, 63, 0.56)', 'rgba(130, 245, 150, 0.53)',
										'rgba(54, 162, 235, 0.2)', 'rgba(255, 99, 132, 0.2)', 'rgba(2, 216, 168, 0.4)', 'rgba(224, 178, 247, 0.5)', 'rgba(130, 245, 150, 0.53)', 'rgba(251, 186, 63, 0.56)', 'rgba(255, 99, 132, 0.2)'
									],
									borderColor: [
										'rgba(255,99,132,1)', 'rgba(54, 162, 235, 1)', '#05ad87', '#da9e2d', '#38c551',
										'rgba(54, 162, 235, 1)', 'rgba(255,99,132,1)', '#05ad87', '#7c20ab', '#38c551', '#da9e2d', 'rgba(255,99,132,1)'
									],
									borderWidth: 1,
								}]
							},
							options: {
								layout: {
							        padding: {
							            left: 20,
							            right: 20,
							            top: 20,
							            bottom: 20
							        }
							    },
									 scales: {
											 yAxes: [{
												 scaleLabel: {
										       display: true,
										       labelString: "Visitor Count"
										     },
													 ticks: {
															 beginAtZero:true,
															 callback: function (value) { if (Number.isInteger(value)) { return value; } },
												       stepSize: 1,
															 fontStyle: 'bold'
													 }
											 }],
											 xAxes: [{
													ticks: {
															fontStyle: 'bold'
													}
											}]
									 }
							 },
						});
						</script>
					<?php }else{ ?>
						<br/><hr/>
							<p class="d-block bg-danger text-white no-data">No data found in <?php echo $year; ?>.</p>
					<?php } ?>
						<hr/><br/>

				</div>
				<div id="mapid" style="height:256px;"></div>

		<?php elseif(isset($_GET['view_visitors'])): ?>
			<?php
				$months = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
			 ?>
				<div class="table-title">
					<div class="loader" style="display:none;">
						<div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
					</div>
					<div class="search-option" >
						<label style="vertical-align:super;">Search By:</label>
						<select class="selectby form-control" name="selectby">
							<option value="Date"  selected>Date Range</option>
							<option value="Month">Select Month(s)</option>
						</select>
					</div>
					<div class="search_month"  style="display:none;">
						<h3> <?php echo $year." Site Visitors"; ?> </h3>
		 			 <?php foreach ($months as $key => $value): ?>
		 				 <div class="custom-control custom-checkbox sv_checkbox">
		 					 <input type="checkbox" class="custom-control-input month" id="<?php echo $value ?>" data-mnum="<?php echo $key+1; ?>">
		 					 <label class="custom-control-label" for="<?php echo $value ?>"><?php echo $value ?></label>
							 <div class="sv_select"></div>
		 				 </div>
		 			 <?php endforeach; ?>
					 <div class="custom-control custom-checkbox sv_checkbox clear_months" style="width:27px;">
						 <label class="custom-control-label" for="clear_months">&#x21ba;</label>
						 <div class="sv_select"></div>
					 </div>
				 </div>
				 <div class="search_date">
					 <h3> <?php echo $year." Site Visitors"; ?> </h3>
					 <form id="range_date" class="by_range" action="" method="POST" autocomplete="off">
							 <div class="date-input">
								 <label for="">
									 From:
								 </label>
								 <input type="text" name="From_date" class="va-input-datepicker" placeholder="" aria-controls="table_visitors" placeholder="Pick a date" required>
							 </div>
							 <div class="date-input">
								 <label for="">
									 To:
								 </label>
								 <input type="text" name="To_date" class="va-input-datepicker" placeholder="" aria-controls="table_visitors" placeholder="Pick a date" required>
							 </div>
							 <input class="btn btn-elegant btn-block btn-rounded" type="submit" name="submit" value="Go">
					 </form>
				 </div>

				</div>
				<hr>
				<div id="visitor_result">
				</div>

		<?php endif; ?>
	</div>

	<script type="text/javascript">

		$(document).ready(function(){
			getajax();
		});

		$('.clear_months').on('click',function(e){
			$('.sv_checkbox').each(function(){
				$(this).removeClass('selected');
			});
			getajax();
		});

		$('#range_date').on('submit',function(e){
			e.preventDefault();
			var range = $(this).serialize();
			getajax(0,range);
		})

		$('.sv_select').on('click',function(){
			$(this).siblings('.month').trigger('click');
		});

		$('.month').on('click',function(){
			$(this).parent('.sv_checkbox').toggleClass( "selected" );
			var months = [];
			$('.month:checked').each(function(){
				months.push($(this).data('mnum'));
			});
			getajax(months);
		});

		$('button[name="gen_pdf"]').on('click',function(){
			$('#pdf_options').toggleClass('show');
		});

		$('#gen_pdf').on('click',function(e){
			e.preventDefault();
			$.ajax({
				url : '../wp-content/plugins/visitor-analytics/includes/temp/getData.php',
				beforeSend: function(){
					$('#gen_pdf').html('Please wait..');
				},
				success : function(res){
					genPDF(JSON.parse(res));
				},
				complete: function(){
					// $('#gen_pdf').html('Success!');
					$('#gen_pdf').html('Generate');
				}
			});
		});

		function genPDF(data) {
			var vcolumns = ["IP Address","State / Region","Country", "Date", "Time Duration"]; //visitor columns
			var tcolumns = ["Time Duration","Visitor Count"]; //time duration columns
			var ccolumns = ["Country","Visitor Count"]; //country duration columns
			var scolumns = ["State", "Visitor Count"];
			var doc = new jsPDF('p', 'pt');

			/**
			V_DATA = VISITOR TABLE DATA
			T_DATA = TIME DURATION TABLE DATA
			C_DATA = COUNTRY VISITORS DATA
			**/

			//GET PDF OPTIONS
			var pdf_options = {};

			$('.pdf_option:checked').each(function(){
				pdf_options[$(this).data('option')] = 'true';
			});

			var page_count = 0;

			//Pdf Structure 	#PAGE 1
			if (pdf_options.main_table) {
				doc.setFontSize(20);
				doc.text(20,40,'Visitor Analytics');
				doc.setFontType('Bold');
				doc.setFontSize(10);
				doc.text(500,40,'Total visitors: '+<?php echo sizeof($result); ?>);
				doc.autoTable(vcolumns,data.v_data,{
					margin: { top: 45, left: 20, right: 20, bottom: 45 },
				});
				page_count += 1;
			}

			//						#PAGE 2
			if (pdf_options.time_table) {
				if (page_count != 0) {
					doc.addPage();
				}
				doc.setFontSize(20);
				doc.text(20,40,'TIME DESCRIPTION');
				doc.addImage($('#myChartTime')[0].toDataURL(), 'JPEG', 10, 60,560,300);
				doc.autoTable(tcolumns,data.t_data,{
					margin: { top: 370, left: 20, right: 20, bottom: 20 },
				});
				page_count += 1;
			}

			//						#PAGE 3
			if (pdf_options.country_table) {
				if (page_count != 0) {
					doc.addPage();
				}
				doc.text(20,40,'COUNTRIES');
				doc.addImage($('#myChartCountry')[0].toDataURL(), 'JPEG', 10, 60,560,300);
				doc.autoTable(tcolumns,data.c_data,{
					margin: { top: 380, left: 20, right: 20, bottom: 20 },
				});
				page_count += 1;
			}

			//STATES
			if (pdf_options.state_table) {
				doc.setFontSize(15);
				$.each(data.c_states, function(key,value){
					if (page_count != 0) {
						doc.addPage();
					}
					doc.text(20,40, key.toUpperCase());
					doc.autoTable(scolumns,value,{
						margin: { top: 45, left: 20, right: 20, bottom: 45 },
					});
					page_count += 1;
				})
			}

			//						#PAGE FOR (US)
			doc.setFontSize(20);
			if ($('#myChartState').length) {
				if (page_count != 0) {
					doc.addPage();
				}
				doc.text(20,40, 'UNITED STATES CHART');
				doc.addImage($('#myChartState')[0].toDataURL(), 'JPEG', 10, 60,565,300);
			}

			//Save file
			doc.save('Visitor Analytics.pdf');
		}

		function getajax(months, range){
			var year = '<?php echo $year; ?>';
			var data = {months, year};
			if (range) {
				data = range;
			}

			$.ajax({
				url : '../wp-content/plugins/visitor-analytics/includes/temp/site_visitors.php',
				type : 'POST',
				data : data,
				beforeSend: function() {
						$('#visitor_result').empty();
		        $('div.loader').fadeIn();
		    },
				success : function(response){
					$('#visitor_result').append(response);
				},
				complete: function(e){
					$('#table_visitors').DataTable({
							 "order": [[3,"desc"]],
							 "responsive": true
					});
					$('div.loader').fadeOut();
				}
			});
		}

	</script>
</div>
