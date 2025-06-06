import {afterCalculateByError, setErrorMessage} from "./calculate";
import {notifyComplete} from "./notify";
import {Chart, registerables, ChartConfiguration, ChartDataset} from "chart.js";
import katex from "katex";
import jsrenderWrapper from '../plugins/jsrender-wrapper.js';
Chart.register(...registerables);

let myChartNashSocialWelfare: Chart | undefined;

interface NashData {
    alpha_1: {
        numerator: string;
        denominator: string;
    };
    alpha_2: {
        numerator: string;
        denominator: string;
    };
    beta_1: {
        numerator: string;
        denominator: string;
    };
    beta_2: {
        numerator: string;
        denominator: string;
    };
    rho: {
        numerator: string;
        denominator: string;
    };
}

interface RenderParams {
    line: RenderParam[];
    dot: RenderParam[];
}

interface RenderParam {
    title: string;
    display_text: string;
    x: number;
    y: number;
}

interface ReportParam {
    a_rho: string;
}

export function doNashCalculate(): void
{
    const data: NashData = {
        alpha_1: {
            numerator: String($('#alpha_1_numerator').val() || ''),
            denominator: String($('#alpha_1_denominator').val() || ''),
        },
        alpha_2: {
            numerator: String($('#alpha_2_numerator').val() || ''),
            denominator: String($('#alpha_2_denominator').val() || ''),
        },
        beta_1: {
            numerator: String($('#beta_1_numerator').val() || ''),
            denominator: String($('#beta_1_denominator').val() || ''),
        },
        beta_2: {
            numerator: String($('#beta_2_numerator').val() || ''),
            denominator: String($('#beta_2_denominator').val() || ''),
        },
        rho: {
            numerator: String($('#rho_numerator').val() || ''),
            denominator: String($('#rho_denominator').val() || ''),
        },
    };
    $.ajax({
        type: 'POST',
        url: '/api/nash/calculate',
        data: data,
        success: function (data: {
            render_params: RenderParams,
            report_params: ReportParam
        }) {
            $('button.calculate').removeClass('disabled');
            renderNashSimulationChart(data.render_params);
            renderNashReportArea(data.report_params);
            $('#nash_spinner').hide();
            notifyComplete();
        },
        error: function (data: JQuery.jqXHR) {
            setErrorMessage($.parseJSON(data.responseText));
            let alertOffset = $('#alert_danger').offset();
            let titleOffset = $('.kt-pagetitle').offset();
            if (alertOffset && titleOffset) {
                let offset = alertOffset.top - titleOffset.top - 20;
                $("html,body").animate({scrollTop: offset});
            }
            afterCalculateByError('nash_spinner');
        }
    });
}

/**
 * レポートエリアの出力を行う
 * @param report_params
 */
function renderNashReportArea(report_params: ReportParam): void
{
    const tmpl  = jsrenderWrapper.render('#nashResultTemplate', {
        a_rho: report_params.a_rho,
    });
    $('#nash_result').html(tmpl);
    $('#nash_result .katex_exp').each(function () {
        let element = $(this)[0];
        katex.render(<string>$(this).attr('expression'), element, {
            throwOnError: false
        });
    });
}

/**
 * チャートの出力を行う
 * @param render_params
 */
function renderNashSimulationChart(render_params: RenderParams): void
{
    $('#chart_area_nash').show();
    let ctx_simulation = document.getElementById('chart_nash_social_welfare') as HTMLCanvasElement;
    if(myChartNashSocialWelfare) {
        myChartNashSocialWelfare.destroy();
        $('#chart_nash_social_welfare').removeAttr('width');
    }
    myChartNashSocialWelfare = new Chart(
        ctx_simulation,
        getNashSimulationOption(
            render_params
        )
    );
}

/**
 * チャートのパラメータ生成を行う
 * @param render_params
 */
function getNashSimulationOption(render_params: RenderParams): ChartConfiguration
{
    const border_color = '#324463';

    let line_data_array: RenderParam[] = [];
    let dot_data_array: RenderParam[] = [];
    $.each(render_params.line, function(index: number, val: RenderParam)  {
        line_data_array.push({
            title: val.title,
            display_text: val.display_text,
            x: val.x,
            y: val.y,
        });
    });
    $.each(render_params.dot, function (index: number, val: RenderParam) {
        dot_data_array.push({
            title: val.title,
            display_text: val.display_text,
            x: val.x,
            y: val.y,
        });
    });

    const datasets: ChartDataset[] = [
        {
            type: 'line',
            label: 'Nash Line',
            data: line_data_array,
            borderColor: border_color,
            backgroundColor: border_color,
            segment: {
                borderDash: function(context: any) {
                    if (
                        context.p0.raw.title === 'gamma2' ||
                        context.p1.raw.title === 'rho beta' ||
                        context.p0.raw.title === 'alpha' ||
                        context.p1.raw.title === 'gamma1'
                    ) {
                        return [5, 5];
                    }
                    return undefined;
                }
            },
            borderWidth: 2,
            tension: 0, // lineTension is deprecated, using tension instead
            pointRadius: 2,
            fill: false,
            showLine: true
        },
        {
            type: 'scatter',
            label: 'Nash Dot',
            data: dot_data_array,
            borderColor: border_color,
            backgroundColor: border_color,
            borderWidth: 2,
            pointRadius: 2,
            fill: false,
        },
    ];
    return {
        type: "scatter",
        data: {
            datasets: datasets,
        },
        options: {
            responsive: true,
            animation: false,
            scales: {
                y: {
                    position: "left",
                    beginAtZero: true,
                    min: 0,
                    // max: max_y_axes,
                    title: {
                        display: false,
                    }
                },
                x: {
                    beginAtZero: true,
                    title: {
                        display: false,
                    }
                }
            },
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    mode: 'nearest',
                    displayColors: false,
                    intersect: false,
                    callbacks: {
                        title: function(tooltipItems) {
                            return (tooltipItems[0].raw as RenderParam).title + ':';
                        },
                        label: function(tooltipItem) {
                            return (tooltipItem.dataset.data[tooltipItem.dataIndex] as RenderParam).display_text;
                        }
                    }
                }
            }
        },
        plugins: [
            {
                id: 'backgroundPlugin',
                beforeDraw: drawBackground
            },
        ]
    }
}

function drawBackground(target: Chart): void {
    let cvs = document.getElementById(target.canvas.id) as HTMLCanvasElement;
    let ctx = cvs.getContext('2d')!;

    // プロット領域に重なるように、背景色の四角形を描画
    ctx.fillStyle = "white";              // 背景色（今回は濃いグレー）
    ctx.fillRect(0, 0, target.width, target.height);
}
