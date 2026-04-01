import Highcharts from 'highcharts';
import Highcharts3D from 'highcharts/highcharts-3d';
import Exporting from 'highcharts/modules/exporting';

Highcharts3D(Highcharts);

Exporting(Highcharts);

window.Highcharts = Highcharts;
