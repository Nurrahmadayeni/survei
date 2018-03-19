<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Simple Geo Map - Chart Map</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css">
	<script type="text/javascript" charset="utf8" src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.8.2.min.js"></script>
	<script src="https://code.highcharts.com/highcharts.js"></script>
    
  </head>
  <body>
  		<h4 class='text-center'>Survey {{$survey->title}}</h4>  			
  	<?php 

  			$i = 1;
			foreach ($data as $dataa) {
				echo "
					<div class='col-md-6'>
						<h5 class='text-center'>".$dataa['question']."</h5>
			      		<div id='container".$i."'> </div>
			    	</div>";

				// var_dump($dataa['choices']);
			    ?>

			    <script>
				    $(function () { 
				        $("#container"+<?=$i?>).highcharts({
					        chart: {
					            plotBackgroundColor: null,
					            plotBorderWidth: 1,//null,
					            plotShadow: false
					        },
					        title: {
					            text: ''
					        },
					        tooltip: {
					            pointFormat: '{series.name}: <b>{point.percentage:.2f}%</b>'
					        },
					        plotOptions: {
					            pie: {
					                allowPointSelect: true,
					                cursor: 'pointer',
					                dataLabels: {
					                    enabled: true,
					                    format: '<b>{point.name}</b>: {point.percentage:.2f} %',
					                    style: {
					                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
					                    }
					                },
									showInLegend: true
					            }
					        },
					        series: [{
					            type: 'pie',
					            name: 'Browser share',
					            data: <?php echo json_encode($dataa['choices']);?>
					        }]
					    });
				    });
				</script>   

			<?php
			$i++;
			}?>
  </body>
</html>