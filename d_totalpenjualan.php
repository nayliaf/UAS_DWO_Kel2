<?php
// Handle back-end logic when the request is made to fetch data
if (isset($_GET['fetch_data'])) {
    header('Content-Type: application/json');

    include 'koneksi.php'; // Pastikan koneksi.php berada di direktori yang sama


    // Query to calculate total sales per year
    $query = "
        SELECT YEAR(OrderDate) AS year, SUM(TotalDue) AS total_sales
        FROM sales_salesorderheader
        GROUP BY YEAR(OrderDate)
        ORDER BY YEAR(OrderDate);
    ";

    $result = mysqli_query($conn, $query);

    // Check query execution
    if (!$result) {
        die(json_encode(["error" => "Query execution failed: " . mysqli_error($conn)]));
    }

    // Fetch data into an array
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = [
            "year" => $row["year"],
            "total_sales" => $row["total_sales"]
        ];
    }

    // Output the data in JSON format
    echo json_encode($data);

    // Close the connection
    mysqli_close($conn);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Total Penjualan Tiap Tahun</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin-bottom: 30px;
            padding: 0;
            background-color: #f4f4f9;
            display: flex;
            justify-content: left;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            width: 80%;
            max-width: 1000px;
            margin: 20px;
            text-align: center;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .chart-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
        }

        canvas {
            width: 100% !important;
            max-width: 900px;
            height: 600px !important;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <h1>Total Penjualan Tiap Tahun</h1>
        <canvas id="salesChart" width="600" height="400"></canvas>
    </div>
    <script>
        // Fetch data from the server and render the chart
        fetch('?fetch_data=true')
            .then(response => response.json())
            .then(data => {
                // Extract labels (years) and data (total sales)
                const labels = data.map(item => item.year);
                const salesData = data.map(item => item.total_sales);

                // Configure the chart
                const ctx = document.getElementById('salesChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: '',
                            data: salesData,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: value => `$ ${value.toLocaleString()}`
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: context => `$ ${context.raw.toLocaleString()}`
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                alert('Failed to fetch sales data.');
            });
    </script>
</body>
</html>
