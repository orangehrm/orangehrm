function visualizePieChart(data, graph, properties, divId) {
    var plottedData = [];

    var showLegend = false;
    var interactive = false;
    var suffixForValueHover ="";
    
    if (properties['show-legend']) {
        showLegend = true;
    }
    if (properties['interactive']) {
        interactive = true;
    }
    if (properties['suffixForValueHover']) {
        suffixForValueHover = " "+properties['suffixForValueHover'];
    }
    var chartOptions = {
        series: {
            pie: {
                show: true
            }
        },
        legend: {
            show: showLegend
        },
        grid: {
            hoverable: interactive
        }
    };    
    
    if (properties['show-labels']) {
        var legendLabel = {
            series: {
                pie: {
                    radius: 1,
                    label: {
                        show: true,
                        radius: 2/3,
                        formatter: function(label, series){
//                            var percent = Math.round(series.percent);                            
//                            var value = series.datapoints.points[1] + ' ('+ percent + '%)';
//                            return '<div style="font-size:x-small;text-align:center;padding:2px;color:' + series.color + '">'+label+'<br/>'+value+'</div>';
 return '<div style="font-weight:bold;font-size:8pt;text-align:center;padding:2px;color:white;">'+Math.round(series.percent)+'%</div>';
                        },
                        
                    threshold: 0.1
                    }
                }
            }
        }; 
        
        $.extend(true, chartOptions, legendLabel);
    }
    
    if( graph.legend.legendDivId){
        legendDivId = graph.legend.legendDivId;        
    }
    
    if (graph.legend.useSeparateContainer) {
        var legendContainer = {
            legend: {
                container: '#' + legendDivId + '_legend'
            }
        }; 
        
        $.extend(true, chartOptions, legendContainer);        
    }
          
    for (var idx in data) {
        var item = {
            label: $('<div>').text(idx.toString()).html(), 
            data: data[idx]
            };
        plottedData.push(item);
    }

    $.plot($("#" + divId), plottedData, chartOptions);
    
    $("#" + divId).bind("plothover", pieHover);

 
    function pieHover(event, pos, obj) {
        if (!obj){
            $("#hover_"+divId).html("");
            return;
        }
        value = obj.series.datapoints.points[1];
        percent = parseFloat(obj.series.percent).toFixed(2);
        $("#hover_"+divId).html('<span style="font-weight: bold; color: '+obj.series.color+'">'+obj.series.label+': '+value+suffixForValueHover+' ('+percent+'%)</span>');
    }
    
    
}

