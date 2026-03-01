<script setup>
import ApexCharts from 'apexcharts'
import { onMounted } from 'vue'
import { formatNumber } from '@/helpers'

const props = defineProps({
    chartData: Array,
    label: String,
    valueKey: String,
    labelKey: String,
})

onMounted(() => {
    if (document.getElementById("donut-chart") && typeof ApexCharts !== 'undefined') {
        const chart = new ApexCharts(document.getElementById("donut-chart"), getChartOptions())
        chart.render()
    }
})

const getChartOptions = () => {
    const seriesData = props.chartData.map(item => item[props.valueKey])
    const labelsData = props.chartData.map(item => item[props.labelKey])

    return {
        series: seriesData,
        colors: [
            "#1C64F2", "#16BDCA", "#9061F9", "#FACC15", "#F43F5E", "#0EA5E9",
            "#8B5CF6", "#10B981", "#F59E0B", "#EF4444", "#3B82F6", "#9333EA",
            "#14B8A6", "#D97706", "#6B7280", "#6366F1", "#4ADE80"
        ],
        chart: {
            height: 320,
            width: "100%",
            type: "donut",
        },
        stroke: {
            colors: ["transparent"],
            lineCap: "",
        },
        plotOptions: {
            pie: {
                donut: {
                    labels: {
                        show: true,
                        name: {
                            show: true,
                            fontFamily: "Inter, sans-serif",
                            offsetY: 20,
                        },
                        total: {
                            showAlways: true,
                            show: true,
                            label: props.label,
                            fontFamily: "Inter, sans-serif",
                            formatter: function (w) {
                                const sum = w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                return formatNumber(sum)
                            },
                        },
                        value: {
                            show: true,
                            fontFamily: "Inter, sans-serif",
                            offsetY: -20,
                            formatter: function (value) {
                                return formatNumber(value)
                            },
                        },
                    },
                    size: "80%",
                },
            },
        },
        grid: {
            padding: {
                top: -2,
            },
        },
        labels: labelsData,
        dataLabels: {
            enabled: false,
        },
        legend: {
            position: "bottom",
            fontFamily: "Inter, sans-serif",
        },
        yaxis: {
            labels: {
                formatter: function (value) {
                    return formatNumber(value)
                },
            },
        },
        xaxis: {
            labels: {
                formatter: function (value) {
                    return formatNumber(value)
                },
                axisTicks: {
                    show: false,
                },
                axisBorder: {
                    show: false,
                },
            },
        },
    }
}
</script>

<template>
    <div class="">
        <div class="bg-white rounded-lg shadow dark:bg-gray-800 p-4 md:p-6">
            <div class="flex justify-between mb-3">
                <div class="flex justify-center items-center">
                    <h5 class="text-xl font-bold leading-none text-gray-800 dark:text-white pe-1">{{ label }}</h5>
                </div>
            </div>

            <!-- Donut Chart -->
            <div class="py-6" id="donut-chart"></div>
        </div>
    </div>
</template>
