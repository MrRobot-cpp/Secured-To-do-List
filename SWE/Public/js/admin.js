
//PROGRESS BAR
const bar = document.querySelector(".toggle-btn");

bar.addEventListener("click", function () {
  document.querySelector("#sidebar").classList.toggle("expand");
});
document.querySelectorAll('.progress').forEach(progress => {
  const value = progress.getAttribute('data-value');
  const progressBar = document.createElement('div');
  progressBar.classList.add('progress-bar');
  progressBar.style.width = value; 
  progress.appendChild(progressBar);
});
// ---------- CHARTS ----------//

// PIE CHART
const pieChartOptions = {
  series: [45, 30, 25], 
  chart: {
    type: 'pie',
    background: 'transparent',
    height: 350,
    toolbar: {
      show: false,
    },
  },
  colors: ['#2f2f2f', '#ffcb74', '#111111'],
  labels: ['Completed', 'Pending', 'Overdue'],
  legend: {
    position: 'top',
    labels: {
      colors: '#f5f7ff',
    },
  },
  tooltip: {
    theme: 'dark',
  },
};

const pieChart = new ApexCharts(
  document.querySelector('#pie-chart'), 
  pieChartOptions
);
pieChart.render();
// LINE CHART
const lineChartTasks = {
  series: [
    {
      name: 'Tasks Completed',
      data: [10, 20, 15, 30, 25, 40, 35],
    },
  ],
  chart: {
    type: 'line',
    background: 'transparent',
    height: 350,
    toolbar: {
      show: false,
    },
  },
  colors: ['#2f2f2f'],
  dataLabels: {
    enabled: false,
  },
  stroke: {
    curve: 'smooth',
    width: 2,
  },
  xaxis: {
    categories: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6', 'Week 7'],
    labels: {
      style: {
        colors: '#ffcb74',
      },
    },
  },
  yaxis: {
    title: {
      text: 'Tasks Completed',
      style: {
        color: '#ffcb74',
      },
    },
    labels: {
      style: {
        colors: '#f5f7ff',
      },
    },
  },
  tooltip: {
    shared: true,
    intersect: false,
    theme: 'dark',
  },
};

const lineTasks = new ApexCharts(
  document.querySelector('#line-chart-tasks'), 
  lineChartTasks
);
lineTasks.render();

const lineChartOptions = {
  series: [{
      name: 'Logins',
      data: [30, 40, 35, 50, 49, 60, 70] 
  }],
  chart: {
      type: 'line',
      height: 350,
      background: 'transparent'
  },
  colors: ['#ffcb74'],
  xaxis: {
      categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
      labels: {
          style: {
              colors: '#2f2f2f'
          }
      },
      axisBorder: { color: '#2f2f2f' }
  },
  yaxis: {
      labels: {
          style: { colors: '#2f2f2f' }
      },
      title: {
          text: 'Logins',
          style: { color: '#2f2f2f' }
      }
  },
  grid: { borderColor: '#2f2f2f' }
};

const lineChart = new ApexCharts(document.querySelector('#line-chart'), lineChartOptions);
lineChart.render();

//BAR CHART(growth)
const barChartGrowthOptions = {
  series: [{
    name: 'New Users',
    data: [20, 30, 25, 40, 35, 45, 50]
  }],
  chart: {
    type: 'bar',
    height: 350,
    background: 'transparent'
  },
  colors: ['#ffcb74'],
  plotOptions: {
    bar: {
      horizontal: false,
      columnWidth: '50%',
      borderRadius: 4
    }
  },
  dataLabels: {
    enabled: false
  },
  xaxis: {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
    labels: {
      style: {
        colors: '#2f2f2f'
      }
    },
    axisBorder: { color: '#2f2f2f' }
  },
  yaxis: {
    title: {
      text: 'New Users',
      style: { color: '#2f2f2f' }
    },
    labels: {
      style: { colors: '#2f2f2f' }
    }
  },
  grid: { borderColor: '#2f2f2f' },
  legend: {
    labels: {
      colors: '#2f2f2f'
    },
    show: true,
    position: 'top'
  },
  tooltip: {
    shared: true,
    intersect: false,
    theme: 'dark'
  }
};

const barChartGrowth = new ApexCharts(document.querySelector('#bar-chart-growth'), barChartGrowthOptions);
barChartGrowth.render();



//BAR CHART(top users)
const barChartTopUsersOptions = {
  series: [{
      name: 'Tasks Created',
      data: [15, 25, 35, 30, 20]
  }],
  chart: {
      type: 'bar',
      height: 350,
      background: 'transparent'
  },
  colors: ['#2f2f2f', '#ffcb74'],
  plotOptions: {
      bar: {
          distributed: true,
          borderRadius: 4
      }
  },
  xaxis: {
      categories: ['User 1', 'User 2', 'User 3', 'User 4', 'User 5'],
      labels: {
          style: {
              colors: '#2f2f2f'
          }
      },
      axisBorder: { color: '#2f2f2f' }
  },
  yaxis: {
      title: {
          text: 'Tasks',
          style: { color: '#2f2f2f' }
      },
      labels: {
          style: { colors: '#2f2f2f' }
      }
  },
  grid: { borderColor: '#2f2f2f' }
};

const barChartTopUsers = new ApexCharts(document.querySelector('#bar-chart-top-users'), barChartTopUsersOptions);
barChartTopUsers.render();
//USER STATUS
document.getElementById('user-status-filter').addEventListener('change', function() {
  const selectedStatus = this.value;
  const rows = document.querySelectorAll('tbody tr');
  
  rows.forEach(row => {
    if (selectedStatus === 'all' || row.getAttribute('data-status') === selectedStatus) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
});
