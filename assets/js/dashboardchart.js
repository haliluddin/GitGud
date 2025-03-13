const labels = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

const salesData = [12000, 15000, 18000, 20000, 22000, 25000, 27000, 29000, 30000, 32000, 35000, 37000];
const visitsData = [200, 300, 250, 400, 500, 450, 600, 550, 650, 700, 750, 800];
const ordersData = [80, 120, 100, 150, 200, 180, 220, 210, 240, 270, 300, 320];

// Colors
const mainColor = '#CD5C08';

// Line Graph for Sales
new Chart(document.getElementById('salesChart').getContext('2d'), {
  type: 'line',
  data: {
    labels: labels,
    datasets: [{
      label: 'Sales per Month (₱)',
      data: salesData,
      borderColor: mainColor,
      backgroundColor: 'rgba(205, 92, 8, 0.2)',
      fill: true,
      tension: 0.4
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: {
        display: true,
        position: 'top',
      }
    },
    scales: {
      y: {
        beginAtZero: true
      }
    }
  }
});

// Bar Graph for Visits
new Chart(document.getElementById('visitsChart').getContext('2d'), {
  type: 'bar',
  data: {
    labels: labels,
    datasets: [{
      label: 'Food Park Visits per Month',
      data: visitsData,
      backgroundColor: mainColor,
      borderColor: mainColor,
      borderWidth: 1
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: {
        display: true,
        position: 'top',
      }
    },
    scales: {
      y: {
        beginAtZero: true
      }
    }
  }
});

// Area Graph for Orders
new Chart(document.getElementById('ordersChart').getContext('2d'), {
  type: 'line',
  data: {
    labels: labels,
    datasets: [{
      label: 'Total Orders per Month',
      data: ordersData,
      borderColor: mainColor,
      backgroundColor: 'rgba(205, 92, 8, 0.2)',
      fill: true,
      tension: 0.4
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: {
        display: true,
        position: 'top',
      }
    },
    scales: {
      y: {
        beginAtZero: true
      }
    }
  }
});