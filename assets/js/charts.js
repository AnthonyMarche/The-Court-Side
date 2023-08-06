import Chart from 'chart.js/auto';

// Doughnut Chart
const likedCategoriesCanvas = document.getElementById('likedCategories');
const mostLikedCategories = JSON.parse(likedCategoriesCanvas.getAttribute('data-most-liked-categories'));

new Chart(likedCategoriesCanvas, {
    type: 'doughnut',
    data: {
        labels: Object.keys(mostLikedCategories),
        datasets: [{
            data: Object.values(mostLikedCategories),
            backgroundColor: ['red', 'orange', 'blue', 'yellow', 'green'],
            borderColor: 'transparent',
            hoverOffset: 4
        }]
    },
    options: {
        plugins: {
            legend: {
                labels: {
                    color: 'white'
                }
            }
        }
    }
});

// Function to create Line Chart
function createLineChart(canvasId, chartData, title, color) {
    const xAxe = Object.keys(chartData);
    const yAxe = Object.values(chartData);
    let maxYAxe = Math.max(...yAxe);
    maxYAxe = Math.ceil(maxYAxe / 10) * 10;
    new Chart(canvasId, {
        type: 'line',
        data: {
            labels: xAxe,
            datasets: [{
                label: title,
                data: yAxe,
                backgroundColor: color,
                borderColor: color,
            }]
        },
        options: {
            scales: {
                x: {
                    grid: {
                        color: 'rgb(105, 105, 105)'
                    },
                    ticks: {
                        color: 'rgb(150, 150, 150)'
                    }
                },
                y: {
                    suggestedMin: 0,
                    suggestedMax: maxYAxe,
                    grid: {
                        color: 'rgb(105, 105, 105)'
                    },
                    ticks: {
                        color: 'rgb(150, 150, 150)'
                    }
                },
            },
            plugins: {
                legend: {
                    labels: {
                        color: 'white'
                    }
                }
            }
        }
    });
}

// Creating Line Charts for "registered user" and "like"
const registeredUserCanvas = document.getElementById('registeredUser');
const registeredUserData = JSON.parse(registeredUserCanvas.getAttribute('data-registered-user-by-month'));
const registeredUserTitle = registeredUserCanvas.getAttribute('data-title');
const registeredUserColor = 'rgb(255, 99, 132)';
createLineChart(registeredUserCanvas, registeredUserData, registeredUserTitle, registeredUserColor);

const likeCanvas = document.getElementById('like');
const likeData = JSON.parse(likeCanvas.getAttribute('data-like-by-month'));
const likeColor = 'rgb(45, 191, 178)';
const likeTitle = likeCanvas.getAttribute('data-title');
createLineChart(likeCanvas, likeData, likeTitle, likeColor);
