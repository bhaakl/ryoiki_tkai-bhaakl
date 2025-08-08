<template>
  <div
    class="flex items-center w-full h-full p-6 overflow-auto bg-white md-bar:justify-center rounded-xl"
  >
    <div
      class="flex items-center md-bar:justify-center w-[570px] h-[280px] lg:w-full lg:h-full"
    >
      <canvas
        ref="barChart"
        width="570"
        height="280"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, onBeforeUnmount } from 'vue'
import Chart from 'chart.js/auto'

const props = defineProps({
  data: {
    type: Object,
    required: true
  }
})

const getBackgroundColor = (value) => {
  const percentage = (value / 100) * 100
  if (percentage < 30) return '#00724D'
  if (percentage >= 30 && percentage < 70) return '#F37F19'

  return '#FF0032'
}

const getName = (value) => {
  const percentage = (value / 100) * 100
  if (percentage < 30) return 'Низкая'
  else if (percentage >= 30 && percentage < 70) return 'Средняя'
  else return 'Высокая'
}

const checkWindow = () => {
  return window.innerWidth > 1164
}

const chartInstance = ref(null)
const barChart = ref(null)

const options = ref({
  responsive: !!checkWindow,
  maintainAspectRatio: true,
  plugins: {
    legend: {
      display: true,
      position: 'top',
      align: checkWindow ? 'start' : 'end',
      padding: 12,
      labels: {
        boxWidth: 16,
        boxHeight: 16,
        usePointStyle: false,
        font: {
          family: 'NGEN Text',
          weight: 'regular',
          size: 17
        },
        generateLabels: function() {
          const labels = ['Низкая', 'Средняя', 'Высокая']
          const colors = ['#00724D', '#F37F19', '#FF0032']

          return labels.map((label, index) => ({
            text: label,
            fillStyle: colors[index],
            strokeStyle: colors[index]
          }))
        }
      }
    }
  },
  tooltip: {
    enabled: false
  },
  hover: {
    mode: null
  },
  interaction: {
    mode: null
  },
  scales: {
    y: {
      min: 0,
      max: 100,
      ticks: {
        stepSize: 20,
        callback: function(value) {
          return value + '%'
        },
        font: {
          family: 'NGEN Text',
          weight: '400',
          size: 17
        }
      },
      beginAtZero: true
    },
    x: {
      grid: {
        display: false
      }
    }
  }
})

const config = {
  type: 'bar',
  data: {
    labels: props.data.labels,
    datasets: [
      {
        backgroundColor: props.data.values.map(getBackgroundColor),
        data: props.data.values,
        barThickness: 15
      }
    ]
  },
  options: options.value,
  plugins: [
    {
      id: 'legendMargin',
      beforeInit(chart = chartInstance.value) {
        const fitValue = chart.legend.fit

        chart.legend.fit = function() {
          fitValue.bind(chart.legend)()
          this.height += 20
        }
      }
    }
  ]
}

const createChart = () => {
  const ctx = barChart.value.getContext('2d')
  if (chartInstance.value) {
    chartInstance.value.destroy()
  }

  chartInstance.value = new Chart(ctx, config)
}

let resizeTimeout

const debouncedResize = () => {
  clearTimeout(resizeTimeout)
  resizeTimeout = setTimeout(() => {
    options.value.responsive = !!checkWindow
    options.value.align = checkWindow ? 'start' : 'end'
    createChart()
  }, 500)
}

onMounted(() => {
  createChart()

  window.addEventListener('resize', debouncedResize)
})

watch(() => props.data, createChart, { deep: true })

onBeforeUnmount(() => {
  window.removeEventListener('resize', debouncedResize)
})
</script>
