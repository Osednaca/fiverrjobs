<html>
  <head>
    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript">
    
    // Load the Visualization API and the piechart package.
    google.load('visualization', '1', {'packages':['corechart']});
      
    // Set a callback to run when the Google Visualization API is loaded.
    google.setOnLoadCallback(drawChart);
      
    function drawChart() {
      var jsonData = $.ajax({
          url: "getData.json", //Change this for the url where is allocated the json or php file
          dataType:"json",
          async: false
          }).responseText;

      jsonData = JSON.parse(jsonData);

      $("#extra_info").html("<b>ETime: "+jsonData.ETime+"</b>");
      var jsonarray = [];
      jsonarray[0] = ['Stroj', 'Produkcia', { role: 'annotation' },'Odpad'];
      if(jsonData.EYield.item.length != undefined) {
        var j = 1;
        for (var i = 0; i < jsonData.EYield.item.length; i++) {
          if(i  % 3 === 0  ){
            jsonarray[j] = [jsonData.EYield.item[i].Stroj, parseFloat(jsonData.EYield.item[i].Produkcia),jsonData.EYield.item[i].Produkcia,parseFloat(jsonData.EYield.item[i].Odpad)];
          }
          else{
            jsonarray[j] = [jsonData.EYield.item[i].Stroj, parseFloat(jsonData.EYield.item[i].Produkcia),"",parseFloat(jsonData.EYield.item[i].Odpad)];
          }
          j++;
        };
      }else{
        jsonarray[1] = [jsonData.EYield.item["Stroj"], parseFloat(jsonData.EYield.item["Produkcia"]),jsonData.EYield.item[i].Produkcia,parseFloat(jsonData.EYield.item["Odpad"])];
      }
      console.log(jsonarray);

      var data = google.visualization.arrayToDataTable(jsonarray);

      var view = new google.visualization.DataView(data);

  var options = {
    title : 'Strojne vyroba',
    hAxis: {title: "Stroj"},
    seriesType: "bars",
    series: {5: {type: "line"}}
  };

  var chart = new google.visualization.ComboChart(document.getElementById('chart_div'));
  chart.draw(view, options);
    }

    </script>
  </head>

  <body>
    <!--Div that will hold the pie chart-->
    <div id="chart_div"></div>
    <div id="extra_info" style='color:red; text-align:center;'></div>
  </body>
</html>