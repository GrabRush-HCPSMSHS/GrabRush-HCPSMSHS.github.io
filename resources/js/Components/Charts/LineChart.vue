<script setup>
import { onMounted } from 'vue'
import ApexCharts from 'apexcharts'
import { formatNumber } from '@/helpers'

const props = defineProps({
    chartData: Array,
    valueKey: String,  // Key for the value to be used in the chart
    labelKey: String,  // Key for the label to be used on the x-axis
})

onMounted(() => {
    if (document.getElementById("data-series-chart") && typeof ApexCharts !== 'undefined') {
        const chart = new ApexCharts(document.getElementById("data-series-chart"), options)
        chart.render()
    }
})

const totalValue = formatNumber(props.chartData.reduce((total, item) => total + item[props.valueKey], 0))

const options = {
    series: [
        {
            name: "Value",
            data: props.chartData.map(item => item[props.valueKey]),
            color: "#1A56DB",
        },
    ],
    chart: {
        height: "100%",
        maxWidth: "100%",
        type: "area",
        fontFamily: "Inter, sans-serif",
        dropShadow: {
            enabled: false,
        },
        toolbar: {
            show: false,
        },
    },
    tooltip: {
        enabled: true,
        x: {
            show: false,
        },
    },
    legend: {
        show: false
    },
    fill: {
        type: "gradient",
        gradient: {
            opacityFrom: 0.55,
            opacityTo: 0,
            shade: "#1C64F2",
            gradientToColors: ["#1C64F2"],
        },
    },
    dataLabels: {
        enabled: false,
    },
    stroke: {
        width: 6,
    },
    grid: {
        show: false,
        strokeDashArray: 4,
        padding: {
            left: 2,
            right: 2,
            top: 0
        },
    },
    xaxis: {
        categories: props.chartData.map(item => item[props.labelKey]),
        labels: {
            show: true,
        },
        axisBorder: {
            show: true,
        },
        axisTicks: {
            show: true,
        },
    },
    yaxis: {
        show: false,
        labels: {
            formatter: function (value) {
                return value
            }
        }
    },
}
</script>

<template>
    <div class="w-full bg-white rounded-lg shadow dark:bg-gray-800 p-4 md:p-6">
        <div class="flex justify-between">
            <div>
                <h5 class="leading-none text-xl font-bold text-gray-800 dark:text-white pb-2">P {{ totalValue }}</h5>
                <p class="text-base font-normal text-gray-500 dark:text-gray-400">Sales per month</p>
            </div>
        </div>
        <div id="data-series-chart"></div>
    </div>
</template>
