function createStatusChart(pendingCount, approvedCount, rejectedCount) {
    if (!pendingCount && !approvedCount && !rejectedCount) {
        console.error("Invalid chart data");
        return;
    }

    var ctx = document.getElementById('statusChart').getContext('2d');
    var statusChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Pending', 'Approved', 'Rejected'],
            datasets: [{
                label: 'Application Status',
                data: [pendingCount, approvedCount, rejectedCount],
                backgroundColor: ['#1e90ff', '#28a745', '#dc3545'],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            let total = pendingCount + approvedCount + rejectedCount;
                            let count = tooltipItem.raw;
                            let percent = (count / total * 100).toFixed(2);
                            return `${tooltipItem.label}: ${count} (${percent}%)`;
                        }
                    }
                }
            }
        }
    });
}
