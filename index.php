<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width"/>
<title>Flyby Checker</title>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
<style>
.form-control {width: 32.5%; display: inline-block;}
.btn-primary {margin: 20px 0;}
th:nth-child(3), td:nth-child(3) {text-align: right;}
</style>
</head>
<body style="background-color: #444;">
<div class="container" style="background-color: #eee; padding: 40px; margin: 40px auto; max-width: 440px;">
<form id="flyby_cal_form">
<select name="f_month" id="f_month" class="form-control">
<?php for ( $m = 1; $m <13; $m++ ): ?>
    <option value="<?php echo sprintf('%02d', $m) ?>" <?php echo ( date('n') == $m ? 'selected' : '') ?>><?php echo sprintf('%02d', $m) ?></option>
<?php endfor; ?>
</select>
<select name="f_day" id="f_day" class="form-control">
<?php for ( $d = 1; $d <32; $d++ ): ?>
    <option value="<?php echo sprintf('%02d', $d);  ?>" <?php echo ( date('d') == $d ? 'selected' : '') ?>><?php echo sprintf('%02d', $d) ?></option>
<?php endfor; ?>
</select>
<select name="f_year" id="f_year" class="form-control">
<?php for ( $y = 1900; $y <2101; $y++ ): ?>
    <option value="<?php echo $y  ?>" <?php echo ( date('Y') == $y ? 'selected' : '') ?>><?php echo $y ?></option>
<?php endfor; ?>
</select>
</form>
<p>
<button id="submit_flyby_cal" class="btn btn-primary btn-block" onclick="">Check Flybys</button>
</p>
<div id="flyby_results">
</div>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
<script type='text/javascript'>
(function($){
    
    function isValidDate(d) {
        return d instanceof Date && !isNaN(d);
    }

    $("#submit_flyby_cal").click(function(){
        var sub_date = $("#f_year").val()+"-"+$("#f_month").val()+"-"+$("#f_day").val();
        var results = checkFlyby( sub_date );
    });

    function checkFlyby( sub_date ) {
        //var url = "http://cheezoid.com/woo/wp-json/myroute/v1/products/62";
        //var url = "http://cheezoid.com/woo/wp-json/myroute/v1/products/";
        var url = "https://api.nasa.gov/neo/rest/v1/neo/3542519";
        url = "https://api.nasa.gov/neo/rest/v1/neo/browse";
        url = "https://api.nasa.gov/neo/rest/v1/feed";

        var jqxhr = $.ajax( {
            url: url,
            method: "GET",
            dataType: "json",
            data: { api_key: "9VoNUwJGlOgkfrPMs28USbWwBNYLKseOC4ojouKg", start_date: sub_date, end_date: sub_date }
        } )
        .done(function( json ) {
            var element_count = json['element_count'];
            console.log(json);
            $("#flyby_results").html("");
            if ( element_count > 0 ) {
                var name, size, dist;
                var near_earth_objects = json['near_earth_objects'][sub_date];
                var table = document.createElement('table');
                var tbody = document.createElement('tbody');
                $(table).prop("class", "table table-bordered table-striped");
                $(table).append('<thead><tr><th>Name</th><th>Size (ft)</th><th>Dist (mi)</th></tr></thead>');
                $(table).append(tbody);
                $("#flyby_results").append('<p>Flybys for '+sub_date+'</p>');
                $("#flyby_results").append(table);
                for ( n = 0; n < element_count; n++ ) {
                    name = near_earth_objects[n]['name'];
                    size = Math.round(near_earth_objects[n]['estimated_diameter']['feet']['estimated_diameter_max']);
                    dist = Math.round(near_earth_objects[n]['close_approach_data'][0]['miss_distance']['miles']);
                    //console.log(near_earth_objects[n]);
                    $(table).append('<tr><td>'+name+'</td><td>'+size+'</td><td>'+dist.toLocaleString()+'</td></tr>');
                }
                
            } else {
                $("#flyby_results").html('<p style="color:#080">No flybys today.</p>');
            }
        })
        .fail(function() {
            //alert( "error" );
            $("#flyby_results").html('<p style="color:#f00">NASA API Error - Invalid date maybe?</p>');
        });

        return;
    }

})(jQuery);
</script>
</body>
</html>